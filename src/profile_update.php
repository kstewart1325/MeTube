<?php

/*
    POSSIBLE ISSUES:
    - When checking email validity, it only checks that the email includes '@' and '.com',
      but it doesn't check where in the string those are located
    - Doesn't check for extra white space in form data before or after string
*/

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
    $password = $_POST['password'];

    //checks if values are empty, ask them to resubmit if not
    //if(empty($firstname) || empty($lastname) || empty($username) || empty($email) || empty($birthday) || empty($password)){
    //    $error_message = "<br>Some fields left blank. Please fill out entire form.<br>";
    //    $resubmit = true;
    //}

    if(!empty($firstname)){
        $sql = "UPDATE Account
        SET 
            first_name = \"$firstname\"
        WHERE 
            user_id = 3";
        $result = $conn->query($sql);
    }
    if(!empty($lastname)){
        $sql = "UPDATE Account
        SET 
            last_name = \"$lastname\"
        WHERE 
            user_id = 3";
        $result = $conn->query($sql);
    }
    if(!empty($username)){
        $sql = "UPDATE Account
        SET 
            username = \"$username\"
        WHERE 
            user_id = 3";
        $result = $conn->query($sql);
    }
    if(!empty($email)){
        $sql = "UPDATE Account
        SET 
            email = \"$email\"
        WHERE 
            user_id = 3";
        $result = $conn->query($sql);
    }
    if(!empty($password)){
        $sql = "UPDATE Account
        SET 
            password = \"$password\"
        WHERE 
            user_id = 3";
        $result = $conn->query($sql);
    }

    //queries username and email
    $sql = "UPDATE username FROM Account WHERE username=\"$username\" LIMIT 0 , 30";
    $result = $conn->query($sql);

    $sql2 = "SELECT email FROM Account WHERE email=\"$email\" LIMIT 0 , 30";
    $result2 = $conn->query($sql2);

} else {
    echo "POST not submitted<br>";
}

CloseCon($conn);

 ?>