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
 * \brief operation for modify operation plugin
 */

class Modop_Operation
{
    function __construct($p_cn,$p_id)
    {
        $this->db=$p_cn;
        $this->jr_id=trim($p_id);
        $this->array=array();
    }
    /**
     *@brief retrieve data from jrnx, jrn, quant_xxx and format them to
     * usable for the acc_leger input function
     *@see Acc_Ledger::input(),Acc_Ledger_Purchase::input(),Acc_Ledger_Sold::input(),
     */
    function format()
    {
        /*  check if we retrieve it */
        $this->ledger_type=$this->db->get_value("select jrn_def_type from jrn_def ".
                                                " where jrn_def_id = (select jr_def_id from jrn where jr_id=$1)",
                                                array($this->jr_id));
        if ($this->ledger_type=='')
            throw new Exception('Operation non trouvée');
        /* ---------------------------------------------------------------------- */
        // PURCHASE
        /* ---------------------------------------------------------------------- */
        if ( $this->ledger_type=='ACH')
        {
            $this->array['e_mp']=0;
            $this->array['jrn_type']='ACH';
            $jrn=$this->db->get_array("select jr_id,to_char(jr_date,'DD.MM.YYYY') as date_fmt,".
                                      " to_char(jrn_ech,'DD.MM.YYYY') as ech_fmt,jr_comment,jr_pj_number,
										  jr_tech_per,jr_Def_id,jr_internal ".
                                      " from jrn where jr_id=$1",
                                      array($this->jr_id));
            $this->jr_id=$jrn[0]['jr_id'];
			$this->jr_internal=$jrn[0]['jr_internal'];
            /*  retrieve from jrn */
            $this->array['e_ech']=$jrn[0]['ech_fmt'];
            $this->array['e_date']=$jrn[0]['date_fmt'];
            $this->array['e_comm']=$jrn[0]['jr_comment'];
            $this->array['e_pj']=$jrn[0]['jr_pj_number'];
            $this->array['p_jrn']=$jrn[0]['jr_def_id'];
            $this->array['periode']=$jrn[0]['jr_tech_per'];

            /* retrieve from jrn_info */
            $this->array['bon_comm']=$this->db->get_value("select ji_value from jrn_info where jr_id=$1 and id_type='BON_COMMANDE'",
                                     array($this->jr_id));
            $this->array['other_info']=$this->db->get_value("select ji_value from jrn_info where jr_id=$1 and id_type='OTHER'",
                                       array($this->jr_id));
            /* retrieve from quant_purchase */
            $qp=$this->db->get_array("select * from quant_purchase where j_id in (select j_id from jrnx join jrn on (j_grpt = jr_grpt_id) where ".
                                     " jr_id=$1)",array($this->jr_id));
            /* check if "quick writing" was  used */
            if ( count($qp) == 0)
                throw new Exception('Désolé cette opération a été faite en écriture directe');

            /* customer */
            $fcard=new Fiche($this->db,$qp[0]['qp_supplier']);
            $this->array['e_client']=$fcard->get_quick_code();
            /* for each item */
            for ($e=0; $e<count($qp); $e++)
            {
                $fcard=new Fiche($this->db,$qp[$e]['qp_fiche']);
                $this->array['e_march'.$e]=$fcard->get_quick_code();;
                $this->array['e_march'.$e.'_price']=round($qp[$e]['qp_price']/$qp[$e]['qp_quantite'],2);
                $this->array['e_march'.$e.'_tva_amount']=$qp[$e]['qp_vat'];
                $this->array['e_march'.$e.'_tva_id']=$qp[$e]['qp_vat_code'];
                $this->array['e_quant'.$e]=$qp[$e]['qp_quantite'];


            } // for each item
            $this->array['nb_item']=(count($qp)<MAX_ARTICLE)?MAX_ARTICLE:count($qp);
        } // ledger ACH
        /* ---------------------------------------------------------------------- */
        // VEN
        /* ---------------------------------------------------------------------- */
        if ( $this->ledger_type=='VEN')
        {
            $this->array['e_mp']=0;
            $this->array['jrn_type']='VEN';
            $jrn=$this->db->get_array("select jr_id,
						to_char(jr_date,'DD.MM.YYYY') as date_fmt,
						to_char(jrn_ech,'DD.MM.YYYY') as ech_fmt,
						jr_comment,jr_pj_number,
						jr_tech_per,jr_Def_id,jr_internal
						from jrn where jr_id=$1",
                                      array($this->jr_id));
            $this->jr_id=$jrn[0]['jr_id'];
            $this->jr_internal=$jrn[0]['jr_internal'];
            /*  retrieve from jrn */
            $this->array['e_ech']=$jrn[0]['ech_fmt'];
            $this->array['e_date']=$jrn[0]['date_fmt'];
            $this->array['e_comm']=$jrn[0]['jr_comment'];
            $this->array['e_pj']=$jrn[0]['jr_pj_number'];
            $this->array['p_jrn']=$jrn[0]['jr_def_id'];
            $this->array['periode']=$jrn[0]['jr_tech_per'];

            /* retrieve from jrn_info */
            $this->array['bon_comm']=$this->db->get_value("select ji_value from jrn_info where jr_id=$1 and id_type='BON_COMMANDE'",
                                     array($this->jr_id));
            $this->array['other_info']=$this->db->get_value("select ji_value from jrn_info where jr_id=$1 and id_type='OTHER'",
                                       array($this->jr_id));
            /* retrieve from quant_purchase */
            $qp=$this->db->get_array("select * from quant_sold where j_id in (select j_id from jrnx join jrn on (j_grpt = jr_grpt_id) where ".
                                     " jr_id=$1)",array($this->jr_id));
            if ( count($qp) == 0)
                throw new Exception('Désolé cette opération a été faite en écriture directe');
            /* customer */
            $fcard=new Fiche($this->db,$qp[0]['qs_client']);
            $this->array['e_client']=$fcard->get_quick_code();


            /* for each item */
            for ($e=0; $e<count($qp); $e++)
            {
                $fcard=new Fiche($this->db,$qp[$e]['qs_fiche']);
                $this->array['e_march'.$e]=$fcard->get_quick_code();;
                $this->array['e_march'.$e.'_price']=round($qp[$e]['qs_price']/$qp[$e]['qs_quantite'],2);
                $this->array['e_march'.$e.'_tva_amount']=$qp[$e]['qs_vat'];
                $this->array['e_march'.$e.'_tva_id']=$qp[$e]['qs_vat_code'];
                $this->array['e_quant'.$e]=$qp[$e]['qs_quantite'];


            } // for each item
            $this->array['nb_item']=(count($qp)<MAX_ARTICLE)?MAX_ARTICLE:count($qp);

        } // ledger VEN
        /* ---------------------------------------------------------------------- */
        // MISC
        /* ---------------------------------------------------------------------- */
        if ( $this->ledger_type=='ODS')
        {
            $this->array['e_mp']=0;
            $this->array['jrn_type']='ODS';
            $jrn=$this->db->get_array("select jr_id,jr_internal,to_char(jr_date,'DD.MM.YYYY') as date_fmt,jr_comment,jr_pj_number, jr_tech_per,jr_Def_id from jrn where jr_id=$1",
                                      array($this->jr_id));
            $this->jr_id=$jrn[0]['jr_id'];
            $this->jr_internal=$jrn[0]['jr_internal'];
            /*  retrieve from jrn */
            $this->array['e_date']=$jrn[0]['date_fmt'];
            $this->array['desc']=$jrn[0]['jr_comment'];
            $this->array['e_pj']=$jrn[0]['jr_pj_number'];
            $this->array['p_jrn']=$jrn[0]['jr_def_id'];
            $this->array['periode']=$jrn[0]['jr_tech_per'];
            $ods=$this->db->get_array('select j_qcode,j_poste,j_text,j_montant,j_debit from jrnx where j_grpt = (select jr_grpt_id from jrn where jr_id=$1)',
                                      array($this->jr_id));
            for ($e=0; $e<count($ods); $e++)
            {
                $this->array['qc_'.$e]=$ods[$e]['j_qcode'];
                $this->array['poste'.$e]=(trim($ods[$e]['j_qcode'])=='')?$ods[$e]['j_poste']:'';
                if ( $ods[$e]['j_debit']=='t' )
                    $this->array['ck'.$e]=true;
                $this->array['amount'.$e]=$ods[$e]['j_montant'];
                $this->array['ld'.$e]=$ods[$e]['j_text'];
            }
            $this->array['nb_item']=(count($ods)<MAX_ARTICLE)?MAX_ARTICLE:count($ods); 

        } // ledger MISC
///////////////////////////////////////////////////////////////////////////
// FIN
///////////////////////////////////////////////////////////////////////////
        if ( $this->ledger_type=="FIN")
        {
            $jrn=$this->db->get_array("select jr_id,to_char(jr_date,'DD.MM.YYYY') as date_fmt,to_char(jrn_ech,'DD.MM.YYYY') as ech_fmt,jr_comment,jr_pj_number, jr_tech_per,jr_Def_id from jrn where jr_id=$1",
                                      array($this->jr_id));
            /*  retrieve from jrn */
            $this->jr_id=$jrn[0]['jr_id'];
            $this->array['e_date']=$jrn[0]['date_fmt'];
            $this->array['e_comm']=$jrn[0]['jr_comment'];
            $this->array['e_pj']=$jrn[0]['jr_pj_number'];
            $this->array['p_jrn']=$jrn[0]['jr_def_id'];
            $this->array['period']=$jrn[0]['jr_tech_per'];
            /* retrieve from quant_purchase */
            $qp=$this->db->get_array("select * from quant_fin  where jr_id =$1",array($this->jr_id));
            /* check if "quick writing" was  used */
            if ( count($qp) == 0)
                throw new Exception('Désolé cette opération ne peut être corrigée');
            /* bank */
            $fcard=new Fiche($this->db,$qp[0]['qf_bank']);
            $this->array['e_bank']=$fcard->get_quick_code();
            /* other */
            $fcard=new Fiche($this->db,$qp[0]['qf_other']);
            $this->array['e_other']=$fcard->get_quick_code();
            $this->array['e_amount']=$qp[0]['qf_amount'];
        }
    } // end function format()
    /**
      *@brief deactivate the strict mode
      */
    function suspend_strict()
    {
        global $g_parameter;
        $g_parameter->MY_STRICT='N';
       $this->strict=false;
    }
    /**
     *@brief activate strict mode, only if $this->strict=true
     * @deprecated
     *@see suspend_receipt
     */
    function activate_strict()
    {
        if ($this->strict==true)
        {
            $owner=new Own($this->db);
            $owner->MY_STRICT='Y';
            $owner->save('MY_STRICT');
        }
    }
    /**
      *@brief deactivate the suggest mode for the receipt number, if
      */
    function suspend_receipt()
    {
        global $g_parameter;
        $g_parameter->MY_PJ_SUGGEST='N';
        $this->toggle=false;
    }
    /**
     *@brief activate receipt, only if $this->toggle=true
     *@deprecated
     *@see suspend_receipt
     */
    function activate_receipt()
    {
        if ($this->toggle==true)
        {
            $owner=new Own($this->db);
            $owner->MY_PJ_SUGGEST='Y';
            $owner->save('MY_PJ_SUGGEST');
        }
    }
   
}
