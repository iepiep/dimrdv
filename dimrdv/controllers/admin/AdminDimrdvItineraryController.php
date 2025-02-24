<?php

/*
 * @author Roberto Minini <r.minini@solution61.fr>
 * @copyright 2025 Roberto Minini
 * @license MIT
 * This file is part of the dimrdv project.
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminDimrdvItineraryController extends ModuleAdminController {

    public $ssl = true;
    // Propriétés pour stocker la matrice des durées et les indices optimisés
    private $durationMatrix = [];
    private $optimizedRouteIndices = [];

    private function getGoogleApiKey() {
        $apiKey = Configuration::get('DIMRDV_GOOGLE_API_KEY');
        return !empty($apiKey) ? $apiKey : '';
    }

    public function initContent() {
        parent::initContent();

        // Assignation des variables de traduction
        $this->context->smarty->assign([
            'itinOptimise' => $this->module->l('Itinéraire optimisé'),
            'resumeItineraire' => $this->module->l('Résumé de l\'itinéraire'),
            'errorMessage' => $this->module->l('Erreur lors de la récupération de l\'itinéraire'),
        ]);

        // Récupération des IDs de RDVs sélectionnés depuis Configuration
        $selected = json_decode(Configuration::get('DIMFORMMODULE_DATA'), true);

        // Vérification de la validité des données
        if (empty($selected) || !is_array($selected)) {
            $this->errors[] = $this->module->l($selected ? 'Données de rendez-vous invalides.' : 'Aucun rendez-vous sélectionné.');
            return;
        }


        // Calcul de l'itinéraire optimisé
        $orderedRoute = $this->calculateOptimizedRoute($selected);
        if (empty($orderedRoute)) {
            $this->errors[] = $this->module->l('Impossible de calculer l\'itinéraire optimisé.');
            $this->context->smarty->assign([
                'optimized_route' => [],
                'itinerary_schedule' => [],
                'google_maps_api_key' => $this->getGoogleApiKey(),
                'module' => $this->module,
            ]);
            $this->setTemplate('module:dimrdv/views/templates/front/itinerary.tpl');
            return;
        }

        // Calcul de la planification horaire en se basant sur les temps réels de déplacement
        $itinerarySchedule = $this->scheduleItinerary($orderedRoute);

        $this->context->smarty->assign([
            'optimized_route' => $orderedRoute,
            'itinerary_schedule' => $itinerarySchedule,
            'start_address' => '25 rue de la Noé Pierre, 53960 Bonchamp-lès-Laval, France',
            'google_maps_api_key' => $this->getGoogleApiKey(),
            'module' => $this->module,
        ]);

        $this->setTemplate('module:dimrdv/views/templates/front/itinerary.tpl');
    }

    /**
     * Calcule l'itinéraire optimisé et stocke la matrice des durées.
     *
     * @param array $selectedIds
     * @return array Tableau ordonné des arrêts (chaque arrêt est un tableau associatif).
     */
    private function calculateOptimizedRoute($selectedIds) {
        $baseLocation = '25 rue de la Noé Pierre, 53960 Bonchamp-lès-Laval, France';

        // Récupération des données des clients depuis la table dim_rdv
        $selectedIds = array_map('intval', $selectedIds);
        $placeholders = implode(',', array_fill(0, count($selectedIds), '?'));
        $sql = 'SELECT firstname, lastname, address, postal_code, city 
        FROM `' . _DB_PREFIX_ . 'dim_rdv` 
        WHERE id_dim_rdv IN (' . $placeholders . ')';
        $results = Db::getInstance()->executeS($sql, array_values($selectedIds));

        $clients = [];
        foreach ($results as $row) {
            // Construction de l'adresse complète
            $row['full_address'] = $row['address'] . ', ' . $row['postal_code'] . ' ' . $row['city'] . ', France';
            $clients[] = $row;
        }
        if (empty($clients)) {
            return [];
        }

        // Tableau des localisations (le premier élément est le point de départ)
        $locations = [];
        $locations[] = $baseLocation;
        foreach ($clients as $client) {
            $locations[] = $client['full_address'];
        }

        // Appel à l'API Google Distance Matrix pour récupérer distances et durées
        $apiKey = $this->getGoogleApiKey();
        $origins = implode('|', array_map('urlencode', $locations));
        $destinations = $origins;
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=$origins&destinations=$destinations&key=$apiKey";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($response === false || $httpCode !== 200) {
            $this->errors[] = $this->module->l('Erreur lors de la récupération des distances.');
            return [];
        }
        curl_close($ch);

        $data = json_decode($response, true);

        if (!isset($data['status']) || $data['status'] !== 'OK') {
            PrestaShopLogger::addLog('Erreur API Google : ' . json_encode($data), 3);
            $this->errors[] = $this->module->l('Problème avec l\'API Google : ') . ($data['status'] ?? 'Réponse vide');
            return [];
        }

        // Vérification que 'rows' existe et contient bien des données valides
        if (!isset($data['rows']) || !is_array($data['rows']) || empty($data['rows'])) {
            PrestaShopLogger::addLog('Réponse invalide de l\'API Google Distance Matrix : ' . json_encode($data), 3);
            $this->errors[] = $this->module->l('Réponse invalide de l\'API Google.');
            return [];
        }

        // Vérification que chaque élément 'elements' est bien défini dans chaque ligne
        foreach ($data['rows'] as $row) {
            if (!isset($row['elements']) || !is_array($row['elements']) || empty($row['elements'])) {
                PrestaShopLogger::addLog('Données "elements" manquantes dans une ligne de la réponse API Google.', 3);
                $this->errors[] = $this->module->l('Problème avec les données renvoyées par Google.');
                return [];
            }
        }

        // Extraction de la matrice des distances et des durées
        $distanceMatrix = [];
        $durationMatrix = [];
        foreach ($data['rows'] as $i => $row) {
            foreach ($row['elements'] as $j => $element) {
                $distanceMatrix[$i][$j] = (isset($element['distance']) && $element['status'] === 'OK') ? $element['distance']['value'] : PHP_INT_MAX;
                $durationMatrix[$i][$j] = (isset($element['duration']) && $element['status'] === 'OK') ? $element['duration']['value'] : PHP_INT_MAX;
            }
        }
        // Stocker la matrice des durées dans une propriété pour utilisation ultérieure
        $this->durationMatrix = $durationMatrix;

        // Calcul du chemin optimisé (basé sur la distance)
        $optimizedRouteIndices = $this->solveTSP($distanceMatrix);
        // Stocker les indices optimisés pour la planification
        $this->optimizedRouteIndices = $optimizedRouteIndices;

        // Construction du tableau ordonné des arrêts en convertissant les indices
        $orderedRoute = [];
        foreach ($optimizedRouteIndices as $index) {
            if ($index == 0) {
                $orderedRoute[] = [
                    'is_base' => true,
                    'full_address' => $baseLocation,
                ];
            } else {
                // L'indice client correspond à index - 1 dans le tableau $clients
                $client = $clients[$index - 1];
                $client['is_base'] = false;
                $orderedRoute[] = $client;
            }
        }
        return $orderedRoute;
    }

    /**
     * Algorithme TSP : plus proche voisin puis optimisation 2‑opt.
     *
     * @param array $distanceMatrix
     * @return array Ordre optimal des indices.
     */
    private function solveTSP($distanceMatrix) {
        $numLocations = count($distanceMatrix);
        $unvisited = range(1, $numLocations - 1); // Exclut le point de départ
        $route = [0]; // Commence à la base
        $current = 0;

        while (!empty($unvisited)) {
            $nearest = null;
            $minDistance = PHP_INT_MAX;
            foreach ($unvisited as $i) {
                if ($distanceMatrix[$current][$i] < $minDistance) {
                    $minDistance = $distanceMatrix[$current][$i];
                    $nearest = $i;
                }
            }
            $route[] = $nearest;
            $current = $nearest;
            $unvisited = array_values(array_diff($unvisited, [$nearest]));
        }
        // Retour à la base
        $route[] = 0;
        return $this->optimizeRoute2Opt($route, $distanceMatrix);
    }

    private function optimizeRoute2Opt($route, $distanceMatrix) {
        $improved = true;
        $numLocations = count($route);

        while ($improved) {
            $improved = false;
            for ($i = 1; $i < $numLocations - 2; $i++) {
                for ($j = $i + 1; $j < $numLocations - 1; $j++) {
                    $newRoute = $this->swapTwoOpt($route, $i, $j);
                    if ($this->calculateTotalDistance($newRoute, $distanceMatrix) < $this->calculateTotalDistance($route, $distanceMatrix)) {
                        $route = $newRoute;
                        $improved = true;
                    }
                }
            }
        }
        return $route;
    }

    private function swapTwoOpt($route, $i, $j) {
        return array_merge(
                array_slice($route, 0, $i),
                array_reverse(array_slice($route, $i, $j - $i + 1)),
                array_slice($route, $j + 1)
        );
    }

    private function calculateTotalDistance($route, $distanceMatrix) {
        $totalDistance = 0;
        for ($i = 0; $i < count($route) - 1; $i++) {
            $totalDistance += $distanceMatrix[$route[$i]][$route[$i + 1]];
        }
        return $totalDistance;
    }

    /**
     * Planifie les horaires en utilisant les temps réels de déplacement.
     * Pour chaque trajet, le temps de déplacement (en secondes) est récupéré depuis la matrice des durées,
     * puis ajouté au temps courant. On ajoute également une durée d'intervention de 2 heures et,
     * si nécessaire, une pause déjeuner d'1h.
     *
     * @param array $orderedRoute Tableau ordonné des arrêts (déjà construit dans calculateOptimizedRoute)
     * @return array Synthèse de l’itinéraire avec horaires et informations clients.
     */
    private function scheduleItinerary($orderedRoute) {
        // Récupérer les indices optimisés calculés précédemment
        $routeIndices = $this->optimizedRouteIndices;
        $currentTime = new DateTime('08:30');
        $lunchTaken = false;
        $schedule = [];
        $numLegs = count($routeIndices);

        // On planifie pour chaque RDV (les indices 1 à numLegs-2 correspondent aux RDVs, en excluant le départ (0) et le retour)
        for ($i = 1; $i < $numLegs - 1; $i++) {
            // Calcul du temps de déplacement entre l'arrêt précédent et l'arrêt courant
            $prevIndex = $routeIndices[$i - 1];
            $currIndex = $routeIndices[$i];
            $travelSeconds = isset($this->durationMatrix[$prevIndex][$currIndex]) ? $this->durationMatrix[$prevIndex][$currIndex] : 0;
            // Ajouter le temps de déplacement réel
            $currentTime->modify("+{$travelSeconds} seconds");

            // Si le déjeuner n'a pas encore été pris et que l'heure est >= 12:00, ajouter une pause d'1h
            if (!$lunchTaken && $currentTime->format('H') >= 12) {
                $currentTime->modify('+1 hour');
                $lunchTaken = true;
            }
            $appointmentTime = clone $currentTime;
            // Utiliser l'arrêt courant depuis le tableau ordonné
            $stop = $orderedRoute[$i];
            $schedule[] = [
                'time' => $appointmentTime->format('H:i'),
                'lastname' => $stop['lastname'],
                'firstname' => $stop['firstname'],
                'address' => $stop['full_address'],
            ];
            // Ajouter la durée d'intervention de 2 heures (7200 secondes)
            $currentTime->modify('+7200 seconds');
        }
        return $schedule;
    }
}
