<?php
/**
 * This file is part of the dimrdv project.
 *
 * (c) 2025 iepiep <r.minini@solution61.fr>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 *
 * @author Roberto Minini <r.minini@solution61.fr>
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminDimrdvGestionRdvController extends ModuleAdminController {

    public function __construct() {
        $this->bootstrap = true; // Pour utiliser le style Bootstrap
        parent::__construct();
    }

    public function initContent() {
        parent::initContent();

        // Récupérer la liste des RDV depuis la table
        $rdvs = Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'dim_rdv` ORDER BY date_creneau1 ASC') ?: [];

        $this->context->smarty->assign([
            'rdvs' => $rdvs,
        ]);

        // Vérifie le chemin d'accès du template
        $templatePath = _PS_MODULE_DIR_ . 'dimrdv/views/templates/admin/gestionRdv.tpl';
        if (!file_exists($templatePath)) {
            die("Le template '$templatePath' est introuvable.");
        }

        $this->setTemplate('module:dimrdv/views/templates/admin/gestionRdv.tpl');
    }

}
