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
                        <td>{$row.lastname|escape:'html'}</td>
                        <td>{$row.firstname|escape:'html'}</td>
                        <td>{$row.address|escape:'html'}</td>
                        <td>{$row.city|escape:'html'}</td>
                        <td>{$row.phone|escape:'html'}</td>
                        <td>{$row.email|escape:'html'}</td>
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