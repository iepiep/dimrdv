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
