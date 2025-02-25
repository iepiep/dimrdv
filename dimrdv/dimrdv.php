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

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Translation\TranslatorInterface;

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
        // $this->controllers = ['dimform']; // Remove this line
        $this->displayName = $this->l('DIM-RDV');
        $this->description = $this->l('Appointment management module with optimized itinerary.');
        $this->ps_versions_compliancy = ['min' => '8.2.0', 'max' => _PS_VERSION_];

        parent::__construct();
    }

    public function isUsingNewTranslationSystem(): bool
    {
        return true;
    }

    public function install(): bool
    {
        if (!parent::install()
            || !$this->installSql()
            || !$this->registerHook('displayHome')
            || !$this->registerHook('actionFrontControllerSetMedia')
            || !$this->registerHook('actionAdminControllerSetMedia')
            || !$this->installTabs()
        ) {
            return false;
        }

        return true;
    }

    private function installTabs(): bool
    {
        $parentTab = new Tab();
        $parentTab->active = 1;
        $parentTab->class_name = 'AdminDimrdvGestionRdv';
        $parentTab->id_parent = 0;
        $parentTab->module = $this->name;
        $parentTab->icon = 'directions';

        $languages = Language::getLanguages(true);
        foreach ($languages as $lang) {
            $parentTab->name[$lang['id_lang']] = $this->l('DIM RDV', $lang['locale']);
        }
        return $parentTab->add();
    }

    public function uninstallTab(): bool
    {
        $tabs = ['AdminDimrdvGestionRdv', 'AdminDimrdvConfig', 'AdminDimrdvItinerary'];
        foreach ($tabs as $tabClass) {
            $id_tab = (int) Tab::getIdFromClassName($tabClass);
            if ($id_tab) {
                $tab = new Tab($id_tab);
                if (!$tab->delete()) {
                    return false;
                }
            }
        }

        return true;
    }

    private function installSql(): bool
    {
        $sql_file = dirname(__FILE__) . '/sql/installs.sql';
        if (!file_exists($sql_file)) {
            return false;
        }

        $sql_content = file_get_contents($sql_file);
        $sql_content = str_replace('PREFIX_', _DB_PREFIX_, $sql_content);
        $queries = preg_split("/;\s*[\r\n]+/", $sql_content);

        foreach ($queries as $query) {
            if (!empty(trim($query))) {
                try {
                    if (!Db::getInstance()->execute($query)) {
                        return false;
                    }
                } catch (Exception $e) {
                    PrestaShopLogger::addLog("SQL Error: " . $e->getMessage(), 3);
                    return false;
                }

            }
        }

        return true;
    }

    private function uninstallSql(): bool
    {
        $sql = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'dim_rdv`';
        try {
            return Db::getInstance()->execute($sql);
        } catch (Exception $e) {
            PrestaShopLogger::addLog("SQL Uninstall Error: " . $e->getMessage(), 3);
            return false;
        }
    }

    public function uninstall(): bool
    {
        if (!parent::uninstall()
            || !$this->uninstallSql()
            || !$this->unregisterHook('displayHome')
            || !$this->unregisterHook('actionFrontControllerSetMedia')
            || !$this->unregisterHook('actionAdminControllerSetMedia')
            || !$this->uninstallTab()
        ) {
            return false;
        }

        return true;
    }

    public function resetModuleData(): bool
    {
        $sql = 'TRUNCATE TABLE `' . _DB_PREFIX_ . 'dim_rdv`';
        try {
            return Db::getInstance()->execute($sql);
        } catch (Exception $e) {
            PrestaShopLogger::addLog("SQL Reset Error: " . $e->getMessage(), 3);
            return false;
        }
    }

    public function getContent(): string
    {
        $url = $this->context->link->getAdminLink('AdminDimrdvConfig');
        Tools::redirectAdmin($url);
        return '';
    }

    public function hookDisplayHome(array $params): string
    {
        $this->context->smarty->assign([
            'dimrdv_link' => $this->context->link->getModuleLink($this->name, 'dimform'),
            'module' => $this,
        ]);

        return $this->display(__FILE__, 'views/templates/hook/displayHome.tpl');
    }

    public function hookActionFrontControllerSetMedia(array $params): void
    {
        $this->context->controller->addCSS($this->_path . 'views/css/front/dimrdv.css', 'all');
        $this->context->controller->addJS($this->_path . 'views/js/front/dimrdv.js');
    }

    public function hookActionAdminControllerSetMedia(array $params): void
    {
        $this->context->controller->addCSS($this->_path . 'views/css/back/dimrdv.css', 'all');
        $this->context->controller->addJS($this->_path . 'views/js/back/dimrdv.js');
    }

    public function getPathUri(): string
    {
        return $this->_path;
    }
}
