<?php

if(!session_id()) session_start();
$isLoggedIn = $_SESSION['isLoggedIn'];

if($isLoggedIn){
    //shows header to allow signin
} else {
    //shows header to allow logout
}

?>