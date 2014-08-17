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
/* $Revision$ */

// Copyright Author Dany De Bontridder ddebontridder@yahoo.fr

/**
 * @file
 * @brief display content of a declaration
 *
 */
?>
<h1><?php echo $this->d_title?></h1>
<h2> Du <?php echo $this->d_start?> au <?php echo $this->d_end?></h2>
<p>
	Note : <?php echo h($this->d_description)?>
</p>
<?php 
	if ( empty($array) ) { echo 'Aucune donnée'; exit();}

	// Create infobulle
	echo HtmlInput::hidden('d_id',$this->d_id);
for ($i=0;$i<count($array);$i++):
	$row=$array[$i];
if ($this->d_step <> 0 ) $per= $row['dr_start']." : ".$row['dr_end'];
$per=(isset($per))?$per:"";
switch($row['dr_type'])
{
	case 1:
		echo '<h1  class="title">'.$row['dr_libelle'].'</h1>';
		break;
	case 2:
		echo '<h2  class="title">'.$row['dr_libelle'].'</h2>';
		break;
	case 6:
		echo '<h3  class="title">'.$row['dr_libelle'].'</h3>';
		break;
	case 3:
		$input=new INum('amount[]',$row['dr_amount']);
		$input->size=15;
		echo HtmlInput::hidden('code[]',$row['dr_id']);
		echo '<p>'.$row['dr_code']." ".$row['dr_libelle']." = ".$input->input()."  $per ".' </p>';
		break;
	case 7:
		echo '<p>'.$row['dr_libelle'].'</p>';
		break;
	case 8:
		echo '<p class="notice" > '.$row['dr_libelle'].'</p>';
		break;

}

endfor;
?>