<?php
  
$path = "MeTube/src/";
$url = "http://localhost:8070/";

if(!session_id()) session_start();
  
include_once 'db_connection.php';
$conn = OpenCon();

$isLoggedIn = $_SESSION['isLoggedIn'];
$current_user_id = $_SESSION['user_id'];
$media_id = $_SESSION['media_id'];

$resubmit = false;
$error_message = "";

if($_SERVER['REQUEST_METHOD']=="POST"){
    //stores data from form
    $comment = $_POST['comment'];

    //checks if values are empty, ask them to resubmit if not
    if(empty($username) || empty($password)){
        $error_message = "<br>Field left blank.<br>";
        $resubmit = true;
    }

    if($resubmit === false){
        //queries username and password
        $sql = "SELECT `username`, `password` FROM Account WHERE `username`=\"$username\" AND `password`=\"$password\" LIMIT 0 , 30";
        $result = $conn->query($sql);

        //checks if username and password match an account
        if($result->num_rows > 0) {
            //gets userid for session
            $sql = "SELECT `user_id` FROM Account WHERE `username`=\"$username\" AND `password`=\"$password\"";
            $result = $conn->query($sql);
            $row = $result->fetch_assoc();
            $id = $row["user_id"];

            //sets session variables and switches to index
            $_SESSION['isLoggedIn'] = true;
            $_SESSION['user_id'] = $id;
            header('Location: '. $url . $path . 'index.php?page=channel&id=' . $_SESSION['user_id']);
        } else {
            $error_message = "<br><br>Incorrect username or password. Please try again.<br>";
            $resubmit = true;
        }
    }
}

CloseCon($conn);

$html = <<< PAGE
<!DOCTYPE html>
<html>
  <head>
    <title>Login</title>
  </head>
  <body>
    <span id="login_form">
      <form action="login.php" method="post">
        <fieldset>
          <legend>MeTube Login</legend>
          <p>
            <label for="username">Username: </label>
            <input type="text" id="username" name="username" /><br />
          </p>
          <p>
            <label for="password">Password: </label>
            <input type="text" id="password" name="password" /><br />
          </p>
          <p><input type="submit" value="Login" /> <input type="reset" /></p>
        </fieldset>
PAGE;

//displays signup page with appropriate error message
if($resubmit === true){
  $html .= $error_message;
}

$html .= <<< PAGE
      </form>
    </span> 
    <span id="sign-up">
      <p>Don't have an account? Sign up here:</p>
      <a href="signup.php"><input type="submit" value="Sign-up" id="sign-up"/></a>
    </span>
  </body>
</html>
PAGE;

echo $html;

?>