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

/**
 * @file
 * @brief Montre un form pour ajouter une formule
 * retour en XML
 */
require_once 'include/class_formulaire_param.php';
require_once 'include/class_formulaire_param_detail.php';

global $cn;
/**
 * Retrouve le type de row si == 3
 */
$type=$cn->get_value("select p_type from rapport_advanced.formulaire_param"
        . " where p_id=$1",array($p_id));
$obj=new Formulaire_Param_Detail();
if ( $type == 3)
{
    /*
     * type = formula
     */
    $obj->input_new($p_id);
} elseif ($type==9) {
    /*
     * type = child
     */
    $obj->input_new_child($p_id);
    
}
?>
