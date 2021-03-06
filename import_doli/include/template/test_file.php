<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
?>

<h2> 
<?php
echo _('Importation de données');
?>
</h2>

<p>
    <?php echo _("Pour importer des données, c'est-à-dire transformer des fichiers CSV (Valeur séparé par des virgules) en des fiches. Vous devez choisir, un fichier et donner une catégorie de fiche existante. Ensuite, il suffit d'indiquer quelles colonnes correspondent à quelle attribut. ");?>
</p>

<form method="POST" >
<?php echo $hidden?>
<table>
<tr>
<td>Délimiteur </td>
<td> <?php echo $_POST['rdelimiter']?></td>
</tr>
<tr>
<td><?php echo _("Fichier à charger");?></td><td> <?php echo $_FILES['csv_file']['name']?></td>
</tr>
<tr>
<td><?php echo _("Catégorie de fiche");?></td><td> <?php echo $file_cat;?></td>
</tr>
<tr>
<td><?php echo _("Encodage");?> </td><td> <?php echo $encoding?></td>
</tr>
<tr>
<td><?php echo _("Texte entouré par");?></td><td> <?php echo $_POST['rsurround'];?></td>
</tr>
</table>
<?php 
foreach (array('rfichedef','rdelimiter','encodage') as $e)
{
  if ( isset($_POST[$e])) echo HtmlInput::hidden($e,$_POST[$e]);
}
echo HtmlInput::hidden('filename',$filename);

 echo HtmlInput::submit('record_import','Valider');
?>
<input type="hidden" name="rsurround" value='<?php echo $_POST['rsurround']?>'>



<?php 
   global $cn;
   ob_start();
  /**
   * Open the file and parse it
   */
$fcard=fopen($filename,'r');
$row_count=0;
$max=0;
while (($row=fgetcsv($fcard,0,$_POST['rdelimiter'],$_POST['rsurround'])) !== false)
  {
    $row_count++;
    echo '<tr style="border:solid 1px black">';
    echo td($row_count);
    $count_col=count($row);
    $max=($count_col>$max)?$count_col:$max;
    for ($i=0;$i<$count_col;$i++)
      {
	echo td($row[$i],'style="border:solid 1px black"');
      }
      echo '</tr>';
  }
$table=ob_get_contents();
ob_end_clean();


echo '<table style="border:solid 1px black;width:100%">
<tr>';

/**
 *create widget column header
 */
$header=new ISelect('head_col[]');
$fiche_def=HtmlInput::default_value_post('rfichedef', -1);
if ( $fiche_def == -1 ||isNumber($fiche_def)==0) {
     throw new Exception(_('Catégorie invalide'));
}
$sql=sprintf('select ad_id,ad_text from jnt_fic_attr join attr_def using(ad_id) where fd_id=%d order by ad_text ',  sql_string($fiche_def));
$a_attribute=$cn->get_array($sql);
$header->value=$cn->make_array($sql);
$header->value[]=array('value'=>-1,'label'=>'-- Non Utilisé --');
$header->selected=-1;
echo th(_('Numéro de ligne'));
for ($i=0;$i<$max;$i++)
  {
    // Select the default column
    $header->selected=$a_attribute[$i]['ad_id'];
    echo '<th>'.$header->input().'</th>';
  }
echo '</tr>';
echo $table;
echo '</table>';
echo '</form>';
?>