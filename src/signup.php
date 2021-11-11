<?php

$path = "MeTube/src/";
$url = "http://localhost:8070/";

include 'db_connection.php';
$conn = OpenCon();

$resubmit = false;
$error_message = "";

if($_SERVER['REQUEST_METHOD']=="POST"){
    //stores data from form
    $firstname = $_POST['first_name'];
    $lastname = $_POST['last_name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $birthday = $_POST['birthday'];
    $password = $_POST['password'];

    //checks if values are empty, ask them to resubmit if not
    if(empty($firstname) || empty($lastname) || empty($username) || empty($email) || empty($birthday) || empty($password)){
        $error_message = "<br>Some fields left blank. Please fill out entire form.<br>";
        $resubmit = true;
    }

    //queries username and email
    $sql = "SELECT username FROM Account WHERE username=\"$username\" LIMIT 0 , 30";
    $result = $conn->query($sql);

    $sql2 = "SELECT email FROM Account WHERE email=\"$email\" LIMIT 0 , 30";
    $result2 = $conn->query($sql2);

    //if username and/or email already in use
    if($result->num_rows > 0 && $result2->num_rows > 0) {
        $error_message = "<br>Username and email already in use. Please choose another.<br>";
        $resubmit = true;
    } else if($result->num_rows > 0){
        $error_message = "<br>Username already in use. Please choose another.<br>";
        $resubmit = true;
    } else if($result2->num_rows > 0){
        $error_message = "<br>Email already in use. Please choose another.<br>";
        $resubmit = true;
    }

    // adds info to 'Account' database if username and email available
    if($resubmit === false){
        //calculates appropriate user_id
        $id = 0;
        $sql = "SELECT MAX(user_id) AS id FROM Account";
        $result = $conn->query($sql);
        if($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $id = $row["id"] + 1;
            echo "New ID: $id";
        }

        $sql = "INSERT INTO Account VALUES 
        ('$id', '$firstname', '$lastname', '$username', '$email', CURRENT_TIMESTAMP, '$birthday', '$password')";
        $result = $conn->query($sql);

        if ($result === TRUE) {
            header('Location: '. $url . $path . 'login.html');
        } else {
            $echo("Error: " . $sql . "<br>" . $conn->error);
        }
    }

} else {
    echo "POST not submitted<br>";
}

CloseCon($conn);

//displays signup page with appropriate error message
if($resubmit === true){
    include 'signup.html';
    echo $error_message;
}

 ?>