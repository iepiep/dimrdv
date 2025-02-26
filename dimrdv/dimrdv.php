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

use Db;
use Exception;
use Language;
use PrestaShop\PrestaShop\Core\Module\Install\ModuleInstaller;
use PrestaShopLogger;
use Tab;
use Validate;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Dimrdv extends Module
{
    private $templateFile;

    public function __construct()
    {
        $this->name = 'dimrdv';
        $this->tab = 'shipping_logistics';
        $this->version = '1.0.0';
        $this->author = 'Roberto Minini';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->displayName = $this->l('DIM-RDV');
        $this->description = $this->l('Appointment management module with optimized itinerary.');
        $this->ps_versions_compliancy = ['min' => '8.2.0', 'max' => _PS_VERSION_];

        parent::__construct();

        $this->templateFile = 'module:' . $this->name . '/views/templates/hook/displayHome.tpl';
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

        if (!$parentTab->add()) {
            return false;
        }

        // Save the parent tab ID to configuration
        Configuration::updateValue('DIMRDV_PARENT_TAB_ID', (int) $parentTab->id);

        // Create subtab for configuration
        $configTab = new Tab();
        $configTab->active = 1;
        $configTab->class_name = 'AdminDimrdvConfig';
        $configTab->id_parent = (int) $parentTab->id; // Parent is the main module tab
        $configTab->module = $this->name;
        foreach ($languages as $lang) {
            $configTab->name[$lang['id_lang']] = $this->l('Configuration', $lang['locale']); // Translation key
        }
        if (!$configTab->add()) {
            return false;
        }

        // Create subtab for itinerary
        $itineraryTab = new Tab();
        $itineraryTab->active = 1;
        $itineraryTab->class_name = 'AdminDimrdvItinerary';
        $itineraryTab->id_parent = (int) $parentTab->id; // Parent is the main module tab
        $itineraryTab->module = $this->name;
        foreach ($languages as $lang) {
            $itineraryTab->name[$lang['id_lang']] = $this->l('Itinerary', $lang['locale']); // Translation key
        }
        if (!$itineraryTab->add()) {
            return false;
        }

        return true;
    }

    public function uninstallTab(): bool
    {
        // Retrieve the parent tab ID from configuration
        $parentTabId = Configuration::get('DIMRDV_PARENT_TAB_ID');

        if ($parentTabId) {
            $tab = new Tab($parentTabId);
            if (Validate::isLoadedObject($tab)) {
                return $tab->delete();
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
                    PrestaShopLogger::addLog('SQL Error: ' . $e->getMessage(), 3);
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
            PrestaShopLogger::addLog('SQL Uninstall Error: ' . $e->getMessage(), 3);

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
            PrestaShopLogger::addLog('SQL Reset Error: ' . $e->getMessage(), 3);

            return false;
        }
    }

    public function hookDisplayHome(array $params): string
    {
        if (!isset($this->context->smarty->tpl_vars['hook']->value) || !$this->isCached($this->templateFile, $this->getCacheId('displayHome'))) {
            $this->context->smarty->assign([
                'dimrdv_link' => $this->context->link->getModuleLink($this->name, 'dimform'),
                'module' => $this,
            ]);
        }

        return $this->fetch($this->templateFile, $this->getCacheId('displayHome'));
    }

    public function hookActionFrontControllerSetMedia(array $params): void
    {
        $this->context->controller->registerStylesheet(
            'module-dimrdv-front',
            'modules/' . $this->name . '/views/css/front/dimrdv.css',
            [
                'media' => 'all',
                'priority' => 50,
            ]
        );
        $this->context->controller->registerJavascript(
            'module-dimrdv-front',
            'modules/' . $this->name . '/views/js/front/dimrdv.js',
            [
                'position' => 'bottom',
                'priority' => 50,
            ]
        );
    }

    public function hookActionAdminControllerSetMedia(array $params): void
    {
        $this->context->controller->registerStylesheet(
            'module-dimrdv-back',
            'modules/' . $this->name . '/views/css/back/dimrdv.css',
            [
                'media' => 'all',
                'priority' => 50,
            ]
        );
        $this->context->controller->registerJavascript(
            'module-dimrdv-back',
            'modules/' . $this->name . '/views/js/back/dimrdv.js',
            [
                'position' => 'bottom',
                'priority' => 50,
            ]
        );
    }

    public function getPathUri(): string
    {
        return $this->_path;
    }
}
