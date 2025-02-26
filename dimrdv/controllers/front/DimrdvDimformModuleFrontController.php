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

use PrestaShop\PrestaShop\Core\MailTemplate\Layout\LayoutInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class DimrdvDimformModuleFrontController extends ModuleFrontController
{
    public $ssl = true;

    public function initContent()
    {
        parent::initContent();

        // Check for confirmation message
        $confirmation = Tools::getValue('conf');

        if ($confirmation == 1) {
            $this->context->smarty->assign([
                'confirmation_message' => $this->module->l('Your appointment request has been successfully submitted.', 'dimform'),
            ]);
        }

        if (Tools::isSubmit('submit_dimrdv')) {
            $this->processForm();
        }

        $this->context->smarty->assign([
            'module' => $this->module,
            'module_dir' => $this->module->getPathUri(),
            'gdpr_link' => $this->context->link->getPageLink('gdpr'),
            'date_options' => $this->getDateOptions(),
            'errors' => $this->errors, // Pass errors to the template
            'confirmation_message' => $this->context->smarty->tpl_vars['confirmation_message'] ?? null,
            'formValues' => [ // Pass form values back to the template to repopulate fields
                'lastname' => Tools::getValue('lastname'),
                'firstname' => Tools::getValue('firstname'),
                'address' => Tools::getValue('address'),
                'postal_code' => Tools::getValue('postal_code'),
                'city' => Tools::getValue('city'),
                'phone' => Tools::getValue('phone'),
                'email' => Tools::getValue('email'),
                'date_creneau1' => Tools::getValue('date_creneau1'),
                'date_creneau2' => Tools::getValue('date_creneau2'),
                'gdpr_consent' => Tools::getValue('gdpr_consent'),
            ],
        ]);

        $this->setTemplate('module:dimrdv/views/templates/front/dimrdv.tpl');
    }

    protected function processForm()
    {
        // Sanitize input
        $lastname = Tools::getValue('lastname');
        $firstname = Tools::getValue('firstname');
        $address = Tools::getValue('address');
        $postal_code = Tools::getValue('postal_code');
        $city = Tools::getValue('city');
        $phone = Tools::getValue('phone');
        $email = Tools::getValue('email');
        $date_creneau1 = Tools::getValue('date_creneau1');
        $date_creneau2 = Tools::getValue('date_creneau2');
        $gdpr_consent = Tools::getValue('gdpr_consent');

        // Validation
        if (empty($lastname) || !Validate::isName($lastname)) {
            $this->errors[] = $this->module->l('Invalid lastname.', 'dimform');
        }

        if (empty($firstname) || !Validate::isName($firstname)) {
            $this->errors[] = $this->module->l('Invalid firstname.', 'dimform');
        }

        if (empty($address) || !Validate::isAddress($address)) {
            $this->errors[] = $this->module->l('Invalid address.', 'dimform');
        }

        if (empty($postal_code) || !Validate::isPostCode($postal_code)) {
            $this->errors[] = $this->module->l('Invalid postal code.', 'dimform');
        }

        if (empty($city) || !Validate::isCityName($city)) {
            $this->errors[] = $this->module->l('Invalid city.', 'dimform');
        }

        if (empty($phone) || !Validate::isPhoneNumber($phone))
            $this->errors[] = $this->module->l('Invalid phone number.', 'dimform');
        }

        if (empty($email) || !Validate::isEmail($email)) {
            $this->errors[] = $this->module->l('Invalid email address.', 'dimform');
        }

        if (empty($date_creneau1)) {
            $this->errors[] = $this->module->l('Please select a time slot (morning).', 'dimform');
        }

        if (empty($date_creneau2)) {
            $this->errors[] = $this->module->l('Please select a time slot (afternoon).', 'dimform');
        }

        if (!$gdpr_consent) {
            $this->errors[] = $this->module->l('You must accept the GDPR terms.', 'dimform');
        }

        // If there are errors, return to the form
        if (!empty($this->errors)) {
            return;
        }

        $data = [
            'lastname' => pSQL($lastname),
            'firstname' => pSQL($firstname),
            'address' => pSQL($address),
            'postal_code' => pSQL($postal_code),
            'city' => pSQL($city),
            'phone' => pSQL($phone),
            'email' => pSQL($email),
            'date_creneau1' => pSQL($date_creneau1),
            'date_creneau2' => pSQL($date_creneau2),
            'visited' => 0,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        if (Db::getInstance()->insert('dim_rdv', $data)) {
            Tools::redirect($this->context->link->getModuleLink('dimrdv', 'dimform', ['conf' => 1]));
        } else {
            $this->errors[] = $this->module->l('An error occurred while saving your appointment request.', 'dimform');
        }
    }

    // Generates time slot options for the next two weeks (excluding weekends)
    protected function getDateOptions()
    {
        $options = [];
        $start = new DateTime();
        $end = (new DateTime())->modify('+14 days');

        while ($start <= $end) {
            // Exclude Saturday (6) and Sunday (7)
            if (!in_array($start->format('N'), [6, 7])) {
                $dateStr = $start->format('l d/m/y');
                $options[] = ['value' => $dateStr . ' MATIN', 'label' => $dateStr . ' MATIN'];
                $options[] = ['value' => $dateStr . ' APRES-MIDI', 'label' => $dateStr . ' APRES-MIDI'];
            }

            $start->modify('+1 day');
        }

        return $options;
    }
}
