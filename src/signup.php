<?php

$path = "MeTube/src/";
$url = "http://localhost:8070/";

include 'db_connection.php';
$conn = OpenCon();

if ($conn->connect_error) {
    die("Connection failed: " 
        . $conn->connect_error);
} else {
    echo "Connection established<br>";
}

$resubmit = false;

if($_SERVER['REQUEST_METHOD']=="POST"){
    //check if values are empty, ask them to resubmit if not
    $firstname = $_POST['first_name'];
    $lastname = $_POST['last_name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $birthday = $_POST['birthday'];
    $password = $_POST['password'];

    if(empty($firstname) || empty($lastname) || empty($username) || empty($email) || empty($birthday) || empty($password)){
        echo "Some fields left blank. Please fill out entire form.<br>";
        $resubmit = true;
    }

    //checks if username and/or email is in use
    $sql = "SELECT username FROM User WHERE username LIKE " . $username . " LIMIT 0 , 30";
    $result = $conn->query($sql);

    $sql = "SELECT email FROM User WHERE email LIKE " . $email;
    $result2 = $conn->query($sql);
    
    if($result === true || $result2 === true) {
        $echo("Username or email already in use. Please choose another.<br>");
        $resubmit = true;
    }

    // // adds info to "user" database if username and email available
    // if($resubmit === false){
    //     //calculates appropriate userid
    //     $id = 0;
    //     $sql = "SELECT MAX(userid) AS id FROM user";
    //     $result = $conn->query($sql);
    //     if($result->num_rows > 0) {
    //         $row = $result->fetch_assoc();
    //         $id = $row["id"] + 1;
    //     }

    //     $sql = "INSERT INTO user VALUES 
    //     ('" . $id . "', '" . $firstname . "', '" . $lastname . "', '" . $username . 
    //     "', '" . $email . "', 'CURRENT_TIMESTAMP', '" . $birthday . "', '" . $password . "')";
    //     $result = $conn->query($sql);

    //     if ($result === TRUE) {
    //         $echo("record inserted successfully<br>");
    //     } else {
    //         $echo("Error: " . $sql . "<br>" . $conn->error);
    //     }
    // }

    // $errorMessage = <<< EOPAGE
    // <p></p>
    // EOPAGE;

    // echo $pageContents;
} else {
    echo "POST not submitted<br>";
}

CloseCon($conn);

//redirects to signup page if form must be resubmitted
//else redirects to home page
// if($resubmit === true){
//     header('Location: ' . $url . $path . 'signup.html');
// } else {
//     header('Location: ' . $url . $path . 'index.html');
// }
 ?>