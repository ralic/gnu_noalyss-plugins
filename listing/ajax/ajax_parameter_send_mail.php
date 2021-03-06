<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt

/**
 * Display form to enter parameters
 */
require_once $g_listing_home.'/include/class_rapav_listing_compute.php';
require_once NOALYSS_INCLUDE.'/class/class_fiche_def.php';

ob_start();
$compute=new RAPAV_Listing_Compute();
$compute->load($_GET['lc_id']);
$fiche_def=new Fiche_Def($cn,$compute->listing->data->getp('fiche_def_id'));
if ( $fiche_def->HasAttribute(ATTR_DEF_EMAIL) == false) {
    echo '<p class="notice">';
    echo _("Cette catégorie n'a pas d'attribut email");
    echo '</p>';
} else {
    echo HtmlInput::title_box(_('Envoi par email'), "parameter_send_mail_input");
    $subject=new IText('p_subject');
    $from=new IText('p_from');
    $message=new ITextarea('p_message');
    $attach=new ISelect('p_attach');
    $copy=new ICheckBox('copy');
    //-----
    // Propose to generate document to attach at the email ,
    // if there is a template
    if ( $compute->has_template() )
    {
        $attach->value=array (
                array('value'=>0,'label'=>_('Aucun document')),
                array('value'=>1,'label'=>_('Document en PDF')),
                array('value'=>2,'label'=>_('Document généré'))
        );
        
    } else {
        $attach->value=array(
            array('value'=>0,'label'=>_('Aucun document'))
        );
    }
    require_once $g_listing_home.'/template/parameter_send_mail_input.php';
    
}
$response = ob_get_clean();
$html = escape_xml($response);
header('Content-type: text/xml; charset=UTF-8');
echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<data>
<ctl></ctl>
<code>$html</code>
</data>
EOF;
?>        