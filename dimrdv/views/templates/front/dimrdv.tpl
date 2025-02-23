{*
  This file is part of the dimrdv project.
  (c) 2025 iepiep <r.minini@solution61.fr>
  
  This source file is subject to the MIT license that is bundled
  with this source code in the file LICENSE.
*}

{if isset($errors) && $errors|@count > 0}
    <div class="alert alert-danger">
        {foreach from=$errors item=error}
            <p>{$error}</p>
        {/foreach}
    </div>
{/if}

<form action="{$link->getModuleLink('dimrdv', 'dimform')}" method="post" class="dimrdv-form">
    <div class="form-group">
        <label for="lastname">{$module->l('Nom')}</label>
        <input type="text" name="lastname" id="lastname" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="firstname">{$module->l('Prénom')}</label>
        <input type="text" name="firstname" id="firstname" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="address">{$module->l('Adresse')}</label>
        <input type="text" name="address" id="address" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="postal_code">{$module->l('Code Postal')}</label>
        <input type="text" name="postal_code" id="postal_code" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="city">{$module->l('Ville')}</label>
        <input type="text" name="city" id="city" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="phone">{$module->l('Téléphone')}</label>
        <input type="text" name="phone" id="phone" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="email">{$module->l('Email')}</label>
        <input type="email" name="email" id="email" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="date_creneau1">{$module->l('Créneau Matin / Première plage')}</label>
        <select name="date_creneau1" id="date_creneau1" class="form-control" required>
            <option value="">{$module->l('Sélectionnez un créneau')}</option>
            {foreach from=$date_options item=option}
                <option value="{$option.value}">{$option.label}</option>
            {/foreach}
        </select>
    </div>
    <div class="form-group">
        <label for="date_creneau2">{$module->l('Créneau Après-midi / Deuxième plage')}</label>
        <select name="date_creneau2" id="date_creneau2" class="form-control" required>
            <option value="">{$module->l('Sélectionnez un créneau')}</option>
            {foreach from=$date_options item=option}
                <option value="{$option.value}">{$option.label}</option>
            {/foreach}
        </select>
    </div>
    <div class="form-group">
        <input type="checkbox" name="gdpr_consent" id="gdpr_consent" value="1" required>
        <label for="gdpr_consent">
            {$module->l('J\'accepte le traitement de mes données personnelles conformément à la réglementation RGPD.')}
            <a href="{$gdpr_link}" target="_blank">{$module->l('En savoir plus')}</a>
        </label>
    </div>
    <button type="submit" name="submit_dimrdv" class="btn btn-primary">{$module->l('Envoyer')}</button>
</form>
