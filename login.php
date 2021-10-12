<?php
require "settings.php";
session_start();

$page = new WebPage($db,$website_name,$list_title,$orders_email,"");
$page->render_login();

?>
