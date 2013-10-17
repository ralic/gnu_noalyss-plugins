<?php

/*
 *   This file is part of PhpCompta.
 *
 *   PhpCompta is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   PhpCompta is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with PhpCompta; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
/* $Revision$ */

// Copyright Author Dany De Bontridder ddebontridder@yahoo.fr

/**
 * @file
 * @brief
 *
 */

?>

<table style="width:90%;margin-left:5%" >
	<tr>
	<td id="new_formula_id_bt" class="tool" style="background:red">
		<a class="mtitle"  href="javascript:void(0)"  onclick="show_type_formula('new_formula_id')">
			Formule
		</a>
	</td>
	<td id="new_compute_id_bt"  class="tool" style="background:inherit">
		<a class="mtitle" href="javascript:void(0)"
		   onclick="show_type_formula('new_compute_id')">
			Compute
		</a>
	</td>
	<td id="new_account_tva_id_bt"  class="tool" style="background:inherit">
		<a class="mtitle" href="javascript:void(0)"   onclick="show_type_formula('new_account_tva_id')">
			Poste comptable et code TVA
		</a>
	</td>
	<td id="new_account_id_bt"  class="tool" style="background:inherit">
		<a class="mtitle" href="javascript:void(0)"   onclick="show_type_formula('new_account_id')">
			Poste comptable
		</a>
	</td>
	<td id="new_reconcile_id_bt"  class="tool" style="background:inherit">
		<a class="mtitle" href="javascript:void(0)"   onclick="show_type_formula('new_reconcile_id')">
			Opérations rapprochées
		</a>
	</td>
</tr>
</table>
<div style="width:100%;height:290px;margin:1px">
	<span class="error" id="param_detail_info_div"></span>

	<div style="padding: 10px">
<div id="new_formula_id" style="display: block">
	<p>
	Entrer une formule avec des postes comptables, la syntaxe est la même que celle des "rapports"
	</p>
		<p>
	Exemple : [70%]*0.25+[71%]
	</p>

<form id="new_padef" method="POST" onsubmit="save_param_detail('new_padef');return false">
	<?php echo HtmlInput::request_to_hidden(array('gDossier','ac','plugin_code','p_id'))?>
	<p>
	<?php echo HtmlInput::hidden('tab','formula')?>
		<?php echo RAPAV_Formula::new_row()?>
	</p>
        <p> Filtrage par journal <?php echo $select->input(); ?> </p>
<?php echo HtmlInput::submit('save','Sauve')?>

</form>
</div>
<div id="new_account_tva_id" style="display: none">
	<p>
	Entrer un poste comptable et un code de TVA
	</p>
<form id="new_padea" method="POST" onsubmit="save_param_detail('new_padea');return false">
	<?php echo HtmlInput::request_to_hidden(array('gDossier','ac','plugin_code','p_id'))?>

	<?php echo HtmlInput::hidden('tab','account_tva')?>
		<?php echo RAPAV_Account_Tva::new_row()?>
    <p> Filtrage par journal <?php echo $select->input(); ?> </p>
<?php echo HtmlInput::submit('save','Sauve')?>

</form>
</div>
<div id="new_compute_id" style="display: none">
	<p>
	Entrer une formule avec des codes utilisés dans ce formulaires
	</p>
<form id="new_padec" method="POST" onsubmit="save_param_detail('new_padec');return false">
	<?php echo HtmlInput::request_to_hidden(array('gDossier','ac','plugin_code','p_id'))?>

	<?php echo HtmlInput::hidden('tab','compute_id')?>
		<?php echo RAPAV_Compute::new_row($p_id)?>
    <p>
<?php echo HtmlInput::submit('save','Sauve')?>
    </p>

</form>
</div>
	<div id="new_account_id" style="display: none">
<form id="new_paded" method="POST" onsubmit="save_param_detail('new_paded');return false">

	<?php echo HtmlInput::request_to_hidden(array('gDossier','ac','plugin_code','p_id'))?>

	<?php echo HtmlInput::hidden('tab','new_account_id')?>
		<?php echo RAPAV_Account::new_row($p_id)?>
    <p> Filtrage par journal <?php echo $select->input(); ?> </p>
<?php echo HtmlInput::submit('save','Sauve')?>

</form>
</div>
		<div id="new_reconcile_id" style="display: none">
<form id="new_padee" method="POST" onsubmit="save_param_detail('new_padee');return false">

	<?php echo HtmlInput::request_to_hidden(array('gDossier','ac','plugin_code','p_id'))?>

	<?php echo HtmlInput::hidden('tab','new_reconcile_id')?>
		<?php echo RAPAV_Reconcile::new_row($p_id)?>
    <p> Filtrage par journal <?php echo $select->input(); ?> </p>
<?php echo HtmlInput::submit('save','Sauve')?>

</form>
</div>
	</div>
</div>