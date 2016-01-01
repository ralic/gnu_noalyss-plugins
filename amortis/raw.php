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
 * \brief raw file for PDF
 */
require_once('amortis_constant.php');

extract ($_REQUEST);

/* export all cards in PDF */
if ( isset($_REQUEST['pdf_all']))
  {
    require_once('include/class_pdf_card.php');
    global $cn;
    $a=new Pdf_Card($cn);
    $a->setDossierInfo(dossier::id());
    $a->AliasNbPages('{nb}');
    $a->AddPage();
    $a->export();
    exit();
  }
/*
 * Export the list per year in CSV
 */
if ( isset ($_REQUEST['csv_list_year']))
{
    require_once NOALYSS_INCLUDE.'/lib/class_noalyss_csv.php';
    
    $year=HtmlInput::default_value_request('csv_list_year','0000');

    $csv_list_year=new Noalyss_Csv("amortissement-$year");
    $csv_list_year->send_header();
    $csv_list_year->write_header(array(_('Code'),
                                _('Description'),
                                _('Date acquisition'),
                                _('Année Achat'),
                                _('Montant Achat'),
                                _('Nombre annuités'),
                                _("Montant à amortir"),
                                _('Amortissement'),
                                _('Restant')));
    
  
  $sql="select * from amortissement.amortissement where a_id
         in (select a_id from amortissement.amortissement_detail where ad_year=$1)";
  $array=$cn->get_array($sql,array($year));
  bcscale(2);
  for ($i=0;$i<count($array);$i++)
    {
        $fiche=new fiche($cn,$array[$i]['f_id']);
        $remain=$cn->get_value("select coalesce(sum(ad_amount),0) from amortissement.amortissement_detail
                          where a_id=$1 and ad_year >= $2",
                               array($array[$i]['a_id'],$year));
        $amortize=$cn->get_value("select ad_amount from amortissement.amortissement_detail
                          where a_id=$1 and ad_year = $2",
                                 array($array[$i]['a_id'],$year));
        $toamortize=bcsub($remain,$amortize);


        $csv_list_year->add($fiche->strAttribut(ATTR_DEF_QUICKCODE));
        $csv_list_year->add($fiche->strAttribut(ATTR_DEF_NAME));
        $csv_list_year->add(format_date($array[$i]['a_date']));
        $csv_list_year->add($array[$i]['a_start']);
        $csv_list_year->add($array[$i]['a_amount'],'number');
        $csv_list_year->add($array[$i]['a_nb_year'],'number');
        $csv_list_year->add($remain,'number');
        $csv_list_year->add($amortize,'number');
        $csv_list_year->add($toamortize,'number');
        
        $csv_list_year->write();
	     
    }
}

/* export all cards in PDF */
if ( isset($_REQUEST['pdf_list_year']))
  {
    require_once('include/class_amortissement_table_pdf.php');
    global $cn;
    $year=$_REQUEST['pdf_list_year'];
    $a=new Amortissement_Table_PDF($cn);
    $a->SetTitle('Amortissement '.$year);
    $a->SetAuthor('NOALYSS');
    $a->SetCreator('NOALYSS');
    $a->year=$year;
    $a->setDossierInfo(dossier::id());
    $a->AliasNbPages('{nb}');
    $a->AddPage();
    $a->export();
    exit();
  }
/*
 * Export to CSV all the listing
 */
if ( isset($_GET['csv_material']))
  {
    $name="listing-material";
    header('Pragma: public');
    header('Content-type: application/csv');
    header('Content-Disposition: attachment;filename="'.$name.'.csv"',FALSE);
    
    $ret=$cn->get_array("select * from amortissement.v_amortissement_summary order by a_start,a_date");
    printf ("\"Visible\";\"qcode\";\"Nom\";\"Description\";\"Date acquisition\";\"Année Achat\";\"Nbre annuité\";\"Poste Charge\";\"Poste amortis\";\"Fiche de charge\"; \"Fiche amortissement acté\";\"Montant achat\";\"Montant amorti\";\"Montant a amortir\"\r\n");
    for ($i=0;$i<count($ret);$i++)
      {
	printf('"%s";',$ret[$i]['a_visible']);
	printf('"%s";',$ret [$i]['quick_code']);
	printf('"%s";',$ret [$i]['vw_name']);
	printf('"%s";',$ret [$i]['vw_description']);
	printf('"%s";',format_date($ret[$i]['a_date']));
	printf('"%s";',$ret[$i]['a_start']);
	printf('%s;',nb($ret[$i]['a_nb_year']));
	printf('"%s";',$ret[$i]['account_deb']);
	printf('"%s";',$ret[$i]['account_cred']);
	printf('"%s";',$ret[$i]['card_cred_qcode']);
	printf('"%s";',$ret[$i]['card_deb_qcode']);
	printf('%s;',nb($ret[$i]['a_amount']));
	printf("%s;",nb($ret[$i]['amort_done']));
	$remain=bcsub($ret[$i]['a_amount'],$ret[$i]['amort_done']);
	printf("%s",nb($remain));
	printf("\r\n");
      }
  }
/*
 * Export to PDF all the listing
 */
if ( isset($_GET['pdf_material']))
  {
    require_once('include/class_amortissement_material_pdf.php');
    global $cn;
    $a=new Amortissement_Material_PDF($cn);
    $a->SetTitle('Amortissement ');
    $a->SetAuthor('NOALYSS');
    $a->SetCreator('NOALYSS');
    $a->setDossierInfo(dossier::id());
    $a->AliasNbPages('{nb}');
    $a->AddPage();
    $a->export();
    exit();
    
   
  }


?>