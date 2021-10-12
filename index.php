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
        $page = new WebPage($db,$website_name,$list_title,$orders_email,$_SESSION['login']);
}
else
{
    $page = new WebPage($db,$website_name,$list_title,$orders_email,"");


}



//////////////////////////////
// obsługa requestów fetch
//////////////////////////////

if(isset($_GET['cart_add_fetch']))
{
    if(!isset( $_SESSION['login']))
    {
        echo "Musisz być zalogowany";
        exit();
    }
    $page->cart_add();
    exit();
}
else if(isset($_GET['cart_remove_fetch']))
{
    if(!isset( $_SESSION['login']))
    {
        echo "Musisz być zalogowany";
        exit();
    }
    $page->cart_remove();
    exit();
}
else if(isset($_GET['cart_view_fetch']))
{
    if(!isset( $_SESSION['login']))
    {
        echo "Musisz być zalogowany";
        exit();
    }
    $page->cart_view();
    exit();
}
else if(isset($_GET['make_order']))
{
    if(!isset( $_SESSION['login']))
    {
        echo "Musisz być zalogowany";
        exit();
    }
    $page->make_order();
    exit();
}

///////////////////////////////////////////////////////////
// jeśli nie mamy fetcha do obsługi to renderujemy stronę
// w zależności od tego czy user jest adminem czy nie
///////////////////////////////////////////////////////////

$page->render();


