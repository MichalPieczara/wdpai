<?php
require "settings.php";

$page = new WebPage($db,$website_name,$list_title,$orders_email,"");
$page->render_registration();

?>
