<?php
/*
 *   This file is part of NOALYSS.
 *
 *   NOALYSS isfree software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   NOALYSS isdistributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with NOALYSS; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
// Copyright (2015) Author Dany De Bontridder <dany@alchimerys.be>

require_once 'class_sav_repair_card_sql.php';
require_once 'class_sav_spare_part_sql.php';

/**
 * @file
 * @brief 
 * @param type $name Descriptionara
 */
class Sav_Spare_Part
{
    private $spare_part;
    function __construct($p_id=-1)
    {
        $this->spare_part=new Sav_Spare_Part_SQL($p_id);
    }
    function display_list($p_repair_card)
    {
        global $cn;
        bcscale(2);
        $sql='select id
                from service_after_sale.sav_spare_part  as sp1
                where 
                sp1.repair_card_id=$1
                ';
        $a_spare=$cn->get_array($sql,array($p_repair_card));
        $count_spare=count($a_spare);
        require 'template/spare_part_display_list.php';
    }
    /**
     * Display a row for a table
     * @global $cn database conx
     * @global $gDossier dossier id
     * @global $ac access code
     * @global $plugin_code
     * @return string
     */
    function print_row()
    {
        global $cn,$gDossier,$ac,$plugin_code;

        // Material
        $qcode=$this->get_qcode();
        $name=$this->get_name();
        
        // Javascript
        $js=sprintf('spare_part_remove(\'%s\',\'%s\',\'%s\',\'%s\')',
                       $gDossier,$ac,$plugin_code,$this->spare_part->id);
        
        // template
        ob_start();
        require 'template/sas_spare_part_print_row.php';
        $result=ob_get_clean();
        return $result;
    }
   /**
    * Attach a spare_part with a Repair Card
    * @global $cn database conx
    * @param integer repair card id
    * @param $qcode of the spare part 
    * @param $p_quant quantity of spare part
    * @throws Exception
    */
    function repair_card_add($p_repair,$p_qcode,$p_quant)
    {
        global $cn;
        $repair=new Sav_Repair_Card_SQL($p_repair);
        if ( $repair->id == -1 ) throw new Exception(_('Pièce inexistante'),NOMATERIAL);
       
        $fiche=new Fiche($cn);
        $fiche->get_by_qcode($p_qcode, FALSE);
        $material_id=$fiche->id;
        /**
         * @todo vérifier que la carte demandée appartient bien  à la catégorie
         * de fiche
         */
        if ( $material_id == 0) throw new Exception (_('Pièce inexistante'),NOSPAREPART);
        
        $this->spare_part->repair_card_id=$p_repair;
        $this->spare_part->id=-1;
        $this->spare_part->quantity=$p_quant;
        $this->spare_part->f_id_material=$material_id;
        $this->spare_part->save();
        
    }
    /**
     * Remove a spare_card from a repair_card
     */
    function remove()
    {
        $this->spare_part->delete();
    }
    /**
     * return the spare_part id
     * @return integer
     */
    function get_id()
    {
        return $this->spare_part->id;
    }
    /**
     * Return name of the spare_part
     * @global  $cn database conx
     * @return string
     */
    function get_name()
    {
        global $cn;
        $fiche = new Fiche($cn,$this->spare_part->f_id_material);
        return $fiche->strAttribut(ATTR_DEF_NAME);
        
    }
    /**
     * Return quick_code of the spare_part
     * @global $cn database conx
     * @return string
     */
    function get_qcode()
    {
        global $cn;
        $fiche = new Fiche($cn,$this->spare_part->f_id_material);
        return $fiche->strAttribut(ATTR_DEF_QUICKCODE);
    }
}
?>
