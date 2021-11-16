<?php
// script to pull category options

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
        ('$id', '$firstname', '$lastname', '$username', '$email', CURRENT_TIMESTAMP, '$birthday', '$password')";
        $result = $conn->query($sql);

        if ($result === TRUE) {
            header('Location: '. $url . $path . 'login.php');
        } else {
            $echo("Error: " . $sql . "<br>" . $conn->error);
        }
    }

}

$html = <<< PAGE
<!DOCTYPE html>
<html>
  <head>
    <title>Upload File</title>
  </head>
  <body>
    <span id="upload_file">
    <form enctype="multipart/form-data" action="upload.php" method="post">
        <fieldset>
          <legend>MeTube Media Upload</legend>
          <input type="hidden" name="MAX_FILE_SIZE" value="4000000000" />
      <p>
        <label for="mediafile">File: </label>
        <input type="file" id="mediafile" name="mediafile" /><br />
      </p>
      <p>
        <label for="title">Title: </label>
        <input type="text" id="title" name="title" /><br />
      </p>
      <p>
        <!-- THESE EVENTUALLY NEED TO BE STORED IN A DB TABLE AND QUERIED TO POPULATE -->
        <label for="category">Category: </label>
        <select name="category">
        <option value="blank">     </option>
PAGE;

//Displays Categories
$sql = "SELECT `name` FROM Categories";
$result = $conn->query($sql);
if($result->num_rows > 0) {
  while($row = $result->fetch_assoc()){
    $cat = $row["name"];
    $html .= "<option value=\"$cat\">$cat</option>";
  }
}

$html .= <<< PAGE
        </select>
      </p>
      <p>
        <label for="description">Description: </label><br>
          500 character max.<br>
        <TEXTAREA name="description" rows="10" cols="80"></TEXTAREA>
      </p>
      <p>
        <label for="keywords">Search Keywords: </label><br>
          Separate keywords with a comma (",").<br>
        <TEXTAREA name="keywords" rows="2" cols="80"></TEXTAREA>
      </p>          
      <p><input type="submit" value="Upload" /> <input type="reset" value="Clear"/></p>
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

CloseCon($conn);

echo $html;

?>
