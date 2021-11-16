<?php
// script to pull category options

$path = "MeTube/src/";
$url = "http://localhost:8070/";

if(!session_id()) session_start();

include 'db_connection.php';
$conn = OpenCon();

$resubmit = false;
$error_message = "";

if($_SERVER['REQUEST_METHOD']=="POST"){
  //stores data from form
  $file = $_FILES['mediafile'];
  $filesize = $_FILES['mediafile']['size'];
  $filetype = $_FILES['mediafile']['type'];

  $title = $_POST['title'];
  $category = $_POST['category'];
  $desc = $_POST['description'];
  $keywords = $_POST['keywords'];

  //checks if values are empty, ask them to resubmit if not
  if(empty($file) || empty($title) || $category==="blank" || empty($desc) || empty($keywords)){
    $error_message = "<br>Some fields left blank. Please fill out entire form.<br>";
    $resubmit = true;
  }

  // adds info to 'Mediafiles' database
  if($resubmit === false){
    $user_id = $_SESSION['user_id'];

    //checks if filename already exists
    $sql = "SELECT `media_title` FROM Mediafiles WHERE `media_title`=\"$title\" LIMIT 0 , 30";
    $result = $conn->query($sql);

    if($result->num_rows === 0) {
      //calculates appropriate media_id
      $id = 0;
      $sql = "SELECT MAX(media_id) AS id FROM Mediafiles";
      $result = $conn->query($sql);
      if($result->num_rows > 0) {
          $row = $result->fetch_assoc();
          $id = $row["id"] + 1;
      }

      $sql = "INSERT INTO Mediafiles VALUES 
      ('$id', '$user_id', '$title', '$filetype', '$filesize', '$category', CURRENT_TIMESTAMP, '0', '$desc', '$file')";
      $result = $conn->query($sql);

      if ($result === TRUE) {
          header('Location: '. $url . $path . 'index.php?page=channel');
      } else {
          $echo("Error: " . $sql . "<br>" . $conn->error);
      }
    } else {
      $error_message = "<br><br>Media title already exists. Please choose another.<br>";
      $resubmit = true;
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
