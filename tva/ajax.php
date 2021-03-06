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
require_once NOALYSS_INCLUDE.'/lib/class_database.php';
require_once('class_ext_tva.php');
require_once NOALYSS_INCLUDE.'/lib/class_ibutton.php';
require_once ('class_ext_list_intra.php');
require_once ('class_ext_list_assujetti.php');
require_once NOALYSS_INCLUDE.'/class/class_acc_ledger.php';

extract($_GET, EXTR_SKIP);
global $g_parameter,$cn;

$html='';$extra='';$ctl='';
switch($act) {
case 'dsp_decl':
  /* the hide button */
  $button=new IButton('hide');
  $button->label=_('Retour');
  $button->javascript="$('detail').hide();$('main').show();";
  if ( $type == 1) {
    /* display the declaration of amount */
    $decl=new Ext_Tva($cn);
    $decl->set_parameter('id',$id);
    $decl->load();
    $r=$button->input();
    $r.=$decl->display();
    $r.=$button->input();
  }
  if ( $type == 3) {
    /* display the declaration for INTRACOM */
    $decl=new Ext_List_Intra($cn);
    $decl->set_parameter('id',$id);
    $decl->load();
    $r=$button->input();
    $r.=$decl->display();
    $r.=$button->input();
  }
  if ( $type == 2) {
    /* display the declaration of customer */
    $decl=new Ext_List_Assujetti($cn);
    $decl->set_parameter('id',$id);
    $decl->load();
    $r=$button->input();
    $r.=$decl->display();
    $r.=$button->input();
  }

  break;
  /**
   * generate writing
   */
case 'rw':
  require_once NOALYSS_INCLUDE.'/class/class_acc_ledger.php';
  $count=$cn->get_value('select count(*) from tva_belge.declaration_amount where da_id=$1',array($_REQUEST['p_id']));
  if ( $count == 1 )
    {
      $ctl='record_write';
      $ledger=new Acc_ledger($cn,0);
      $sel_ledger=$ledger->select_ledger('ODS',1);
      $r=HtmlInput::title_box('Génération écriture','record_write');
	  if ($sel_ledger != null)
	  {
		  $r.='<form onsubmit="save_write(this);return false;">';
		  $decl=new Ext_Tva($cn);
		  $decl->set_parameter('id',$_GET['p_id']);
		  $decl->load();
		  $date=new IDate('pdate');
		  $r.="Date :".$date->input().'<br>';
		  $r.="Choix du journal : ".$sel_ledger->input();
		  $r.=$decl->propose_form();
		  $r.=HtmlInput::hidden('mt',microtime(true));
		  $r.=HtmlInput::extension();
		  $r.=dossier::hidden();
		  $r.=HtmlInput::submit('save','Sauver','onclick="return confirm(\'Vous confirmez ? \')"');
		  $r.='</form>';
		} else {
			$r.='<h2 class="error"> Aucun journal accessible</h2>';
		}
	}
	else
    {
      $ctl='record_write';
      $r=HtmlInput::anchor_close($ctl);
      $r.="<h2 class=\"info\">Désolé cette opération n'existe pas </h2>";
      $r.='<span class="notice">Il se peut que l\'information aie été effacée</span>';
      $r.=HtmlInput::button_close($ctl);
    }
  break;

case 'sw':
  $ctl='record_write';
  ob_start();
  echo   '<div style="float:right"><a class="mtitle" href="javascript:void(0)" onclick="removeDiv(\'record_write\')">fermer</a></div>';
  extract($_GET, EXTR_SKIP);
  try {
    $array=array();
    $array['e_date']=$pdate;
    /* give automatically the periode */
    $periode=new Periode($cn);
    $periode->find_periode($pdate);
    $array['period']=$periode->p_id;
    $nb_item=count($account);
    for ($i=0;$i<count($account);$i++) {
      $array['amount'.$i]=$amount[$i];
      $array['poste'.$i]=$account[$i];
      $array['ld'.$i]='';
      if ( isset($deb[$i])) $array['ck'.$i]=1;
    }
    /* ATVA */
    if ( isset($atva)) {
      $array['poste'.$i]=$atva;
      $array['amount'.$i]=$atva_amount;
      $array['ld'.$i]='';
      if ( isset($atva_ic)) $array['ck'.$i]=1;
      $i++;
      $nb_item++;
    }
    /* CRED */
   if ( isset($crtva)) {
      $array['poste'.$i]=$crtva;
      $array['amount'.$i]=$crtva_amount;
      $array['ld'.$i]='';
      if ( isset($crtva_ic)) $array['ck'.$i]=1;
      $i++;
      $nb_item++;
    }
    /* DET */
   if ( isset($dttva)) {
      $array['poste'.$i]=$dttva;
      $array['amount'.$i]=$dttva_amount;
      $array['ld'.$i]='';
      if ( isset($dttva_ic)) $array['ck'.$i]=1;
      $i++;
      $nb_item++;
    }
    /* solde */
   if ( isset($solde)) {
      $array['poste'.$i]=$solde;
      $array['amount'.$i]=$solde_amount;
      $array['ld'.$i]='';
      if ( isset($solde_ic)) $array['ck'.$i]=1;
      $i++;
      $nb_item++;
    }
   $array['nb_item']=$nb_item;
   $array['e_pj']='';
   $array['e_pj_suggest']='NONE';

   $array['p_jrn']=$p_jrn;
   $array['mt']=$mt;

   $array['desc']='Extension TVA : écriture générée';
   $ods=new Acc_Ledger($cn,$p_jrn);
   $ods->save($array);
   echo h2info("Sauvée : ajoutez le numéro de pièce");
   echo   HtmlInput::detail_op($ods->jr_id,'détail opération : '.$ods->internal);
   $ods->with_concerned=false;
   echo $ods->confirm($array,true);
  } catch(Exception $e) {
    echo alert($e->getMessage());
  }
  $r=ob_get_contents();
  ob_end_clean();
   break;
case 'rm_form':
  switch($type)
    {
    case 'da':
      $sql="delete from tva_belge.declaration_amount where da_id=$1";
      break;
    case 'li':
      $sql="delete from tva_belge.intracomm where i_id=$1";
      break;
    case 'lc':
      $sql="delete from tva_belge.assujetti where a_id=$1";
      break;
    }
  $ctl='remove_form';
  $cn->exec_sql($sql,array($_REQUEST['p_id']));
  $r=HtmlInput::anchor_close($ctl);
  $r.='<h2 class="info"> Information </h2>';
  $r.='<h2 class="notice">Opération effacée</h2>';
  $r.=HtmlInput::button_close($ctl);
  $html=escape_xml($r);
  break;
case 'add_param':
	$ctl="paramadd_id";
	$r=HtmlInput::title_box("Ajout paramètre",$ctl);
	$r.='<h3>'._('Pour la grille ').$pcode.'</h3>';
	// TVA input
	$text = new ITva_Popup('tva_id');
	$text->add_label('tva_label');
	$text->js = 'onchange="set_tva_label(this);"';
	$text->with_button(true);

	// Accounting
	$iposte=new IPoste('paccount');
	$iposte->set_attribute('gDossier',Dossier::id());
	$iposte->set_attribute('jrn',0);
	$iposte->set_attribute('account','paccount');
	$iposte->set_attribute('label','paccount_label');


	$r.='<form method="POST" id="faddparm"onsubmit="return confirm(\'Vous confirmez ?\');" style="margin-left:15px">';
	$r.=HtmlInput::request_to_hidden(array("tab","gDossier","plugin_code","ac","pcode"));
	$r.=_(" Code TVA ");
	$r.=$text->input();
	$r.='<span id="tva_label" style="display:block"></span>';
	$r.=" Poste comptable (utilisez le % pour inclure les postes qui en dépendent)";
	$r.=$iposte->input();

	$r.='<span id="paccount_label" style="display:inline"></span>';
	$r.='<span style="display:block"></span>';
	$r.=HtmlInput::submit("save_addparam","Sauver");
	$r.=HtmlInput::button_close($ctl);
	$r.='</form>';
	break;
default:
  $r=var_export($_REQUEST,true);
}

$html=escape_xml($r);

header('Content-type: text/xml; charset=UTF-8');
echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<data>
<ctl>$ctl</ctl>
<code>$html</code>
<extra>$extra</extra>
</data>
EOF;
?>