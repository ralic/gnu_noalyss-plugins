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
 * \brief add material
 */
$date=new IDate('p_date');
$p_year=new INum('p_year');
$p_number=new INum('p_number');
$p_card=new ICard('p_card');
$p_date=new IDate('p_date');
$p_card->size=25;
$list=$cn->make_list('select fd_id from fiche_def where frd_id=7');
if ( $list == '') 
  {
    
    echo h2info(_('Matériel à amortir'));
    echo h2(_('Attention pas de catégorie de fiche à amortir'),'class="error"');
  }
else 
  {
    $p_date->value=HtmlInput::default_value_post('p_date',"");
    $p_year->value=HtmlInput::default_value_post('p_year',"");
    $p_number->value=HtmlInput::default_value_post('p_number',"");

    $p_card->set_attribute('typecard',$list);
    $p_card->set_attribute('label','p_card_label');
    $p_card->javascript=sprintf(' onchange="fill_data_onchange(\'%s\');" ',
			       $p_card->name);
    $p_card->set_function('fill_data');
    $p_card->set_dblclick("fill_ipopcard(this);");
    $p_card->value=HtmlInput::default_value_post('p_card','');
    
    $p_deb=new IPoste('p_deb');
    $p_deb->set_attribute('jrn',0);
    $p_deb->set_attribute('account','p_deb');
    $p_deb->set_attribute('label','p_deb_label');
    $deb_span=new ISpan('p_deb_label');
    $p_deb->value=HtmlInput::default_value_post('p_deb','');
    
    $p_cred=new IPoste('p_cred');
    $p_cred->set_attribute('jrn',0);
    $p_cred->set_attribute('account','p_cred');
    $p_cred->set_attribute('label','p_cred_label');
    $p_cred->value=HtmlInput::default_value_post('p_cred','');
    
    $cred_span=new ISpan('p_cred_label');
    
    $p_amount=new INum('p_amount');
    $p_amount->value=HtmlInput::default_value_post('p_amount','');
    
    $p_card_deb=new ICard('p_card_deb');
    $p_card_deb->typecard = "all";
    $p_card_deb->value=HtmlInput::default_value_post('p_card_deb','');
    
    $p_card_cred=new ICard('p_card_cred');
    $p_card_cred->set_attribute('typecard'," select fd_id from fiche_def where frd_id=".FICHE_TYPE_MATERIAL);
    $p_card_cred->value=HtmlInput::default_value_post('p_card_cred','');
    
    $select_type=new ISelect('type');
    $select_type->id='select_type_id';
    $select_type->value=array(array('label'=>'--Faites un choix --','value'=>-1),
        array('label'=>'Poste comptable','value'=>'0'),
        array('label'=>'Fiche','value'=>'1')
        );
    
    $select_type->selected=HtmlInput::default_value_post('type','-1');
    
    $select_type->javascript=' onchange = "show_selected_material(this);"';
    require_once ('template/material_add.php');
  }
  ?>
<script>
show_selected_material($('select_type_id'));

</script>