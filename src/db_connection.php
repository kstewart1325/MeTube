<?php
function OpenCon()
 {
    $servername = "mysql1.cs.clemson.edu";
    $username = "MeTube_05su";
    $password = "cpsc4620";
    $dbname = "MeTube_e7b2";

    $conn = new mysqli($servername, $username, $password,$dbname) or die("Connect failed: %s\n". $conn -> error);
 
    return $conn;
 }
function CloseCon($conn)
 {
    $conn -> close();
 }
   
?>