<?php
/*
 *   This file is part of NOALYSS.
 *
 *   NOALYSS is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   NOALYSS is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with NOALYSS; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
// Copyright (c) 2002 Author Dany De Bontridder dany@alchimerys.be
require_once 'class_acc_ledger_sold_generate.php';
$ledger = new Acc_Ledger_Sold_Generate($cn, -1);
$_GET['ledger_type'] = 'VEN';
//var_dump($_GET);
$request = HtmlInput::default_value_request('action', -2);
if ($request <> -2)
{
    if (!isset($_REQUEST['sel_sale']))
    {
        echo h2(_("Rien n'a été choisi"), 'class="notice"');
    } else
    {
        switch ($request)
        {
            case 1:
                // Download zip
                require ('invoice_to_zip.inc.php');
                break;
            case 2:
                // regenerer facture
                require ('invoice_generate.inc.php');
                break;
            case 3:
                // Envoi facture par email
                require('invoice_send_mail_route.inc.php');
                break;
        }
        return;
    }
}
echo $ledger->display_search_form();
// Example
// Build the sql
list($sql, $where) = $ledger->build_search_sql($_GET);
// Count nb of line
$max_line = $cn->count_sql($sql);

$offset = 0;
// show a part
list($count, $html) = $ledger->list_operation($sql, $offset, 0);

// --- template Invoice  to generate --- //
$document=new ISelect('document');
$document->value=$cn->make_array("select md_id,md_name from document_modele where md_affect='VEN' order by 2");

$document_to_send=new ISelect('format_document');
$document_to_send->value=array(
    array('value'=>'1',_('Convertir en PDF')),
    array('value'=>'2',_('Envoi de la facture sans conversion en PDF'))
)
?>
<div style="float:right"><a class="mtitle" style="font-size:140%" href="http://wiki.noalyss.eu/doku.php?id=facturation" target="_blank">Aide</a>
 </div>
<form method="GET" id="sel_sale_frm" onsubmit="return verify_invoicing()">
    <span class="notice"> Utiliser le bouton "Filtrer" pour chercher les factures voulues</span>
    <?php
    echo HtmlInput::request_to_hidden(array('gDossier', 'ac', 'plugin_code'));
    echo HtmlInput::request_to_hidden(array('date_start', 'date_end'));
    echo HtmlInput::request_to_hidden(array('date_paid_start', 'date_paid_end'));
    echo HtmlInput::request_to_hidden(array('amount_min', 'amount_max'));
    echo HtmlInput::request_to_hidden(array('desc', 'qcode', 'accounting'));
    echo HtmlInput::request_to_hidden(array('r_jrn'));
    echo $html;
    ?>
    <ul style="list-style-type: none">
        <li>
            
            <input type="radio" name="action" value="1" >
            <?php echo _('Télécharger toutes les factures') ?>
        </li>
        <li>
          
            <input type="radio" name="action" value="2" 
                   onclick="$('invoice_div').show();">
              <?php echo _('Générer les factures') ?>
            <div id="invoice_div" style="display:none">
                <?php echo _('Document à générer'); ?> : <?php echo $document->input(); ?>
            </div>
        </li>
        <li>
            <input type="radio" name="action" id="invoice_radio" value="3">
            <?php echo _('Envoi des factures par email') ?>
           
        </li>
    </ul>   
    <p>
        <?php
        echo HtmlInput::submit('choice_sel', 'Exécuter');
        ?>
    </p>
</form>

    
    