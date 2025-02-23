<?php

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