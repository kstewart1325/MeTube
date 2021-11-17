<!-- 
    Contacts page 
    Allow users to organize their contacts into a contact list, 
    to (1) add a user, and to (2) remove a user from their contact lists. 
    BONUS: Allow users to organize their contacts into different categories.
-->

<?php

  $path = "MeTube/src/";
  $url = "http://localhost:8070/";
  $manage = "playlist_management.php";

  include 'db_connection.php';
  $conn = OpenCon();

  //$session_user = $_SESSION['user_id'];
  $session_user = 2;
  
  $resubmit = false;
  $error_message = "";
  
  if($_SERVER['REQUEST_METHOD']=="POST"){
      //stores data from form
      $playlist = $_POST['p_name'];

      //checks if values are empty, ask them to resubmit if not
      if(empty($playlist)){
          $error_message = "<br>Playlist left blank. Please enter a playlist name.<br>";
          $resubmit = true;
      }

      if(isset($_POST['removePlaylist']) && $resubmit === false){
          // remove playlist
          // queries the entered playlist with current user and verifies it exists
          $sql2 = "SELECT list_id FROM Playlists WHERE p_name=\"$playlist\" AND user_id=$session_user LIMIT 0 , 30";
          $result2 = $conn->query($sql2);

          if($result2->num_rows > 0){
            $row = $result2->fetch_assoc();
            $pid = $row['list_id'];

            // queries to find the entry number in the playlist data table
            $sql3 = "SELECT entry_num FROM Playlist_Data WHERE list_id=\"$pid\" LIMIT 0 , 30";
            $result3 = $conn->query($sql3);

            if($result3->num_rows > 0){
                // delete files from the playlist
                while($entry = $result3->fetch_assoc()){
                    $eid = $entry['playlist_id'];
                    $sql = "DELETE FROM Playlist_Data WHERE entry_num=\"$eid\"";
                }
            }

            // remove info from 'Playlists' database
            $sql = "DELETE FROM Playlists WHERE list_id=\"$pid\"";
            $result = $conn->query($sql);

            if ($result === TRUE) {
                // header('Location: '. $url . $path . 'contacts.php');
                $error_message = "<br>Playlist <i>$playlist</i> deleted. ";
            } else {
                echo("Error: " . $sql . "<br>" . $conn->error);
            }

          }else{
              $error_message = "<br>Playlist <i>$playlist</i> does not exist.<br>";
              $resubmit = true;
          }          
            
      }else if($_POST['renamePlaylist'] && $resubmit === false){
          $nname = $_POST['new_name'];

          //queries the entered playlist with current user
          $sql2 = "SELECT p_name FROM Playlists WHERE p_name=\"$nname\" AND user_id=$session_user LIMIT 0 , 30";
          $result2 = $conn->query($sql2);
          
          if($result2->num_rows > 0){
              $error_message = "<br>There is already a playlist named $nname on your account. <br> Please try another name.<br>";
              $resubmit = true;
          }else{
              $sql3 = "UPDATE Playlists SET p_name=\"$nname\" WHERE p_name=\"$playlist\"";
              $result3 = $conn->query($sql3);

              if ($result3 === TRUE) {
                $error_message = "<br>$playlist renamed to $nname.<br>";
              } else {
                echo("Error: " . $sql3 . "<br>" . $conn->error);
               } 
          }

      }else if($_POST['removeMedia'] && $resubmit === false){
        $mid = $_POST['m_id'];
        $sql = "DELETE FROM Playlist_Data WHERE media_id=\"$mid\"";
        $result = $conn->query($sql);

        if ($result === TRUE) {
          $error_message = "<br>Media removed from playlist.<br>";
        } else {
          echo("Error: " . $sql3 . "<br>" . $conn->error);
        } 
        
      }else if($resubmit === false){
          // add to user's playlists

          //queries the entered playlist with current user
          $sql2 = "SELECT p_name FROM Playlists WHERE p_name=\"$playlist\" AND user_id=$session_user LIMIT 0 , 30";
          $result2 = $conn->query($sql2);
          
          if($result2->num_rows > 0){
              $error_message = "<br>There is already a playlist named <i>$playlist</i> on your account. <br>";
              $resubmit = true;
          }else{
              //calculates appropriate cid for a unique value (to allow deletions)
              $id = 0;
              $sql = "SELECT MAX(list_id) AS id FROM Playlists";
              $result = $conn->query($sql);
              if($result->num_rows > 0) {
                  $row = $result->fetch_assoc();
                  $id = $row["id"] + 1;
              }
      
              // add info to 'Contacts' database
              $sql = "INSERT INTO Playlists VALUES ('$id', '$session_user', '$playlist')";
              $result3 = $conn->query($sql);
      
              if ($result3 === TRUE) {
                  $error_message = "<br><i>$playlist</i> added to your playlists.<br>";
              } else {
                  echo("Error: " . $sql . "<br>" . $conn->error);
              }            
          }      
      }

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
          <span id="contact_form">
            <form method="post" name="create_playlist_form" id="create_playlist_form">
              <fieldset>
                
                <p>
                  <label for="p_name">Playlist Name: </label>
                  <input type="text" id="p_name" name="p_name" /><br />
                </p>
                <p>
      PAGE;
                
      $html .= "<input type=\"submit\" name=\"createPlaylist\" value=\"Create New Playlist\" /> ";
      $html .= "<input type=\"submit\" name=\"removePlaylist\" value=\"Delete Playlist\" /> ";
    
      //displays contact page with appropriate error message
      $html .= "<br><i>$error_message</i>";
    
      $html .= <<< PAGE
                </p>
              </fieldset>
            </form>
          </span>
      PAGE;
    
      // queries existing playlists for the current user
      $sql = "SELECT list_id FROM Playlists WHERE user_id=\"$session_user\"";
      $result = $conn->query($sql);
      $html .= "<h3><u>Your Playlists</u></h3><p>Click a playlist to view and manage.</p>";
      
      // print out into a table v
      if ($result->num_rows > 0) {
          $html .= "<ul>";
          // get each playlist name and print 
          while($row = $result->fetch_assoc()) {
              $pid = $row['list_id'];
            
              $sql2 = "SELECT p_name FROM Playlists WHERE list_id=\"$pid\"";
              $result2 = $conn->query($sql2);
              $row2 = $result2->fetch_assoc();
              
              $html .= "<li><a href=\"/MeTube/src/playlists.php?list=$pid\">".$row2["p_name"]." </a></li>";
          }
          $html .= "<ul>";
      } else {
        $html .= "You have no Playlists.";
      }
    
      $html .= <<< PAGE
        </body>
      </html>
      PAGE;
    
  }else if($_SERVER['REQUEST_METHOD']=="GET"){
        $playlist = $_GET['list'];

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

        // query playlist name
        $sql = "SELECT p_name FROM Playlists WHERE list_id=$playlist";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $pname = $row['p_name'];
            $html .= "<p><h3>Playlist $pname</h3></p>";

            // query media file info from the playlist
            $sql2 = "SELECT Playlist_Data.media_id, Mediafiles.media_title FROM (Playlist_Data INNER JOIN Mediafiles ON Playlist_Data.media_id = Mediafiles.media_id) WHERE Playlist_Data.list_id=$playlist";
            $result2 = $conn->query($sql2);
            
            if ($result2->num_rows > 0) {
                $html .= <<< PAGE
                    <table style=\"width:50%\">
                    <colgroup>
                        <col span="1" style="width: 15%;">
                        <col span="1" style="width: 70%;">
                    </colgroup>
                    <tr><th>     </th><th>Media</th></tr>
                PAGE;
                while($row2 = $result2->fetch_assoc()){
                    $mid = $row2['media_id'];
                    $mname = $row2['media_title'];
                    // make mname a link to the mediafile page
                    $html .= <<< PAGE
                        <tr><td><form method="post" name="manage_media" id="manage_media">
                                        <fieldset>
                                        <input type="submit" name="removeMedia" value="delete" /> 
                                        <input type="hidden" id="m_id" name="m_id" value="$mid"/>
                                        <input type="hidden" id="p_name" name="p_name" value="$pname"/>
                                        </fieldset>
                        </form></td><td>$mname</td></tr>
                    PAGE;
                }
                $html .= "</table><br>";
            }else{
                $html .= "<p><i>Playlist is empty</i></p>";
            }
            
            $html .= <<< PAGE
                    <form method="post" name="manage_playlist" id="manage_playlist">
                        <fieldset>
                        <p>
                            <label for="new_name">New Name: </label>
                            <input type="text" id="new_name" name="new_name" />
                            <input type="submit" name="renamePlaylist" value="Rename Playlist" />
                        </p>
                        <p>
                            <b>Playlist cannot be recovered once deleted.</b>
                        </p>
                        <p>
                            <input type="submit" name="removePlaylist" value="Delete Playlist" /> 
                        <p>
                        <input type="hidden" id="p_name" name="p_name" value="$pname"/>
                        </fieldset>
                    </form>
                </body>
            </html>
            PAGE;
        }else{
            echo "Error fetching playlist.";
        }
  }

  echo $html;
  
  CloseCon($conn);
?>
