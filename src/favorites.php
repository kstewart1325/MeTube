<!-- 
    Contacts page 
    Allow users to organize their contacts into a contact list, 
    to (1) add a user, and to (2) remove a user from their contact lists. 
    BONUS: Allow users to organize their contacts into different categories.
-->

<?php
  $path = "MeTube/src/index.php";
  $url = "http://localhost:8070/";

  include 'db_connection.php';
  $conn = OpenCon();

  $session_user = $_SESSION['user_id'];
  
  $removed = false;
  $error_message = "";

  $html = <<< PAGE
      <!DOCTYPE html>
        <html>
        <head>
          <title>Playlists</title>
          <style>
            table, th, td {
              border: 1px solid black;
              border-collapse: collapse;
            }
          </style>
        </head>
        <body>
    PAGE;
  
  if($_SERVER['REQUEST_METHOD']=="POST"){

    if(isset($_POST['removeMedia'])){
        $mid = $_POST['m_id'];
        $sql = "DELETE FROM Favorites WHERE media_id=\"$mid\" AND user_id=$session_user";
        $result = $conn->query($sql);

        if ($result === TRUE) {
          $error_message = "<br>Media removed from Favorites.<br>";
          $subpage = true;
        } else {
          echo("Error: " . $sql . "<br>" . $conn->error);
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
        // $error_message = "<br>$playlist added to your playlists.<br>";
        header('Location: '. $url . $path . '?page=media&success=true&list=' . $pl);
    } else {
        header('Location: '. $url . $path . '?page=media&success=false&list=' . $pl);
    }  
      
  }

  // queries existing playlists for the current user
  $sql = "SELECT media_id FROM Favorites WHERE user_id=\"$session_user\"";
  $result = $conn->query($sql);
  $html .= "<h3><u>Your Favorites</u></h3>";
  
  // print out into a table v
  if ($result->num_rows > 0) {

      $html .= <<< PAGE
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
          $html .= <<< PAGE
              <tr><td><form method="post" name="manage_media" id="manage_media">
                              <fieldset>
                              <input type="submit" name="removeMedia" value="remove" /> 
                              <input type="hidden" id="m_id" name="m_id" value="$mid"/>
                              </fieldset>
              </form></td><td><a href=\"/MeTube/src/index.php?page=media&id=$mid\">$mname</a></td></tr>
          PAGE;

      }
      $html .= "</table><br>";
  } else {
      $html .= "You have no Favorites.";
  }

  $html .= <<< PAGE
    </body>
    </html>
  PAGE;

  echo $html;
  
  CloseCon($conn);

?>
