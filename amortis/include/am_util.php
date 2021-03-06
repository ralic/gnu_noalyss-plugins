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

// Copyright (c) 2002 Author Dany De Bontridder dany@alchimerys.be

/*!\file
 * \brief let you generate the accounting for the paid off for a selected
 *  year history and remove
 */
$url='?'.dossier::get().'&plugin_code='.$_REQUEST['plugin_code'].'&sa=util'.'&ac='.$_REQUEST['ac'];

$menu=array(
	    array($url.'&sb=generate','Génére écriture',' Génération écriture comptable ',1),
	    array($url.'&sb=histo','Historique','Historique des opérations',3)
	    );


$sb=(isset($_REQUEST['sb']))?$_REQUEST['sb']:-1;
$_REQUEST['sb']=$sb;
$def=0;

switch($sb)
  {
  case 'generate':
    $def=1;
    break;
  case 'histo':
    $def=3;
    break;
  }
echo '<div class="menu2">';
echo ShowItem($menu,'H','mtitle ','mtitle ',$def,' class="mtitle" ');
echo '</div>';
    
/* List + add and modify card */
if ($def==1)
  {
    require_once('am_generate.inc.php');
    exit();
  }

/* histo */
if ( $def==3)
  {
    require_once('am_histo.inc.php');
    exit();
  }
