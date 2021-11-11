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
        $conn->query($sql);
    }
    if(!empty($lastname)){
        $sql = "UPDATE Account
        SET 
            last_name = \"$lastname\"
        WHERE 
            user_id = 3";
        $conn->query($sql);
    }
    if(!empty($username)){
        $sql = "UPDATE Account
        SET 
            username = \"$username\"
        WHERE 
            user_id = 3";
        $conn->query($sql);
    }
    if(!empty($email)){
        $sql = "UPDATE Account
        SET 
            email = \"$email\"
        WHERE 
            user_id = 3";
        $conn->query($sql);
    }
    if(!empty($password)){
        $sql = "UPDATE Account
        SET 
            password = \"$password\"
        WHERE 
            user_id = 3";
        $conn->query($sql);
    }

    //queries username and email
    $sql = "SELECT username FROM Account WHERE username=\"$username\" LIMIT 0 , 30";
    $result = $conn->query($sql);

    $sql2 = "SELECT email FROM Account WHERE email=\"$email\" LIMIT 0 , 30";
    $result2 = $conn->query($sql2);

    //checks if username and/or email already in use
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
        //checks if email is valid
        if(strpos($email, "@") === false || strpos($email, ".com") === false){
            $error_message = "<br>Invalid Email. Please choose another.<br>";
            $resubmit = true;
        }
    }



} else {
    echo "POST not submitted<br>";
}

CloseCon($conn);

//displays signup page with appropriate error message
if($resubmit === true){
    include 'profile_update.html';
    echo $error_message;
}

 ?>