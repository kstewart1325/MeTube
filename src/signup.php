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

//checks if username and/or password already exists
$sqlquery = ""

if ($conn->query($sql) === TRUE) {
    echo "record inserted successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

//adds info to "user" database if not already available
$sqlquery = "INSERT INTO table VALUES 
('John', 'Doe', 'john@example.com')"

if ($conn->query($sql) === TRUE) {
echo "record inserted successfully";
} else {
echo "Error: " . $sql . "<br>" . $conn->error;
}
 ?>