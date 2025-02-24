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

if (!defined('_PS_VERSION_')) { exit; }

use PrestaShop\PrestaShop\Core\Search\Filters\ConfigurationFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;

class AdminDimrdvConfigController extends FrameworkBundleAdminController
{
    public function indexAction()
    {
        return $this->render('@Modules/dimrdv/views/templates/admin/configure.html.twig', [
            'module_link' => $this->generateUrl('admin_dimrdv_config'),
        ]);
    }
}
