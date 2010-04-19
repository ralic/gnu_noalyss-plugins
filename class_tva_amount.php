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

/*!\file
 * \brief
 */


/*!\brief 
 *
 *
 */
class Tva_Amount
{

  private static $variable=array('amount'=>'amount',
				 'amount_tva'=>'amount_tva',
				 'param'=>'param',
				 'dir'=>'dir',
				 'start_periode'=>'start_periode',
				 'end_periode'=>'end_periode',
				 'grid'=>'grid'
				 );
  function __construct ($p_init,$p_dir,$p_start_periode,$p_end_periode) {
    $this->db=$p_init;
    $this->start_periode=$p_start_periode;
    $this->end_periode=$p_end_periode;
    $this->dir=$p_dir;
  }
  public function get_parameter($p_string) {
    if ( array_key_exists($p_string,self::$variable) ) {
      $idx=self::$variable[$p_string];
      return $this->$idx;
    }
    else 
      exit (__FILE__.":".__LINE__.'Erreur attribut inexistant');
  }
  public function set_parameter($p_string,$p_value) {
    if ( array_key_exists($p_string,self::$variable) ) {
      $idx=self::$variable[$p_string];
      $this->$idx=$p_value;
    }
    else 
      exit (__FILE__.":".__LINE__.'Erreur attribut inexistant');
    
    
  }
  public function get_info() {    return var_export(self::$variable,true);  }
  public function verify() {
    // Verify that the elt we want to add is correct
  }
  /**
   *@brief load parameter and set param to a object to tva_parameter
   */
  public function load_parameter() {
    // first get the vat code
     $ctva=new Tva_Parameter($this->db);
     $ctva->set_parameter('code',$this->grid);
     if ( $ctva->load() == -1 ) {
       throw new Exception (_("code $this->grid non trouvé"));
     }    
     $this->param=$ctva;
  }
  /**
   *@brief get the amount of vat thanks its code
   *@param $p_gril is the gril code
   *@param $p_dir is out or in in for the table quant_purchase and
   * out for the table quant_sold
   *@return a number
   */
  function amount_operation() {
    // get the VAT code
    $this->load_parameter();
    $result=0;
    // filter on accounting
    $vat_code=$this->param->get_parameter('value');
    $account=$this->param->get_parameter('account');
    // if vat code contains a "," then split it and recall get_amount
    if ( strpos($vat_code,',') == true ) {
      $aVat_code=split(',',$vat_code);
    }else {
      $aVat_code=array($vat_code);
    }
    if (strpos($account,',') == true) 
      $aAccount=split(',',$account);
    else
      $aAccount=array($account);
    for ($i=0;$i<count($aVat_code);$i++) {
      for ($j=0;$j<count($aAccount);$j++)
	$result+=round($this->get_amount_filter($aVat_code[$i],$aAccount[$j]),2);
    }
    return round($result,2);


  }
  function amount_vat() {
    // get the VAT code
    $this->load_parameter();
    $result=0;
    // filter on accounting
    $vat_code=$this->param->get_parameter('value');
    $account=$this->param->get_parameter('account');
    // if vat code contains a "," then split it and recall get_amount
    if ( strpos($vat_code,',') == true ) {
      $aVat_code=split(',',$vat_code);
    }else {
      $aVat_code=array($vat_code);
    }
    if (strpos($account,',') == true) 
      $aAccount=split(',',$account);
    else
      $aAccount=array($account);
    for ($i=0;$i<count($aVat_code);$i++) {
      for ($j=0;$j<count($aAccount);$j++)
	$result+=round($this->get_vat_filter($aVat_code[$i],$aAccount[$j]),2);
    }
    return round($result,2);


  }

