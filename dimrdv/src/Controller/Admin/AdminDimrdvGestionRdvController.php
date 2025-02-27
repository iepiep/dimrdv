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

namespace Iepiep\Dimrdv\src\Controller\Admin;

if (!defined('_PS_VERSION_')) {
    exit;
}

use Iepiep\Dimrdv\Service\ItineraryService;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Iepiep\Dimrdv\Dimrdv;

class AdminDimrdvGestionRdvController extends FrameworkBundleAdminController
{
    public function indexAction(): Response
    {
        $rdvs = \Db::getInstance()->executeS(
            'SELECT * FROM `' . _DB_PREFIX_ . 'dim_rdv` ORDER BY date_creneau1 ASC'
        ) ?: [];

        return $this->render('@Modules/dimrdv/views/templates/admin/gestionRdv.html.twig', [
            'rdvs' => $rdvs,
        ]);
    }
}
