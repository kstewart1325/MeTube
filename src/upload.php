<?php

$path = "MeTube/src/";
$url = "http://localhost:8070/";

if(!session_id()) session_start();

include 'db_connection.php';
$conn = OpenCon();

$resubmit = false;
$error_message = "";
$allowedExtensions = array("jpg", "jpeg", "gif", "png", "mp3", "mp4", "wma");
$allowedTypes = array("image/jpeg", "image/pjpeg", "image/png", "image/gif", "audio/mp3", "video/mp4", "audio/wma");

if($_SERVER['REQUEST_METHOD']=="POST"){
  //stores data from form
  $file = $_FILES['mediafile']['tmp_name'];
  $title = $_POST['title'];
  $category = $_POST['category'];
  $desc = $_POST['description'];
  $keywords = $_POST['keywords'];

  //checks if values are empty or invalid, ask them to resubmit if not
  if(!is_uploaded_file($file) || empty($title) || $category==="blank" || empty($desc) || empty($keywords)){
    $error_message = "<br>Some fields left blank. Please fill out entire form.<br>";
    $resubmit = true;
  }

  // adds info to 'Mediafiles' database
  if($resubmit === false){
    //stores file data and properties
    $target_dir = "../uploads/";
    $target_file = $target_dir . basename($_FILES['mediafile']['name']);
    $fileExtension = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    $fileType = $_FILES['mediafile']['type'];
    $fileSize = $_FILES['mediafile']['size'];

    //checks if media type is valid
    if(in_array($fileExtension, $allowedExtensions) && in_array($fileType, $allowedTypes)){
      //checks if file already exists
      if(!file_exists($target_file)) {
        //calculates appropriate media_id
        $media_id = 0;
        $sql = "SELECT MAX(media_id) AS id FROM Mediafiles";
        $result = $conn->query($sql);
        if($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $media_id = $row["id"] + 1;
        }

        //stores uploaded file in uploads folder
        if(move_uploaded_file($file, $target_file)){
          $user_id = $_SESSION['user_id'];

          //stores file info into database
          $sql = "INSERT INTO Mediafiles VALUES 
          ('$media_id', '$user_id', \"$title\", '$fileType', '$fileSize', '$category', CURRENT_TIMESTAMP, '0', \"$desc\", '$target_file')";
          $result = $conn->query($sql);

          if ($result === TRUE) {
              header('Location: '. $url . $path . 'index.php?page=channel&id=' . $user_id);
          } else {
              echo("Error: " . $sql . "<br>" . $conn->error);
          }
        } else {
          $error_message = "<br><br>Error uploading media.<br>";
          $resubmit = true;
        }
      } else {
        $error_message = "<br><br>Media already uploaded. Please choose another.<br>";
        $resubmit = true;
      }
    } else {
      $error_message = "<br><br>Invalid media type. Please choose another.<br>";
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
