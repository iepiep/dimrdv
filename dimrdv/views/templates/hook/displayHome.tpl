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

<div class="dimrdv-home">
    <p>{$module->l('Prenez rendez-vous pour une visite dès aujourd\'hui !')|escape:'html':'UTF-8'}</p>
    <a class="btn btn-primary" target="_blank" href="{$dimrdv_link|escape:'html':'UTF-8'}">
        {$module->l('Prendre un RDV')|escape:'html':'UTF-8'}
    </a>
</div>
