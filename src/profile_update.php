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
$submitted = false;
$error_message = "";

if(!session_id()) session_start();
$session_user = $_SESSION['user_id'];

if($_SERVER['REQUEST_METHOD']=="POST"){
    //stores data from form
    $firstname = $_POST['first_name'];
    $lastname = $_POST['last_name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    //queries username and email
    $sql = "SELECT username FROM Account WHERE username=\"$username\" LIMIT 0 , 30";
    $result = $conn->query($sql);

    $sql2 = "SELECT email FROM Account WHERE email=\"$email\" LIMIT 0 , 30";
    $result2 = $conn->query($sql2);

    //checks if username and/or email already in use
    if($result->num_rows > 0 && $result2->num_rows > 0) {
        $error_message = "<br>Username and email already in use. Please choose another.<br>";
        $resubmit = true;
    } 
    else if($result->num_rows > 0){
        $error_message = "<br>Username already in use. Please choose another.<br>";
        $resubmit = true;
    } 
    else if($result2->num_rows > 0){
        $error_message = "<br>Email already in use. Please choose another.<br>";
        $resubmit = true;
    } 
    else if(!empty($email) && (strpos($email, "@") === false || strpos($email, ".")) === false){
        $error_message = "<br>Invalid Email. Please choose another.<br>";
        $resubmit = true;
    } 
    else{
        //update the information if the box is filled in
        if(!empty($firstname)){
            $sql = "UPDATE Account
            SET 
                first_name = \"$firstname\"
            WHERE 
                user_id = \"$session_user\"";
            $conn->query($sql);
            $submitted = true;
        }
        if(!empty($lastname)){
            $sql = "UPDATE Account
            SET 
                last_name = \"$lastname\"
            WHERE 
                user_id = \"$session_user\"";
            $conn->query($sql);
            $submitted = true;
        }
        if(!empty($username)){
            $sql = "UPDATE Account
            SET 
                username = \"$username\"
            WHERE 
                user_id = \"$session_user\"";
            $conn->query($sql);
            $submitted = true;
        }
        if(!empty($email)){
            $sql = "UPDATE Account
            SET 
                email = \"$email\"
            WHERE 
                user_id = \"$session_user\"";
            $conn->query($sql);
            $submitted = true;
        }
        if(!empty($password)){
            $sql = "UPDATE Account
            SET 
                password = \"$password\"
            WHERE 
                user_id = \"$session_user\"";
            $conn->query($sql);
            $submitted = true;
        }
    }
} 

CloseCon($conn);

$html = <<< PAGE
                                <!DOCTYPE html>
    <html>
    <head>
        <title>Profile Update</title>
    </head>
    <body>
        <span id="profile_update">
        <form action="profile_update.php" method="post">
            <fieldset>
            <p>Fill in the fields you wish to update.</p>
            <legend>Update Profile Information</legend>
            <p>
                <label for="first_name">First Name: </label>
                <input type="text" id="first_name" name="first_name" /><br />
            </p>
            <p>
                <label for="last_name">Last Name: </label>
                <input type="text" id="last_name" name="last_name" /><br />
            </p>
            <p>
                <label for="username">Username: </label>
                <input type="text" id="username" name="username" /><br />
            </p>
            <p>
                <label for="email">Email: </label>
                <input type="text " id="email" name="email" /><br />
            </p>
            <p>
                <label for="password">Password: </label>
                <input type="text" id="password" name="password" /><br />
            </p>
            <p>
                <button type="submit" value="Send" action="profile_update.php">Save </button> 
                <button type="reset">Cancel </button>
            </p>
    PAGE;

//displays signup page with appropriate error message
if($resubmit === true){
    $html .= $error_message;
}
if($submitted === true){
    $html .= "Profile successfully updated";
    header('Location: '. $url . $path . 'index.php?page=channel&id=' . $_SESSION['user_id']);
}

$html .= <<< PAGE
      </form>
    </span>
  </body>
</html>
PAGE;

echo $html;

 ?>