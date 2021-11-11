<?php

$home = "home.php";
$channel = "channel.php";
$login = "login.php";

if(!session_id()) session_start();

if(!isset($_SESSION['isLoggedIn'])){
    $_SESSION['isLoggedin'] = false;
}

//Displays home page by default
include($home);

//When user logs in, they are directed to their channel page

?>