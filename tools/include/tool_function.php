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
 * \brief contains functions for tools
 */
  /**
   *@brief check that the array jr_id is not empty
   *@return html error string or empty
   */
function check_jrid()
{
  if (isset($_POST['jr_id']))
    {
      if (empty($_POST['jr_id']))
	{
	  return "Erreur : aucune opération n'est sélectionnée";
	}
    }
  else
    return "Erreur : aucune opération n'est sélectionnée";

  return "";
}

  /**
   *@brief change the accounting in the selected operation
   *@param $cn database connx
   *@note use the $_POST variables
   * - sposte accounting source
   * - tposte accounting target
   * - jr_id[]
   */
function change_accounting(&$cn)
{
  $msg= check_jrid();
  if( $msg != "")
    {
      echo " <p class=\"error\">$msg</p>"; return;
    }
  $source=$cn->get_value('select pcm_val from tmp_pcmn where pcm_val=$1',array($_POST['sposte']));
  $target=$cn->get_value('select pcm_val from tmp_pcmn where pcm_val=$1',array($_POST['tposte']));
  if ( $source == '' || $target == '')
    {
      echo '<p class="error"> Il manque soit le poste comptable source  soit le poste comptable destination ou l\'un des deux postes n\'existe pas</p>';
      return;
    }
  $cn->prepare('update_account','update jrnx set j_poste = $1 where j_id in (select j_id from jrnx join jrn on (jr_grpt_id=j_grpt) and jr_id=$2) and j_poste=$3
		RETURNING j_id ');
  $cn->prepare('retrieve','select jr_date,jr_comment,jr_montant,jr_internal from jrn where jr_id=$1');
  echo h2info('Opération changée');
  echo 'compte : '.$_REQUEST['sposte'].' vers '.$_REQUEST['tposte'];
  $count=0;
  echo '<table class="result">';
  foreach ($_POST['jr_id'] as $id)
    {
      $update=$cn->execute('update_account',array(trim($_POST['tposte']),$id,trim($_POST['sposte'])));
      if ( Database::num_row($update) ==0 ) continue;
      $feedback=$cn->execute('retrieve',array($id));
      if ( Database::num_row($feedback) != 0)
	{
	  $count++;
	  $row=Database::fetch_array($feedback,0);
	  echo '<tr>';
	  echo td(format_date($row['jr_date']));
	  echo td(HtmlInput::detail_op($id,$row['jr_internal']));
	  echo td($row['jr_comment']);
	  echo td(nbm($row['jr_montant'],2,' class="num"'));
	  echo '</tr>';
	}
    }
  echo '</table>';
  echo '<p> Nombre d\'opérations changées :'.$count.'</p>';
}
 /**
   *@brief change the accounting in the selected operation by a card (with a possible different accounting
   *@param $cn database connx
   *@note use the $_POST variables
   * - csource1 accounting source
   * - ctarget1 card target
   * - jr_id[]
   */
function change_card_account(&$cn)
{
  $msg= check_jrid();
  if( $msg != "")
    {
      echo " <p class=\"error\">$msg</p>"; return;
    }
  $source=$cn->get_value('select pcm_val from tmp_pcmn where pcm_val=$1',array($_POST['csource1']));
  $target=$cn->get_value('select f_id from fiche_detail where ad_id=23 and ad_value=trim(upper($1))',array($_POST['ctarget1']));

  if ( $source == '' || $target == '')
    {
      echo '<p class="error"> Il manque soit le poste comptable source  soit la fiche  destination ou l\'un des deux n\'existe pas</p>';
      return;
    }

 // accounting target
	 $target_account=$cn->get_value('select pcm_val from tmp_pcmn where pcm_val in (select ad_value from fiche_detail where ad_id=5 and f_id=$1)',array($target));
	if  ($target_account == '') {
		echo '<p class="error"> Le poste comptable de la fiche n\'existe pas</span>';
		return;
	}
  $cn->prepare('update_account','update jrnx set j_poste = $1,j_qcode=upper($2),f_id=$3 where j_id in (select j_id from jrnx join jrn on (jr_grpt_id=j_grpt) and jr_id=$4) and j_poste=$5
		RETURNING j_id ');
  $cn->prepare('retrieve','select jr_date,jr_comment,jr_montant,jr_internal from jrn where jr_id=$1');
  echo h2info('Opération changée');
  echo 'compte : '.$_REQUEST['sposte'].' vers '.$_REQUEST['tposte'];
  $count=0;
  echo '<table class="result">';
  foreach ($_POST['jr_id'] as $id)
    {
      $update=$cn->execute('update_account',array(trim($target_account),trim($_POST['ctarget1']),$target,$id,trim($_POST['csource1'])));
      if ( Database::num_row($update) ==0 ) continue;
      $feedback=$cn->execute('retrieve',array($id));
      if ( Database::num_row($feedback) != 0)
	{
	  $count++;
	  $row=Database::fetch_array($feedback,0);
	  echo '<tr>';
	  echo td(format_date($row['jr_date']));
	  echo td(HtmlInput::detail_op($id,$row['jr_internal']));
	  echo td($row['jr_comment']);
	  echo td(nbm($row['jr_montant'],2,' class="num"'));
	  echo '</tr>';
	}
    }
  echo '</table>';
  echo '<p> Nombre d\'opérations changées :'.$count.'</p>';
}

  /**
   *@brief change the accounting and card in the selected operations
   *@param $cn database connx
   *@note use the $_POST variables
   * - csource accounting source
   * - ctarget accounting target
   * - jr_id[]
   */
function change_card(&$cn)
{
  $msg= check_jrid();
  if( $msg != "")
    {
      echo " <p class=\"error\">$msg</p>"; return;
    }
  $source=$cn->get_value('select f_id from fiche_detail where ad_id=23 and ad_value=trim(upper($1))',array($_POST['csource']));
  $target=$cn->get_value('select f_id from fiche_detail where ad_id=23 and ad_value=trim(upper($1))',array($_POST['ctarget']));

  if ( $source == '' || $target == '')
    {
      echo '<p class="error"> Il manque soit la fiche source  soit le fiche destination ou l\'une des deux fiches n\'existe pas</p>';
      return;
    }
  /*
   *retrieve info about card :  accounting
   */
  $source_account=$cn->get_value('select ad_value from fiche_detail where ad_id=5 and f_id=$1',array($source));
  $target_account=$cn->get_value('select ad_value from fiche_detail where ad_id=5 and f_id=$1',array($target));

  /*
   * test if accounting valid
   */
  if ( $source_account == '' || $target_account == '')
    {
      echo '<p class="error"> L\'une des deux fiche n\'a pas de poste comptable</p>';
      return;
    }

  /*
   *Prepare stmt
   */
  $cn->prepare('update_account','update jrnx set f_id = $1, j_poste=$2, j_qcode=trim(upper($3 ))
			where j_id in (select j_id from jrnx join jrn on (jr_grpt_id=j_grpt) and jr_id=$4) and j_qcode=upper(trim($5)) RETURNING j_id');
  /*
   * Change also in quant_sold
   */
  $cn->prepare('update_sold_mer','update quant_sold set qs_fiche=$1 where
		j_id in (select j_id from jrnx join jrn on (jr_grpt_id=j_grpt) and jr_id=$2) and qs_fiche=$3');

  $cn->prepare('update_sold_cust','update quant_sold set qs_client=$1 where
		j_id in (select j_id from jrnx join jrn on (jr_grpt_id=j_grpt) and jr_id=$2) and qs_client=$3');

  /*
   * Change also in  quant_purchase
   */
  $cn->prepare('update_pur_mer','update quant_purchase set qp_fiche=$1 where
		j_id in (select j_id from jrnx join jrn on (jr_grpt_id=j_grpt) and jr_id=$2) and qp_fiche=$3');

  $cn->prepare('update_pur_cust','update quant_purchase set qp_supplier=$1 where
		j_id in (select j_id from jrnx join jrn on (jr_grpt_id=j_grpt) and jr_id=$2) and qp_supplier=$3');
  /*
   * quant_fin
   */
  $cn->prepare('update_fin_bk','update quant_fin set qf_bank=$1 where
		jr_id=$2 and  qf_bank=$3');

  $cn->prepare('update_fin_oth','update quant_fin set qf_other=$1 where
		jr_id=$2 and  qf_other=$3');



  $cn->prepare('retrieve','select jr_date,jr_comment,jr_montant,jr_internal,jrn_def_type from jrn join jrn_def on (jr_def_id=jrn_def_id)  where jr_id=$1');
  echo h2info('Opération changée');
  echo 'Fiche : '.$_REQUEST['csource'].' vers '.$_REQUEST['ctarget'];
  $count=0;
  echo '<table class="result">';
  $cn->start();

  foreach ($_POST['jr_id'] as $id)
    {
      $feedback=$cn->execute('retrieve',array($id));
      $change_row=Database::num_row($feedback);
      if ($change_row  != 0)
	{
	  $row=Database::fetch_array($feedback,0);
	  switch ( $row['jrn_def_type'])
	    {
	    case 'VEN':
	      $cn->execute('update_sold_mer',array($target,$id,$source));
	      $cn->execute('update_sold_cust',array($target,$id,$source));
	      break;
	    case 'ACH':
	      $cn->execute('update_pur_mer',array($target,$id,$source));
	      $cn->execute('update_pur_cust',array($target,$id,$source));
	      break;
	    case 'FIN':
	      $cn->execute('update_fin_bk',array($target,$id,$source));
	      $cn->execute('update_fin_oth',array($target,$id,$source));
	      break;

	    }
	}

      $update=$cn->execute('update_account',array($target,$target_account,$_POST['ctarget'],$id,$_POST['csource']));

      if ( Database::num_row($update) ==0 ) continue;

      if ( $change_row != 0)
	{
	  $count++;

	  echo '<tr>';
	  echo td(format_date($row['jr_date']));
	  echo td(HtmlInput::detail_op($id,$row['jr_internal']));
	  echo td($row['jr_comment']);
	  echo td(nbm($row['jr_montant']),2,' class="num"');
	  echo '</tr>';
	}

    }
  echo '</table>';
  $cn->commit();
  echo '<p> Nombre d\'opérations changée :'.$count.'</p>';
}
  /**
   *@brief change the ledger for the selected operation
   *@param $cn database connx
   *@note use the $_POST variables
   * - csource accounting source
   * - ctarget accounting target
   * - jr_id[]
   */
function change_ledger(&$cn)
{
  $msg= check_jrid();
  if( $msg != "")
    {
      echo " <p class=\"error\">$msg</p>"; return;
    }
  /*
   * Type must be the same
   */
  $type_source=$cn->get_value('select jrn_def_type from jrn_def where jrn_def_id=$1',array($_POST['sledger']));
  $type_target=$cn->get_value('select jrn_def_type from jrn_def where jrn_def_id=$1',array($_POST['tledger']));

  if ( $type_target != $type_source)
    {
      echo " <p class=\"error\">Les journaux doivent être de même type</p>"; return;
    }
  $cn->prepare('update_ledger_x','update jrnx set j_jrn_def = $1 where j_id in (select j_id from jrnx join jrn on (jr_grpt_id=j_grpt) and jr_id=$2) and j_jrn_def=$3
		RETURNING j_id ');
  $cn->prepare('update_ledger','update jrn set jr_def_id = $1 where jr_id = $2 and  jr_def_id=$3
		RETURNING jr_id ');

  $cn->prepare('retrieve','select jr_date,jr_comment,jr_montant,jr_internal from jrn where jr_id=$1');
  echo h2info('Opération changée');
  $source_name=$cn->get_value("select '('||jrn_def_type||') '||jrn_def_name from jrn_def where
				jrn_def_id=$1",array($_POST['sledger']));

  $target_name=$cn->get_value("select '('||jrn_def_type||') '||jrn_def_name from jrn_def where
				jrn_def_id=$1",array($_POST['tledger']));


  echo 'compte : '.$source_name.' vers '.$target_name;
  $count=0;
  echo '<table class="result">';
  $cn->start();
  foreach ($_POST['jr_id'] as $id)
    {
      $update=$cn->execute('update_ledger_x',array(trim($_POST['tledger']),$id,trim($_POST['sledger'])));
      $cn->execute('update_ledger',array(trim($_POST['tledger']),$id,trim($_POST['sledger'])));

      if ( Database::num_row($update) ==0 ) continue;
      $feedback=$cn->execute('retrieve',array($id));
      if ( Database::num_row($feedback) != 0)
	{
	  $count++;
	  $row=Database::fetch_array($feedback,0);
	  echo '<tr>';
	  echo td(format_date($row['jr_date']));
	  echo td(HtmlInput::detail_op($id,$row['jr_internal']));
	  echo td($row['jr_comment']);
	  echo td(nbm($row['jr_montant'],2,' class="num"'));
	  echo '</tr>';
	}
    }
  echo '</table>';
  $cn->commit();

  echo '<p> Nombre d\'opérations changée'.$count.'</p>';

}

/**
 *@brief display search box
 *@param $cn database connx
 */
function display_search_receipt(&$cn)
{
  $idate_start=new IDate('dstart');
  $idate_start->value=HtmlInput::default_value('dstart','',$_GET);
  $idate_end=new IDate('dend');
  $idate_end->value=HtmlInput::default_value('dend','',$_GET);

  $sql="select jrn_def_id, '('||jrn_def_type||') '||jrn_def_name from jrn_def order by jrn_def_name asc";
  $array=$cn->make_array($sql);
  $iledger=new ISelect('ledger');
  $iledger->value=$array;
  $iledger->selected=HtmlInput::default_value('ledger','',$_GET);

  $hidden=HtmlInput::get_to_hidden(array('sa','plugin_code','gDossier'));

  $submit=HtmlInput::submit('search','Rechercher');
  $hide=  HtmlInput::button('accounting_hide_bt','Annuler','onclick="$(\'div_receipt\').hide();"');
  require_once('template/search_box.php');
}
/**
 *@brief display result of search receipt
 *@param $cn database connx
 *@note from $_REQUEST retrieve dstart,dend and ledger
 */
function display_result_receipt(&$cn)
{
  $sql="select jr_id,jr_internal,to_char(jr_date,'DD.MM.YY') as str_date, jr_date,jr_montant,jr_comment,jr_pj_number
	from jrn
	where
	jr_def_id=$1 and jr_date >= to_date($2,'DD.MM.YYYY')
	and jr_date <= to_date($3,'DD.MM.YYYY')
	 order by jr_date asc,substring(jr_pj_number,'[0-9]+$')::numeric asc
	";
  $ret=$cn->exec_sql($sql,array($_GET['ledger'],$_GET['dstart'],$_GET['dend']));
  $nb_row=Database::num_row($ret);
  $checkbox=new ICheckBox('jr_id[]');

  require_once('template/result_receipt.php');
}
function display_download_receipt()
{
    echo HtmlInput::submit('download_receipt','Télécharger',' onclick="$(\'act\').value=\'download\'" ');
}
/**
 *@brief display the prefix + from number
 *@note use the variable from $_GET
 */
function display_numb_receipt()
{
  $prefix=new IText('prefix');
  $number=new INum('number');
  $submit=HtmlInput::submit('chg_receipt','Valider',' onclick="$(\'act\').value=\'numbering\'"  ');
  $hidden=HtmlInput::get_to_hidden(array('ledger','dend','dstart'));
  $with_step=new ISelect('istep');
  $with_step->value=array(
			  array('value'=>1,'label'=>'Non'),
			  array('value'=>2,'label'=>'Oui')
			 );

  require_once('template/numbering.php');
}
/**
 *@brief change the number of receipt
 *@param $cn database connx
 */
function change_receipt(&$cn)
{
    $msg=check_jrid();
    if ($msg!="")
    {
        echo " <p class=\"error\">$msg</p>";
        return;
    }
    $cn->prepare('update_receipt', 'update jrn set jr_pj_number=$1 where jr_id =$2');
    $start=$_POST['number'];
    $nb=1;
    if (trim($start)==""||$_POST['istep']==2)
        $nb=0;

    $cn->start();
    foreach ($_POST['jr_id'] as $id)
    {
        if ($nb==1)
            $pj=trim($_POST['prefix']).sprintf("%d", $start);
        else
            $pj=trim($_POST['prefix']);
        if (trim($pj)=='')
            $pj=null;

        $result_update=$cn->execute('update_receipt', array($pj, $id));
        if ($nb==1)
            $start++;
    }
    $cn->commit();
}

/**
 * Download all receipt
 * @param type $cn
 */
function download_receipt(Database &$cn)
{
    $dirname=tempnam($_ENV['TMP'], 'receipt');
    unlink($dirname);
    mkdir($dirname);
    $cn->start();
    $feedback=array();
    $jr_id  = HtmlInput::default_value_post('jr_id',array());
    if (empty ($jr_id ) ) {
        echo '<div class="notice">'._('Aucune opération sélectionnée').'</div>';
        return;
    }
//--- take all the invoice
    foreach ( $_POST['jr_id'] as $value)
    {
        $a_invoice=$cn->get_array("select jr_pj_name,jr_pj from jrn where jr_id = $1", array($value));
        $invoice=$a_invoice[0];
        if ($invoice['jr_pj_name']!=""&&$invoice['jr_pj']!="")
        {
            $file=$dirname.'/'.$invoice['jr_pj_name'];
            $cn->lo_export($invoice['jr_pj'], $file);
            $feedback[]=_('Ajout pièce ').$invoice['jr_pj_name'];
        }
    }
// -- zip file
    $date=date('ymd.Hi');
    $zip_file=$_ENV['TMP']."/"."receipt-".$date.".zip";

// --- create the zip
    $zip=new Zip_Extended();
    $res=$zip->open($zip_file, ZipArchive::CREATE);
    if ($res!=true)
    {
        die("Cannot create zip file");
    }
    $zip->add_recurse_folder($dirname."/");
    $zip->close();
//-- send the zip
    $link=http_build_query(array('gDossier'=>Dossier::id(), 'ac'=>$_REQUEST['ac'], 'plugin_code'=>$_REQUEST['plugin_code'],'act'=>'download_receipt', 'file'=>basename($zip_file)));
    ?>
    <p>
    <h2>
        <?php echo _('Pièce'); ?>
    </h2>
    <ol>
        <?php foreach ($feedback as $row): ?>

                <li>
                  <?php echo $row ?>
                </li>
    <?php endforeach; ?>
    </ol>
    </p>
    <p>
    <a class="button" style="display:inline;" href="extension.raw.php?<?php echo $link; ?>"> <?php echo _('Télécharger le fichier') ?></a>
    </p>
    <?php
}