<?php

/**
 * This file is part of the dimrdv project.
 * 
 * (c) 2025 iepiep <r.minini@solution61.fr>
 * 
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

if (!defined('_PS_VERSION_')) { exit; }

class DimrdvDimformModuleFrontController extends ModuleFrontController {

    public $ssl = true;

    public function initContent() {
        parent::initContent();

        if (Tools::isSubmit('submit_dimrdv')) {
            $this->processForm();
        }

        $this->context->smarty->assign([
            'module' => $this->module, // Ajout de l'instance du module pour le traduire
            'module_dir' => $this->module->getPathUri(),
            'gdpr_link' => $this->context->link->getPageLink('gdpr'),
            'date_options' => $this->getDateOptions(),
        ]);
        $this->setTemplate('module:dimrdv/views/templates/front/dimrdv.tpl');
    }

    protected function processForm() {
        $lastname = Tools::getValue('lastname');
        $firstname = Tools::getValue('firstname');
        $address = Tools::getValue('address');
        $postal_code = Tools::getValue('postal_code');
        $city = Tools::getValue('city');
        $phone = Tools::getValue('phone');
        $email = Tools::getValue('email');
        $date_creneau1 = Tools::getValue('date_creneau1');
        $date_creneau2 = Tools::getValue('date_creneau2');
        $gdpr_consent = Tools::getValue('gdpr_consent');

        // Validation des champs obligatoires et du consentement RGPD
        if (empty($lastname) || empty($firstname) || empty($address) || empty($postal_code) || empty($city) || empty($phone) || empty($email) || empty($date_creneau1) || empty($date_creneau2) || !$gdpr_consent) {
            $this->errors[] = $this->module->l('Tous les champs sont obligatoires et le consentement RGPD doit être accepté.');
            return;
        }

        if (!Validate::isEmail($email)) {
            $this->errors[] = $this->module->l('Email invalide.');
            return;
        }

        $data = array(
            'lastname' => pSQL($lastname),
            'firstname' => pSQL($firstname),
            'address' => pSQL($address),
            'postal_code' => pSQL($postal_code),
            'city' => pSQL($city),
            'phone' => pSQL($phone),
            'email' => pSQL($email),
            'date_creneau1' => pSQL($date_creneau1),
            'date_creneau2' => pSQL($date_creneau2),
            'visited' => 0,
            'created_at' => date('Y-m-d H:i:s'),
        );

        if (Db::getInstance()->insert('dim_rdv', $data)) {
            Tools::redirect($this->context->link->getPageLink('index', true, null, 'conf=1'));
        } else {
            $this->errors[] = $this->module->l('Une erreur est survenue lors de l’enregistrement du rendez-vous.');
        }
    }

    // Génère les options de créneaux pour les deux semaines à venir (hors week-end)
    protected function getDateOptions() {
        $options = array();
        $start = new DateTime();
        $end = (new DateTime())->modify('+14 days');

        while ($start <= $end) {
            // Exclure samedi (6) et dimanche (7)
            if (!in_array($start->format('N'), array(6, 7))) {
                $dateStr = $start->format('l d/m/y');
                $options[] = array('value' => $dateStr . ' MATIN', 'label' => $dateStr . ' MATIN');
                $options[] = array('value' => $dateStr . ' APRES-MIDI', 'label' => $dateStr . ' APRES-MIDI');
            }
            $start->modify('+1 day');
        }
        return $options;
    }

}
