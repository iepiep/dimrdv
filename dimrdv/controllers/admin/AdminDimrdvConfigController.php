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
namespace  Iepiep\Dimrdv\Controller\Admin;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminDimrdvConfigController extends FrameworkBundleAdminController
{
    public function indexAction(Request $request): Response
    {
        $form = $this->createForm('Iepiep\Dimrdv\Form\ConfigFormType', [
            'DIMRDV_GOOGLE_API_KEY' => $this->getConfigurationValue('DIMRDV_GOOGLE_API_KEY'),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $this->updateConfigurationValue('DIMRDV_GOOGLE_API_KEY', $data['DIMRDV_GOOGLE_API_KEY']);

            $this->addFlash('success', $this->trans('Settings updated.', 'Admin.Notifications.Success'));

            return $this->redirectToRoute('admin_dimrdv_config');
        }

        return $this->render('@Modules/dimrdv/views/templates/admin/configure.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    private function getConfigurationValue(string $key): string
    {
        return \Configuration::get($key) ?: '';
    }

    private function updateConfigurationValue(string $key, string $value): void
    {
        \Configuration::updateValue($key, $value);
    }
}
