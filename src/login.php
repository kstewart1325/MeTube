<?php
  
$servername = "localhost";
$username = MeTube_05su;
$password = cpsc4620;
$dbname = "MeTube_e7b2";
  
// Create connection
$conn = new mysqli($servername, 
    $username, $password, $dbname);
  
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " 
        . $conn->connect_error);
}

//Checks username and password against 'user' database
$sqlquery = ""

if ($conn->query($sql) === TRUE) {
    echo "Username and password entered successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}


?>