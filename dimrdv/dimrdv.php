<?php

/**
 * @author Roberto Minini <r.minini@solution61.fr>
 * @copyright 2025 Roberto Minini
 * @license MIT
 *
 * This file is part of the dimrdv project.
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class Dimrdv extends Module
{
    public function __construct()
    {
        $this->name = 'dimrdv';
        $this->tab = 'shipping_logistics';
        $this->version = '1.0.0';
        $this->author = 'Roberto Minini';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->controllers = ['dimform'];
        $this->displayName = $this->l('DIM-RDV');
        $this->description = $this->l('Appointment management module with optimized itinerary.');
        $this->ps_versions_compliancy = ['min' => '8.2.0', 'max' => _PS_VERSION_];

        parent::__construct();
    }

    public function isUsingNewTranslationSystem()
    {
        return false;
    }

    public function install()
    {
        return parent::install()
            && $this->installSql()
            && $this->registerHook('displayHome')
            && $this->registerHook('actionFrontControllerSetMedia')
            && $this->registerHook('displayBackOfficeHeader')
            && $this->installTabs();
    }

    private function installTabs()
    {
        $parentTab = new Tab();
        $parentTab->active = 1;
        $parentTab->class_name = 'AdminDimrdvGestionRdv';
        $parentTab->id_parent = 0;
        $parentTab->module = $this->name;
        $parentTab->icon = 'directions';

        foreach (Language::getLanguages(true) as $lang) {
            $parentTab->name[$lang['id_lang']] = 'DIM RDV';
        }
        $parentTab->add();

        $configTab = new Tab();
        $configTab->active = 1;
        $configTab->class_name = 'AdminDimrdvConfig';
        $configTab->id_parent = (int) Tab::getIdFromClassName('AdminDimrdvGestionRdv');
        $configTab->module = $this->name;
        $configTab->icon = 'settings';

        foreach (Language::getLanguages(true) as $lang) {
            $configTab->name[$lang['id_lang']] = 'Config';
        }
        $configTab->add();

        return true;
    }

    public function uninstallTab()
    {
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

    private function installSql()
    {
        $sql_file = dirname(__FILE__) . '/sql/installs.sql';
        if (!file_exists($sql_file)) {
            return false;
        }
        
        $sql_content = str_replace('PREFIX_', _DB_PREFIX_, file_get_contents($sql_file));
        $queries = preg_split("/;\s*[\r\n]+/", $sql_content);
        
        foreach ($queries as $query) {
            if (!empty(trim($query)) && Db::getInstance()->execute($query) == false) {
                return false;
            }
        }
        return true;
    }

    private function uninstallSql()
    {
        return Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'dim_rdv`');
    }

    public function uninstall()
    {
        return parent::uninstall()
            && $this->uninstallSql()
            && $this->unregisterHook('displayHome')
            && $this->unregisterHook('actionFrontControllerSetMedia')
            && $this->unregisterHook('displayBackOfficeHeader')
            && $this->uninstallTab();
    }

    public function resetModuleData()
    {
        return Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'dim_rdv`');
    }

    public function getContent()
    {
        if (Tab::getIdFromClassName('AdminDimrdvConfig')) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminDimrdvConfig'));
        }
        return $this->displayError($this->l('Erreur : L’onglet de configuration n’est pas installé.'));
    }

    public function hookDisplayHome($params)
    {
        $this->context->smarty->assign([
            'dimrdv_link' => $this->context->link->getModuleLink($this->name, 'dimform', [], true),
            'module' => $this,
        ]);
        return $this->display(__FILE__, 'views/templates/hook/displayHome.tpl');
    }

    public function hookActionFrontControllerSetMedia($params)
    {
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

    public function hookDisplayBackOfficeHeader($params)
    {
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
