<?php

/*
 * Copyright (C) 2016 Dany De Bontridder <dany@alchimerys.be>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */


/* * *
  //!@file
  ///Insert into the the table impacc.import_detail + status
 *
 */
require_once DIR_IMPORT_ACCOUNT."/include/class_impacc_csv.php";
require_once DIR_IMPORT_ACCOUNT."/include/class_impacc_verify.php";

///Manage one row of operation of Sale / Purchase for importing them
abstract class Impacc_Csv_Sale_Purchase
{

    ///Works by id_group_code : all the rows with the same id_code_group belong
    ///to the same operation
    ///The operation will be written in the right ledger 
    ///Only operation with a id_status = 0 can be transferred , -1 is in error and
    ///1 means that it has already  been transferred
    function transform($ledger_id, $import_id)
    {
        throw new Exception("Not Yet Implemented");
    }

    //-------------------------------------------------------------------
    /// Check if Data are valid for one row
    //!@param $row is an Impacc_Import_detail_SQL object
    //!@param $p_format_date check for date_limit and date_payment
    //!@param $p_thousand separator for thousands
    //!@param $p_decimal separator for decimal
    //-------------------------------------------------------------------
    static function check(Impacc_Import_detail_SQL $row, $p_format_date,
            $p_thousand, $p_decimal)
    {
        $and=($row->id_message=="")?"":",";
        //-----------------
        ///- Check VAT CODE
        //-----------------
        if (Impacc_Verify::check_tva($row->tva_code)==false)
        {
            $row->id_message=$and."CK_TVA_INVALID";
            $and=",";
        }
        //-----------------
        ///- Check Date payment
        //-----------------
        if ($row->id_date_payment!="")
        {
            $date=Impacc_Verify::check_date($row->id_date_payment,$p_format_date);
            if ($date==false)
            {
                $row->id_message=$and."CK_FORMAT_DATE";
                $and=",";
            } else {
                $row->id_date_payment_conv=$date;
            }
        }
        //-----------------
        ///- Check Date limit
        //-----------------
        if ($row->id_date_limit!="")
        {
            $date=Impacc_Verify::check_date($row->id_date_limit,$p_format_date);
            if ($date==false)
            {
                $row->id_message=$and."CK_FORMAT_DATE";
                $and=",";
            } else {
                $row->id_date_limit_conv=$date;
            }
        }

        //-----------------
        ///- Check Amount VAT
        //-----------------
        $row->id_amount_vat_conv=Impacc_Tool::convert_amount($row->id_amount_vat,
                        $p_thousand, $p_decimal);
        if (isNumber($row->id_amount_vat_conv)==0)
        {
            $and=($row->id_message=="")?"":",";
            $row->id_message.=$and."CK_INVALID_AMOUNT";
        }
        //-----------------
        ///- Check Quantity
        //-----------------
        $row->id_quant_conv=Impacc_Tool::convert_amount($row->id_quant,
                        $p_thousand, $p_decimal);
        if (isNumber($row->id_quant_conv)==0)
        {
            $row->id_message.=$and."CK_INVALID_AMOUNT";
            $and=",";
        }
    }
    
    function check_nb_column()
    {
        throw new Exception("Not Yet Implemented");
    }

    /**
     * @brief insert file into the table import_detail
     */
    function record(Impacc_Csv $p_csv, Impacc_File $p_file)
    {
        global $aseparator;
        // Open the CSV file
        $hFile=fopen($p_file->import_file->i_tmpname, "r");
        $error=0;
        $cn=Dossier::connect();
        $delimiter=$aseparator[$p_csv->detail->s_delimiter-1]['label'];
        if ($p_csv->detail->s_surround==""){
            $p_csv->detail->s_surround='"';
        }
        //---- For each row ---
        while ($row=fgetcsv($hFile, 0, $delimiter, $p_csv->detail->s_surround))
        {
            $nb_row=count($row);
            $insert=new Impacc_Import_detail_SQL($cn);
            $insert->import_id=$p_file->import_file->id;
            if ($nb_row<9)
            {
                $insert->id_status=-1;
                $insert->id_message=join($row, $delimiter);
            }
            else
            {

                $insert->id_date=$row[0];
                $insert->id_code_group=$row[1];
                $insert->id_acc=$row[2];
                $insert->id_pj=$row[3];
                $insert->id_label=$row[4];
                $insert->id_acc_second=$row[5];
                $insert->id_quant=$row[6];
                $insert->id_amount_novat=$row[7];
                $insert->tva_code=$row[8];
                $insert->id_amount_vat=$row[9];
                $date_limit=(isset($row[10]))?$row[10]:"";
                $insert->id_date_limit=$date_limit;
                $date_payment=(isset($row[11]))?$row[11]:"";
                $insert->id_date_payment=$date_payment;
                $insert->id_nb_row=0;
            }
            // insert row into table with status
            $insert->insert();
        }
    }

    /// Abstract cannot be called 
    //  abstract function insert($a_group, $p_ledger);
}
