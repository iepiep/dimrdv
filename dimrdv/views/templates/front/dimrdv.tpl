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

<form action="{$link->getModuleLink('dimrdv', 'dimform')|escape:'html':'UTF-8'}" method="post" class="dimrdv-form">
    <div class="form-group">
        <label for="lastname">{$module->l('Nom')|escape:'html':'UTF-8'}</label>
        <input type="text" name="lastname" id="lastname" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="firstname">{$module->l('Prénom')|escape:'html':'UTF-8'}</label>
        <input type="text" name="firstname" id="firstname" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="address">{$module->l('Adresse')|escape:'html':'UTF-8'}</label>
        <input type="text" name="address" id="address" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="postal_code">{$module->l('Code Postal')|escape:'html':'UTF-8'}</label>
        <input type="text" name="postal_code" id="postal_code" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="city">{$module->l('Ville')|escape:'html':'UTF-8'}</label>
        <input type="text" name="city" id="city" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="phone">{$module->l('Téléphone')|escape:'html':'UTF-8'}</label>
        <input type="text" name="phone" id="phone" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="email">{$module->l('Email')|escape:'html':'UTF-8'}</label>
        <input type="email" name="email" id="email" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="date_creneau1">{$module->l('Créneau Matin / Première plage')|escape:'html':'UTF-8'}</label>
        <select name="date_creneau1" id="date_creneau1" class="form-control" required>
            <option value="">{$module->l('Sélectionnez un créneau')|escape:'html':'UTF-8'}</option>
            {foreach from=$date_options item=option}
                <option value="{$option.value|escape:'html':'UTF-8'}">{$option.label|escape:'html':'UTF-8'}</option>
            {/foreach}
        </select>
    </div>
    <div class="form-group">
        <label for="date_creneau2">{$module->l('Créneau Après-midi / Deuxième plage')|escape:'html':'UTF-8'}</label>
        <select name="date_creneau2" id="date_creneau2" class="form-control" required>
            <option value="">{$module->l('Sélectionnez un créneau')|escape:'html':'UTF-8'}</option>
            {foreach from=$date_options item=option}
                <option value="{$option.value|escape:'html':'UTF-8'}">{$option.label|escape:'html':'UTF-8'}</option>
            {/foreach}
        </select>
    </div>
    <div class="form-group">
        <input type="checkbox" name="gdpr_consent" id="gdpr_consent" value="1" required>
        <label for="gdpr_consent">
            {$module->l('J\'accepte le traitement de mes données personnelles conformément à la réglementation RGPD.')|escape:'html':'UTF-8'}
            <a href="{$gdpr_link|escape:'html':'UTF-8'}" target="_blank">{$module->l('En savoir plus')|escape:'html':'UTF-8'}</a>
        </label>
    </div>
    <button type="submit" name="submit_dimrdv" class="btn btn-primary">{$module->l('Envoyer')|escape:'html':'UTF-8'}</button>
</form>
