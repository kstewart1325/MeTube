
<?php
  $path = "MeTube/src/index.php";
  $url = "http://localhost:8070/";

  include_once 'db_connection.php';
  $conn = OpenCon();

  $session_user = $_SESSION['user_id'];
  //$session_user = 2;
  
  $removed = false;
  $error_message = "";

  $fav_html = "";
  
  if($_SERVER['REQUEST_METHOD']=="POST"){

    if(isset($_POST['removeMedia'])){
        $mid = $_POST['m_id'];
        $sql = "DELETE FROM Favorites WHERE media_id=\"$mid\" AND user_id=$session_user";
        $result = $conn->query($sql);

        if ($result === TRUE) {
          $error_message = "<br>Media removed from Favorites.<br>";
          header('Location: '. $url . $path . 'index.php?page=favorites&msg=' . $error_message);
        } else {
          $fav_html .= "Error: $sql <br> $conn->error";
        } 
    
    } 
  }
  
  if(isset($_GET["file"])){
    $m = $_GET['file'];

    //calculates appropriate id for a unique value (to allow deletions)
    $id = 0;
    $sql = "SELECT MAX(fav_id) AS id FROM Favorites WHERE user_id=$session_user";
    $result = $conn->query($sql);
    if($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id = $row["id"] + 1;
    }
    
    // add info to 'Contacts' database
    $sql2 = "INSERT INTO Playlist_Data VALUES ('$session_user', '$m', '$id')";
    $result2 = $conn->query($sql2);

    // FIX THIS
    $pl = "fav";
    if ($result2 === TRUE) {
        header('Location: '. $url . $path . '?page=media&success=true&list=' . $pl);
    } else {
        header('Location: '. $url . $path . '?page=media&success=false&list=' . $pl);
    }  
      
  }

  // queries existing playlists for the current user
  $sql = "SELECT media_id FROM Favorites WHERE user_id=\"$session_user\"";
  $result = $conn->query($sql);
  $fav_html .= "<h3><u>Your Favorites</u></h3>";
  
  // print out into a table v
  if ($result->num_rows > 0) {

      $fav_html .= <<< PAGE
          <table style=\"width:50%\">
          <colgroup>
              <col span="1" style="width: 15%;">
              <col span="1" style="width: 70%;">
          </colgroup>
          <tr><th>     </th><th>Media</th></tr>
      PAGE;

      // get each mediafile name and print 
      while($row = $result->fetch_assoc()) {
          $mid = $row['media_id'];
      
          $sql2 = "SELECT media_title FROM Mediafiles WHERE media_id=\"$mid\"";
          $result2 = $conn->query($sql2);
          $row2 = $result2->fetch_assoc();
          
          $mname = $row2['media_title'];
          // make mname a link to the mediafile page
          $fav_html .= <<< PAGE
              <tr><td><form action="favorites.php" method="post" name="manage_media" id="manage_media">
                              <fieldset>
                              <input type="submit" name="removeMedia" value="remove" /> 
                              <input type="hidden" id="m_id" name="m_id" value="$mid"/>
                              </fieldset>
              </form></td><td><a href=\"/MeTube/src/index.php?page=media&id=$mid\">$mname</a></td></tr>
          PAGE;

      }
      $fav_html .= "</table><br>";
  } else {
      $fav_html .= "You have no Favorites.";
  }
  
  CloseCon($conn);

?>
