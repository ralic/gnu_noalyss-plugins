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
 * @brief add an exercice of 12 month starting when we want
 *
 */
require_once 'class_tool_exercice.php';
global $g_succeed;
if (isset ($_POST['save']))
{
	try
	{
		$exercice=new Tool_Exercice($cn);
		$exercice->fromPost();
		$exercice->save();
		printf (_("%s Vous avez ajouté %d mois depuis le %d/%d pour l'exercice %d "),$g_succeed,$_POST['nb_month'],$_POST['from_month'],$_POST['year'],$_POST['p_exercice']);
		return;
	}
	catch (Exception $e)
	{
		alert($e->getMessage());
	}

}

$exercice=new Tool_Exercice($cn);
echo $exercice->input();
?>
