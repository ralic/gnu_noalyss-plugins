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
 * @brief Déclaration
 *
 */
require_once 'class_rapav_declaration.php';
global $cn;

/*
 * Save the date (update them)
 */
if (isset($_POST['save']))
{
	$decl = new Rapav_Declaration();
	$decl->d_id = $_POST['d_id'];
	$decl->load();
	$decl->to_keep = 'Y';
	$decl->f_id = $_POST['p_form'];
	$decl->save();
	if ( $decl->d_step == 0 ) {
		$decl->generate_document();
	}	else
	{
		// get empty lob
		$decl->d_filename = null;
		$decl->d_size = null;
		$decl->d_mimetype = null;
		$decl->d_lob = null;
		$decl->update();
	}
	$decl->display();
	echo '<p class="notice">'._(' Sauvé ').date('d-m-Y H:i').'</p>';

	$ref_csv = HtmlInput::array_to_string(array('gDossier', 'plugin_code', 'd_id'), $_REQUEST, 'extension.raw.php?');
	$ref_csv.="&amp;act=export_decla_csv";
	echo HtmlInput::button_anchor("Export CSV", $ref_csv, 'export_id');
	if ( $decl->d_filename != '' && $decl->d_step==0) echo $decl->anchor_document();
	exit();
}
/*
 * compute and propose to modify and save
 */
if (isset($_GET['compute']))
{
	$decl = new Rapav_Declaration();
	if (isDate($_GET['p_start']) == 0 || isDate($_GET['p_end']) == 0)
	{
		alert('Date invalide');
	}
	else
	{
		$decl->d_description=$_GET['p_description'];
		$decl->compute($_GET['p_form'], $_GET['p_start'], $_GET['p_end'],$_GET['p_step']);
		echo '<form class="print" method="POST">';
		echo HtmlInput::hidden('p_form', $_GET['p_form']);
		$decl->display();
		echo HtmlInput::submit('save', 'Sauver');
		echo '</form>';
		exit();
	}
}
$date_start = new IDate('p_start');
$date_end = new IDate('p_end');
$hidden = HtmlInput::array_to_hidden(array('gDossier', 'ac', 'plugin_code', 'sa'), $_GET);
$select = new ISelect('p_form');
$select->value = $cn->make_array('select f_id,f_title from rapport_advanced.formulaire order by 2');
$description=new ITextArea('p_description');
$description->heigh=2;
$description->width=80;

$istep=new ISelect('p_step');
$istep->value=array(
		array('label'=>'Aucun','value'=>0),
		array('label'=>'7 jours','value'=>1),
		array('label'=>'14 jours','value'=>2),
		array('label'=>'1 mois','value'=>3),
		array('label' => '2 mois', 'value' => 4),
	array('label' => '3 mois', 'value' => 5)
);
?>
<form id="declaration_form_id" method="GET" onsubmit="return validate()">
	<?php echo  $hidden?>
	<table>
		<tr>
			<td>
				Déclaration
			</td>
			<td>
				<?php echo  $select->input()?>
			</td>
			</tr>
			<tr>
	<td> Description</td><td> <?php echo $description->input()?></td>
	</tr>
		<tr>
			<td>
				Date de début
			</td>
			<td>
				<?php echo  $date_start->input()?>
			</td>
		</tr>
		<tr>
			<td>
				Date de fin
			</td>
			<td>
				<?php echo  $date_end->input()?>
			</td>
		</tr>
		<tr>
			<td>
				Etape de
			</td>
			<td>
				<?php echo  $istep->input()?>
			</td>
		</tr>
	</table>
	</p>
	<?php echo  HtmlInput::submit('compute', 'Générer')?>
</form>
<script charset="UTF8" lang="javascript">
	function validate() {
		if ( check_date_id('<?php echo  $date_start->id?>') == false ) {alert('Date de début incorrecte');$('<?php echo  $date_start->id?>').style.borderColor='red';$('<?php echo  $date_start->id?>').style.borderWidth=2;return false;}
		if ( check_date_id('<?php echo  $date_end->id?>') == false ) {alert('Date de fin incorrecte');$('<?php echo  $date_end->id?>').style.borderColor='red';$('<?php echo  $date_end->id?>').style.borderWidth=2;return false;}
	}
</script>