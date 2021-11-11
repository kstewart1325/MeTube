<?php
  
$path = "MeTube/src/";
$url = "http://localhost:8070/";
  
include 'db_connection.php';
$conn = OpenCon();

$resubmit = false;
$error_message = "";

if($_SERVER['REQUEST_METHOD']=="POST"){
    //stores data from form
    $username = $_POST['username'];
    $password = $_POST['password'];

    //checks if values are empty, ask them to resubmit if not
    if(empty($username) || empty($password)){
        $error_message = "<br>Some fields left blank. Please fill out entire form.<br>";
        $resubmit = true;
    }

    if($resubmit === false){
        //queries username and password
        $sql = "SELECT `username`, `password` FROM Account WHERE `username`=\"$username\" AND `password`=\"$password\" LIMIT 0 , 30";
        $result = $conn->query($sql);

        //checks if username and password match account an account
        if($result->num_rows > 0) {
            header('Location: '. $url . $path . 'index.html');
        } else {
            $error_message = "<br><br>Incorrect username or password. Please try again.<br>";
            $resubmit = true;
        }
    }
}

CloseCon($conn);

//displays signup page with appropriate error message
if($resubmit === true){
    include 'login.html';
    echo $error_message;
}

?>