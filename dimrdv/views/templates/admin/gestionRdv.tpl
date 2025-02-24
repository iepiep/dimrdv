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

<h2>{l s='Gestion des RDV' mod='dimrdv'}</h2>

<form method="post" action="{$link->getAdminLink('AdminDimrdvItinerary')}">
    <table class="table">
        <thead>
            <tr>
                <th>{l s='ID' mod='dimrdv'}</th>
                <th>{l s='Nom' mod='dimrdv'}</th>
                <th>{l s='Prénom' mod='dimrdv'}</th>
                <th>{l s='Adresse' mod='dimrdv'}</th>
                <th>{l s='Ville' mod='dimrdv'}</th>
                <th>{l s='Téléphone' mod='dimrdv'}</th>
                <th>{l s='Email' mod='dimrdv'}</th>
                <th>{l s='Sélection' mod='dimrdv'}</th>
            </tr>
        </thead>
        <tbody>
            {if $rdvs}
                {foreach from=$rdvs item=row}
                    <tr>
                        <td>{$row.id_dim_rdv}</td>
                        <td>{$row.lastname|escape:'html':'UTF-8'}</td>
                        <td>{$row.firstname|escape:'html':'UTF-8'}</td>
                        <td>{$row.address|escape:'html':'UTF-8'}</td>
                        <td>{$row.city|escape:'html':'UTF-8'}</td>
                        <td>{$row.phone|escape:'html':'UTF-8'}</td>
                        <td>{$row.email|escape:'html':'UTF-8'}</td>
                        <td>
                            <input type="checkbox" name="selected[]" value="{$row.id_dim_rdv}" />
                        </td>
                    </tr>
                {/foreach}
            {else}
                <tr>
                    <td colspan="8">{l s='Aucun RDV trouvé' mod='dimrdv'}</td>
                </tr>
            {/if}
        </tbody>
    </table>
    <button type="submit" name="generate_itinerary" class="btn btn-primary">{l s='Générer itinéraire' mod='dimrdv'}</button>
</form>
