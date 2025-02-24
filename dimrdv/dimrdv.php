<?php

/**
 * This file is part of the dimrdv project.
 * 
 * (c) 2025 iepiep <r.minini@solution61.fr>
 * 
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class Dimrdv extends Module {

    public function __construct() {
        $this->name = 'dimrdv';
        $this->tab = 'shipping_logistics';
        $this->version = '1.0.0';
        $this->author = 'Roberto Minini';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->controllers = array('dimform');
        $this->displayName = $this->l('DIM-RDV');
        $this->description = $this->l('Appointment management module with optimized itinerary.');
        $this->ps_versions_compliancy = array('min' => '8.2.0', 'max' => _PS_VERSION_);

        parent::__construct();
    }

    public function install() {
        if (!parent::install() ||
                !$this->installSql() ||
                !$this->registerHook('displayHome') ||
                !$this->registerHook('header') ||
                !$this->registerHook('displayBackOfficeHeader') ||
                !$this->installTabs()
        ) {
            return false;
        }
        return true;
    }

    private function installTabs() {
        // Onglet principal dans la barre latérale
        $parentTab = new Tab();
        $parentTab->active = 1;
        $parentTab->class_name = 'AdminDimrdvGestionRdv';
        $parentTab->id_parent = 0; // 0 = Menu principal
        $parentTab->module = $this->name;
        $parentTab->icon = 'directions'; // Icône pour "Itinéraire"
        foreach (Language::getLanguages(true) as $lang) {
            $parentTab->name[$lang['id_lang']] = 'DIM RDV';
        }
        $parentTab->add();

        // Onglet de configuration sous "Configuration"
        $configTab = new Tab();
        $configTab->active = 1;
        $configTab->class_name = 'AdminDimrdvConfig';
        $configTab->id_parent = (int) Tab::getIdFromClassName('AdminDimrdvGestionRdv'); // Parent = Gestion RDV
        $configTab->module = $this->name;
        $configTab->icon = 'settings';
        foreach (Language::getLanguages(true) as $lang) {
            $configTab->name[$lang['id_lang']] = 'Config';
        }
        $configTab->add();

        return true;
    }

    public function uninstallTab() {
        $tabs = ['AdminDimrdvGestionRdv', 'AdminDimrdvConfig'];
        foreach ($tabs as $tabClass) {
            $id_tab = (int) Tab::getIdFromClassName($tabClass);
            if ($id_tab) {
                $tab = new Tab($id_tab);
                $tab->delete();
            }
        }
        return true;
    }

    private function installSql() {
        $sql_file = dirname(__FILE__) . '/sql/installs.sql';
        if (!file_exists($sql_file)) {
            return false;
        }
        $sql_content = file_get_contents($sql_file);
        // Remplacement du préfixe de table
        $sql_content = str_replace('PREFIX_', _DB_PREFIX_, $sql_content);
        $queries = preg_split("/;\s*[\r\n]+/", $sql_content);
        foreach ($queries as $query) {
            if (!empty(trim($query))) {
                if (Db::getInstance()->execute($query) == false) {
                    return false;
                }
            }
        }
        return true;
    }

    private function uninstallSql() {
        $sql = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'dim_rdv`';
        if (Db::getInstance()->execute($sql) == false) {
            return false;
        }
        return true;
    }

    public function uninstall() {
        if (!parent::uninstall() ||
                !$this->uninstallSql() ||
                !$this->unregisterHook('displayHome') ||
                !$this->unregisterHook('header') ||
                !$this->unregisterHook('displayBackOfficeHeader') ||
                !$this->uninstallTab()
        ) {
            return false;
        }
        return true;
    }

    // Méthode de réinitialisation : efface uniquement les données de la table
    public function resetModuleData() {
        $sql = 'TRUNCATE TABLE `' . _DB_PREFIX_ . 'dim_rdv`';
        return Db::getInstance()->execute($sql);
    }

    // Back office : page de configuration du module
    public function getContent() {
        if (Tab::getIdFromClassName('AdminDimrdvConfig')) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminDimrdvConfig'));
        } else {
            return $this->displayError($this->l('Erreur : L’onglet de configuration n’est pas installé.'));
        }
    }

    // Hook affiché en front office sur la page d’accueil
    public function hookDisplayHome($params) {
        $this->context->smarty->assign(array(
            'dimrdv_link' => $this->context->link->getModuleLink($this->name, 'dimform', array(), true),
            'module' => $this, // Assigner l'instance du module pour utiliser $module->l()
        ));
        return $this->display(__FILE__, 'views/templates/hook/displayHome.tpl');
    }

    // Ajout de CSS/JS si nécessaire
    public function hookHeader($params) {
        $this->context->controller->registerStylesheet(
            'dimrdv-css',
            $this->_path . 'views/css/front/dimrdv.css',
            ['media' => 'all', 'priority' => 150]
        );
        $this->context->controller->registerJavascript(
            'dimrdv-js',
            $this->_path . 'views/js/front/dimrdv.js',
            ['position' => 'bottom', 'priority' => 150]
        );
    }

    public function hookdisplayBackOfficeHeader($params) {
        $this->context->controller->registerStylesheet(
            'dimrdv-css',
            $this->_path . 'views/css/back/dimrdv.css',
            ['media' => 'all', 'priority' => 150]
        );
        $this->context->controller->registerJavascript(
            'dimrdv-js',
            $this->_path . 'views/js/back/dimrdv.js',
            ['position' => 'bottom', 'priority' => 150]
        );
    }
}
