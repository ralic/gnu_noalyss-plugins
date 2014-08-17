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
$row_count=0;$max=0;
ob_start();

switch ($sep_field->selected)
{
	case 1:
		$sp=',';
		break;
	case 2:
		$sp=';';
		break;
        default:
	  echo "Séparateur invalide ";
	  exit();
}

while( ($row=fgetcsv($fbank,0,$sp)) !== false)
{
	if ( $row_count >= $skip->value)
	  echo '<tr style="border:solid 1px black">';
	else
	  echo '<tr style="background:red;border:solid 1px red">';
	$row_count++;

	$array_row=$row;
   $count_col=count($array_row);
     $max=($count_col>$max)?$count_col:$max;
     echo td($row_count);
     echo td($count_col);
    for ($i=0;$i<$count_col;$i++)
      {
	echo td(utf8_encode($array_row[$i]),'style="border:solid 1px black"');
      }
      echo '</tr>';
}
$table=ob_get_contents();
ob_end_clean();


$nb_col->value=($nb_col->value=='')?$max:$nb_col->value;
?>

<h2>Etape 3/4 : confirmez le transfert</h2>
<form method="POST"   enctype="multipart/form-data">
<table>
<tr>
	<td>
	Nom du format
	</td>
	<td>
	<?php echo $format->input()?>
	</td>
</tr>
<tr>
	<td>
	A importer dans le journal de banque
	</td>
	<td>
	<?php echo $jrn_def->input()?>
	</td>
</tr>
<tr>
	<td>
Format de date
	</td>
	<td>
	<?php echo $format_date->input()?>
	</td>
</tr>

<tr>
	<td>
	Séparateur de champs
	</td>
	<td>
	<?php echo $sep_field->input()?>
	</td>
</tr>

<tr>
	<td>
	Séparateur de millier
	</td>
	<td>
	<?php echo $sep_thousand->input()?>
	</td>
</tr>

<tr>
	<td>
	Séparateur décimal
	</td>
	<td>
	<?php echo $sep_decimal->input()?>
	</td>
</tr>
<tr>
	<td>
	Ligne d'en-tête à ne pas prendre en considération
	</td>
	<td>
	<?php echo $skip->input()?>
	</td>
</tr>
<tr>
	<td>
	Les lignes ayant ce nombre de colonnes sont valides
	</td>
	<td>
	<?php echo $nb_col->input()?>
	</td>
</tr>





</table>
<p class="notice">Les lignes en rouge ne seront pas importées</p>
<?php
echo HtmlInput::post_to_hidden(array('gDossier','plugin_code','sa','format'));
echo HtmlInput::hidden('sb','upload_file');
echo HtmlInput::hidden('filename',$filename);
echo HtmlInput::submit('correct_format','Changer format');
echo '</form>';
?>

<form method="POST"   enctype="multipart/form-data">

<?php

echo HtmlInput::post_to_hidden(array('format','gDossier','plugin_code','sa','format','jrn_def','format_name','format_date','sep_field','sep_thous','sep_dec','skip'));
echo HtmlInput::hidden('sb',$sb);
echo HtmlInput::hidden('nb_col',$nb_col->value);

echo HtmlInput::hidden('filename',$filename);
echo HtmlInput::submit('transfer_submit','Enregistrer les opérations');
?>
<table>
<tr>
<?php
$header=new ISelect('header[]');
$header->value=$aheader;

echo th('Ligne n°');
echo th('Nbre de colonnes');

for ( $i=0;$i<$max;$i++)
{
  $header->selected=-1;
	switch ($i)
	{
		case $pos_date:
		  $header->selected=0;
		  break;

		case $pos_amount:
		  $header->selected=1;
		  break;

		case $pos_lib:
		  $header->selected=2;
		  break;

		case $pos_operation_nb:
		  $header->selected=3;
		  break;
		case $pos_third:
		  $header->selected=4;
		  break;
		case $pos_extra:
		  $header->selected=5;
		  break;
	}
	echo '<th>'.$header->input()."</th>";

}
echo '</tr>';
echo $table;

?>
</table>
<?php echo HtmlInput::submit('transfer_submit','Enregistrer les opérations')?>
</form>