<?php
session_start();
require("settings.php");

if(isset( $_SESSION['login']))
{
   
    if($_SESSION['login']=="admin" || $_SESSION['login']=="Admin")
    {
        $page = new WebPageAdmin($db,$website_name,$list_title,"",$_SESSION['login']);
    }
    else
        $page = new WebPage($db,$website_name,$list_title,"",$_SESSION['login']);

}
else
{
    $page = new WebPage($db,$website_name,$list_title,"","");

}



$page->render();

?>
