/**
 *  Copyright (c) 2025 iepiep <r.minini@solution61.fr>
 *
 *  This source file is subject to the MIT license that is bundled
 *  with this source code in the file LICENSE.
 *
 *  If you did not receive a copy of the license and are unable to
 *  obtain it through the world-wide-web, please send an email
 *  to r.minini@solution61.fr so we can send you a copy immediately.
 */

{if isset($errors) && $errors|@count > 0}
    <div class="alert alert-danger">
        {foreach from=$errors item=error}
            <p>{$error|escape:'html':'UTF-8'}</p>
        {/foreach}
    </div>
{/if}

{if isset($confirmation_message)}
    <div class="alert alert-success">
        {$confirmation_message|escape:'html':'UTF-8'}
    </div>
{/if}

<form action="{$link->getModuleLink('dimrdv', 'dimform')|escape:'html':'UTF-8'}" method="post" class="dimrdv-form">
    <div class="form-group">
        <label for="lastname">{$module->l('Last Name')|escape:'html':'UTF-8'}</label>
        <input type="text" name="lastname" id="lastname" class="form-control" value="{$formValues.lastname|escape:'html':'UTF-8'}" required>
    </div>
    <div class="form-group">
        <label for="firstname">{$module->l('First Name')|escape:'html':'UTF-8'}</label>
        <input type="text" name="firstname" id="firstname" class="form-control" value="{$formValues.firstname|escape:'html':'UTF-8'}" required>
    </div>
    <div class="form-group">
        <label for="address">{$module->l('Address')|escape:'html':'UTF-8'}</label>
        <input type="text" name="address" id="address" class="form-control" value="{$formValues.address|escape:'html':'UTF-8'}" required>
    </div>
    <div class="form-group">
        <label for="postal_code">{$module->l('Postal Code')|escape:'html':'UTF-8'}</label>
        <input type="text" name="postal_code" id="postal_code" class="form-control" value="{$formValues.postal_code|escape:'html':'UTF-8'}" required>
    </div>
    <div class="form-group">
        <label for="city">{$module->l('City')|escape:'html':'UTF-8'}</label>
        <input type="text" name="city" id="city" class="form-control" value="{$formValues.city|escape:'html':'UTF-8'}" required>
    </div>
    <div class="form-group">
        <label for="phone">{$module->l('Phone')|escape:'html':'UTF-8'}</label>
        <input type="text" name="phone" id="phone" class="form-control" value="{$formValues.phone|escape:'html':'UTF-8'}" required>
    </div>
    <div class="form-group">
        <label for="email">{$module->l('Email')|escape:'html':'UTF-8'}</label>
        <input type="email" name="email" id="email" class="form-control" value="{$formValues.email|escape:'html':'UTF-8'}" required>
    </div>
    <div class="form-group">
        <label for="date_creneau1">{$module->l('Time Slot (Morning)')|escape:'html':'UTF-8'}</label>
        <select name="date_creneau1" id="date_creneau1" class="form-control" required>
            <option value="">{$module->l('Select a time slot')|escape:'html':'UTF-8'}</option>
            {foreach from=$date_options item=option}
                <option value="{$option.value|escape:'html':'UTF-8'}" {if $formValues.date_creneau1 == $option.value}selected{/if}>{$option.label|escape:'html':'UTF-8'}</option>
            {/foreach}
        </select>
    </div>
    <div class="form-group">
        <label for="date_creneau2">{$module->l('Time Slot (Afternoon)')|escape:'html':'UTF-8'}</label>
        <select name="date_creneau2" id="date_creneau2" class="form-control" required>
            <option value="">{$module->l('Select a time slot')|escape:'html':'UTF-8'}</option>
            {foreach from=$date_options item=option}
                <option value="{$option.value|escape:'html':'UTF-8'}"  {if $formValues.date_creneau2 == $option.value}selected{/if}>{$option.label|escape:'html':'UTF-8'}</option>
            {/foreach}
        </select>
    </div>
    <div class="form-group">
        <input type="checkbox" name="gdpr_consent" id="gdpr_consent" value="1" {if $formValues.gdpr_consent}checked{/if} required>
        <label for="gdpr_consent">
            {$module->l('I agree to the processing of my personal data in accordance with the GDPR regulations.')|escape:'html':'UTF-8'}
            <a href="{$gdpr_link|escape:'html':'UTF-8'}" target="_blank">{$module->l('Learn more')|escape:'html':'UTF-8'}</a>
        </label>
    </div>
    <button type="submit" name="submit_dimrdv" class="btn btn-primary">{$module->l('Submit')|escape:'html':'UTF-8'}</button>
</form>