  /**
   *@brief get the amount of operation from the table quant_sold or quant_purchase
   *@return amount
   */
  private function get_amount_filter($p_code,$p_account) {
    if ( $this->dir == 'out' && trim($p_account) !='' && trim($p_code) !='' ) {
      $sql="select coalesce(sum(qs_price),0) as amount from quant_sold join jrnx using (j_id)
           where qs_vat_code=$1 and  (j_date >= to_date($2,'DD.MM.YYYY') and j_date <= to_date($3,'DD.MM.YYYY'))
           and j_poste::text like ($4)";
      $res=$this->db->get_array($sql,array($p_code,
					   $this->start_periode,
					   $this->end_periode,
					   $p_account));
      return $res[0]['amount'];      
    }
    if ( $this->dir == 'out' && trim($p_account) ==''  && trim($p_code) !='' ) {
      $sql="select coalesce(sum(qs_price),0) as amount from quant_sold join jrnx using (j_id)
           where qs_vat_code=$1 and  (j_date >= to_date($2,'DD.MM.YYYY') and j_date <= to_date($3,'DD.MM.YYYY'))
           ";
      $res=$this->db->get_array($sql,array($p_code,
					   $this->start_periode,
					   $this->end_periode));

			     
      return $res[0]['amount'];
    }

    if ( $this->dir == 'in' && trim($p_account) !='' && trim($p_code) !='' ) {
      $sql="select coalesce(sum(qp_price),0) as amount from quant_purchase join jrnx using (j_id)
           where qp_vat_code=$1 and  (j_date >= to_date($2,'DD.MM.YYYY') and j_date <= to_date($3,'DD.MM.YYYY'))
           and j_poste::text like ($4)";
      $res=$this->db->get_array($sql,array($p_code,
					   $this->start_periode,
					   $this->end_periode,
					   $p_account));
      return $res[0]['amount'];      
    }
    if ( $this->dir == 'in' && trim($p_account) ==''  && trim($p_code) !='' ) {
      $sql="select coalesce(sum(qp_price),0) as amount from quant_purchase join jrnx using (j_id)
           where qp_vat_code=$1 and  (j_date >= to_date($2,'DD.MM.YYYY') and j_date <= to_date($3,'DD.MM.YYYY'))
           ";
      $res=$this->db->get_array($sql,array($p_code,
					   $this->start_periode,
					   $this->end_periode));

			     
      return $res[0]['amount'];
    }


    return 0;
  }

  /**
   *@brief get the amount of VAT from the table quant_sold or quant_purchase
   *@return amount
   */
  private function get_vat_filter($p_code,$p_account) {
    if ( $this->dir == 'out' && trim($p_account) !='' && trim($p_code) !='' ) {
      $sql="select coalesce(sum(qs_vat),0) as amount from quant_sold join jrnx using (j_id)
           where qs_vat_code=$1 and  (j_date >= to_date($2,'DD.MM.YYYY') and j_date <= to_date($3,'DD.MM.YYYY'))
           and j_poste::text like ($4)";
      $res=$this->db->get_array($sql,array($p_code,
					   $this->start_periode,
					   $this->end_periode,
					   $p_account));
      return $res[0]['amount'];      
    }
    if ( $this->dir == 'out' && trim($p_account) ==''  && trim($p_code) !='' ) {
      $sql="select coalesce(sum(qs_vat),0) as amount from quant_sold join jrnx using (j_id)
           where qs_vat_code=$1 and  (j_date >= to_date($2,'DD.MM.YYYY') and j_date <= to_date($3,'DD.MM.YYYY'))
           ";
      $res=$this->db->get_array($sql,array($p_code,
					   $this->start_periode,
					   $this->end_periode));

			     
      return $res[0]['amount'];
    }

    if ( $this->dir == 'in' && trim($p_account) !='' && trim($p_code) !='' ) {
      $sql="select coalesce(sum(qp_vat),0) as amount from quant_purchase join jrnx using (j_id)
           where qp_vat_code=$1 and  (j_date >= to_date($2,'DD.MM.YYYY') and j_date <= to_date($3,'DD.MM.YYYY'))
           and j_poste::text like ($4)";
      $res=$this->db->get_array($sql,array($p_code,
					   $this->start_periode,
					   $this->end_periode,
					   $p_account));
      return $res[0]['amount'];      
    }
    if ( $this->dir == 'in' && trim($p_account) ==''  && trim($p_code) !='' ) {
      $sql="select coalesce(sum(qp_vat),0) as amount from quant_purchase join jrnx using (j_id)
           where qp_vat_code=$1 and  (j_date >= to_date($2,'DD.MM.YYYY') and j_date <= to_date($3,'DD.MM.YYYY'))
           ";
      $res=$this->db->get_array($sql,array($p_code,
					   $this->start_periode,
					   $this->end_periode));

			     
      return $res[0]['amount'];
    }


    return 0;
  }



  /*!\brief
   *\param
   *\return
   *\note
   *\see
   *\todo
   */	
  static function test_me() {
  }
  
}

/* test::test_me(); */