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
        if(strpos($email, "@") === false || strpos($email, ".") === false){
            $error_message = "<br>Invalid Email. Please choose another.<br>";
            $resubmit = true;
        }

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
        ('$id', '$firstname', '$lastname', '$username', '$email', CURRENT_TIMESTAMP, '$birthday', '$password', 0)";
        $result = $conn->query($sql);

        if ($result === TRUE) {
            header('Location: '. $url . $path . 'login.php');
        } else {
            $echo("Error: " . $sql . "<br>" . $conn->error);
        }
    }

}

CloseCon($conn);

$html = <<< PAGE
<!DOCTYPE html>
<html>
  <head>
    <title>Sign-up</title>
  </head>
  <body>
    <span id="signup_form">
      <form action="signup.php" method="post">
        <fieldset>
          <legend>MeTube Signup</legend>
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
            <input type="text" id="email" name="email" /><br />
          </p>
          <p>
            <label for="birthday">Date of Birth: </label>
            <input type="date" id="birthday" name="birthday" /><br />
          </p>
          <p>
            <label for="password">Password: </label>
            <input type="text" id="password" name="password" /><br />
          </p>
          <p><input type="submit" value="Send" /> <input type="reset" /></p>
        </fieldset>
PAGE;

//displays signup page with appropriate error message
if($resubmit === true){
    $html .= $error_message;
}

$html .= <<< PAGE
      </form>
    </span>
  </body>
</html>
PAGE;

echo $html;

 ?>