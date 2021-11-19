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
  
  $resubmit = false;
  $error_message = "";

  $subpage = false;

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
                //$error_message = "<br>Playlist <i>$playlist</i> deleted. ";
                header('Location: '. $url . $path . '?page=playlists');
            } else {
                echo("Error: " . $sql . "<br>" . $conn->error);
            }

          }else{
              $error_message = "<br>Playlist <i>$playlist</i> does not exist.<br>";
              $resubmit = true;
          }          
            
      }else if(isset($_POST['renamePlaylist']) && $resubmit === false){
          $nname = $_POST['new_name'];
          $pname = $_POST['p_name'];
          $pid = $_POST['p_id'];

          //queries the entered playlist with current user
          $sql2 = "SELECT * FROM Playlists WHERE p_name=\"$nname\" AND user_id=$session_user LIMIT 0 , 30";
          $result2 = $conn->query($sql2);
          
          if($result2->num_rows > 0){
              $error_message = "<br>There is already a playlist named $nname on your account. <br>Please try another name.<br>";
              header('Location: '. $url . $path . '?page=playlists&list='. $pid);
          }else{
              $sql3 = "UPDATE Playlists SET p_name=\"$nname\" WHERE list_id=\"$pid\"";
              $result3 = $conn->query($sql3);

              if ($result3 === TRUE) {
                $error_message = "<br>$playlist renamed to $nname.<br>";
                $subpage = true;
              } else {
                echo("Error: " . $sql3 . "<br>" . $conn->error);
              } 
          }

      }else if(isset($_POST['removeMedia']) && $resubmit === false){
        $mid = $_POST['m_id'];
        $sql = "DELETE FROM Playlist_Data WHERE media_id=\"$mid\" AND user_id=\"$session_user\"";
        $result = $conn->query($sql);

        if ($result === TRUE) {
          $error_message = "<br>Media removed from playlist.<br>";
          $subpage = true;
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
      
              // add info to 'Playlists' database
              $sql = "INSERT INTO Playlists VALUES ('$id', '$session_user', '$playlist')";
              $result3 = $conn->query($sql);
      
              if ($result3 === TRUE) {
                  $error_message = "<br>$playlist added to your playlists.<br>";
              } else {
                  echo("Error: " . $sql . "<br>" . $conn->error);
              }            
          }      
      } 
  }
  
// get request to add media to a playlist from a mediafile page
  if(isset($_GET["media"]) && isset($_GET["list"])){
    $pl = $_GET['list'];
    $m = $_GET['media'];

    //calculates appropriate id for a unique value (to allow deletions)
    $id = 0;
    $sql = "SELECT MAX(entry_num) AS id FROM Playlist_Data";
    $result = $conn->query($sql);
    if($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id = $row["id"] + 1;
    }
    
    // add info to 'Playlist_Data' table
    $sql2 = "INSERT INTO Playlist_Data VALUES ('$pl', '$m', '$id')";
    $result2 = $conn->query($sql2);

    if ($result2 === TRUE) {
        header('Location: '. $url . $path . 'page=media&success=true&list=' . $pl);
        
    } else {
        header('Location: '. $url . $path . 'page=media&success=false&list=' . $pl);
    }  
      
// get request for displaying a playlist's page
  }else if(isset($_GET["list"])){
    $subpage = true;
    $playlist = $_GET['list'];

    // query playlist name
    $sql = "SELECT p_name FROM Playlists WHERE list_id=$playlist AND user_id=\"$session_user\"";
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

                $html .= <<< PAGE
                    <tr><td><form method="post" name="manage_media" id="manage_media">
                                    <fieldset>
                                    <input type="submit" name="removeMedia" value="remove" /> 
                                    <input type="hidden" id="m_id" name="m_id" value="$mid"/>
                                    <input type="hidden" id="p_id" name="p_id" value="$playlist"/>
                                    <input type="hidden" id="p_name" name="p_name" value="$pname"/>
                                    </fieldset>
                    </form></td><td><a href=\"/MeTube/src/index.php?page=media&id=$mid\">$mname</a></td></tr>
                PAGE; 
            }
            $html .= "</table><br>";
        }else{
            $html .= "<p><i>Playlist is empty</i></p><p>$error_message</p>";
        }
        
        $html .= <<< PAGE
                <form method="post" name="manage_playlist" id="manage_playlist" >
                    <fieldset>
                    <p>
                        <label for="new_name">New Name: </label>
                        <input type="text" id="new_name" name="new_name" />
                        <input type="submit" name="renamePlaylist" value="Rename Playlist" />
                    </p>
                    <b>Playlist cannot be recovered once deleted.</b><br>
                    <p>
                        <input type="submit" name="removePlaylist" value="Delete Playlist" /> 
                        <input type="hidden" id="p_id" name="p_id" value="$playlist"/>
                        <input type="hidden" id="p_name" name="p_name" value="$pname"/>
                    </p>
                    </fieldset>
                </form>
        PAGE;
    }else{
        echo "Playlist does not exist.";
    }

    $html .= "<br><a href = \"playlists.php\">Return to Playlists</a>";
// displays default page with all playlists for a user listed
  }else if(!($subpage)){
        $html = <<< PAGE
        <span id="playlist_form">
        <form method="post" name="create_playlist_form" id="create_playlist_form">
            <fieldset>
            
            <p>
                <label for="p_name">Playlist Name: </label>
                <input type="text" id="p_name" name="p_name" /><br />
            </p>
            <p>
                <input type="submit" name="createPlaylist" value="Create New Playlist" /> 
                <input type="submit" name="removePlaylist" value="Delete Playlist" />
                <br><i>$error_message</i>
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
                
                $html .= "<li><a href=\"/MeTube/src/index.php?page=playlists&list=$pid\">".$row2["p_name"]." </a></li>";
            }
            $html .= "<ul>";
        } else {
            $html .= "You have no Playlists.";
        }
  }

  $html .= <<< PAGE
    </body>
    </html>
  PAGE;

  echo $html;
  
  CloseCon($conn);
?>
