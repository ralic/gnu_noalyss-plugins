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
// Copyright (2014) Author Dany De Bontridder <dany@alchimerys.be>

if (!defined('ALLOWED'))
    die('Appel direct ne sont pas permis');

/**
 * @file
 * @brief history of the import file
 */
require_once DIR_IMPORT_ACCOUNT."/include/class_impacc_file.php";
$id=HtmlInput::default_value_get("id", 0);
$ifile=new Impacc_File();
if ( $id > 0){
    $impacc_file=new Impacc_File();
    $impacc_file->load($id);
    $impacc_file->impid=$id;
    $impacc_file->result_transfer();
}
if ( $id==0)
{
    $ifile->display_list();
}
?>
