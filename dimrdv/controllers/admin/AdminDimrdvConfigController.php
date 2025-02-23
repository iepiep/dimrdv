<?php

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