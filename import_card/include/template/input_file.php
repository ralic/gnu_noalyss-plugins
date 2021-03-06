<h2> 
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
echo _('Importation de données');
?>
</h2>

<p><?php
echo _("Pour importer des données, c'est-à-dire transformer des fichiers CSV (Valeur séparé par des virgules) en des fiches. Vous devez choisir, un fichier et donner une catégorie de fiche existante. Ensuite, il suffit d'indiquer quelles colonnes correspondent à quelle attribut. ")?>
</p>
<p>
   <?php
   echo _("Par défaut, correspond à un export CSV depuis Calc (OpenOffice.org ou libreoffice)");?>
</p>

<form method="POST" enctype="multipart/form-data">
<?php echo $hidden?>

<?php echo _("Fichier à charger");?></td><TD> <?php echo $file->input()?>
<?php echo _("Catégorie de fiche");?></td><TD> <?php echo $fd->input();?>
<?php echo HtmlInput::submit('import_file',_('Valider'));?>

</form>
