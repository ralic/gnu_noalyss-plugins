<?php
/**
 * @file montre détail budget pour mise à jour
 */
require_once 'class_budget.php';

$bud=new Budget();
$bud->b_id=$bud_id;
echo '<form id="fbud_update" method="post">';
$bud->detail();
echo HtmlInput::submit("bud_update","Valider");
echo "</form>";
?>
