<div class="content" id="listing_definition_div_id">
<?php
/**
 * @file
 * @brief manage the listing
 */
var_dump($_POST);
require_once 'class_rapav_listing.php';
global $cn;
$listing=new Rapav_Listing();

/**
 * save new listing
 */
if ( isset ($_POST['listing_add_sb']))
{
    $new=new Rapav_Listing($_POST['l_id']);
    if ( ! isset ($_POST['remove']))
        $new->save($_POST);
    else
        $new->delete($_POST);
}

///////////////////////////////////////////////////////////////////////////////
//Listing
///////////////////////////////////////////////////////////////////////////////
$listing->to_list();
echo '<p>';
Rapav_Listing::Button_Add_Listing();
echo '</p>';
?>
</div>