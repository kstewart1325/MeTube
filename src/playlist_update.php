<?php
include_once 'db_connection.php';

$path = "MeTube/src/";
$url = "http://localhost:8070/";

if(!session_id()) session_start();

$isLoggedIn = $_SESSION['isLoggedIn'];
$session_user = $_SESSION['user_id'];

$conn = openCon();

$html = "";
$action = "";
$list = "";
$id = "";
$msg = "";

if($_SERVER['REQUEST_METHOD']=="GET"){
    if(isset($_GET['action'])){
        $action = $_GET['action'];
    }

    if(isset($_GET['list'])){
        $list = $_GET['list'];
    }

    if(isset($_GET['id'])){
        $id = $_GET['id'];
    }

    if($action === "delete"){
        // remove playlist
        // queries the entered playlist with current user and verifies it exists
        $sql2 = "SELECT list_id FROM Playlists WHERE p_name=\"$list\" AND user_id='$session_user'";
        $result2 = $conn->query($sql2);
    
        if($result2->num_rows > 0){
            $canDelete = TRUE;
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
                    $result = $conn->query($sql);
    
                    if($result !== TRUE){
                        $msg = "Error removing playlist entries from <i>$playlist</i>.";
                        $canDelete = FALSE;
                    }
                }
            } 
    
            // remove info from 'Playlists' database
            if($canDelete === TRUE){
                $sql = "DELETE FROM Playlists WHERE list_id=\"$pid\"";
                $result = $conn->query($sql);
    
                if ($result === TRUE) {
                    $msg = "Playlist <i>$playlist</i> deleted successfully.";
                } else {
                    $msg = "Error deleting playlist <i>$playlist</i>.";
                }
            }
    
        } else {
            $msg = "Playlist does not exist.";
        }
    
        header('Location: '. $url . $path . 'index.php?page=playlists&list=all&msg=' . $msg);
       
    } else if($action === "create") {
    
        //displays Create Playlist form
        $html .= <<< FORM
        <!DOCTYPE html>
        <html>
        <head>
            <title>Create Playlist</title>
        </head>
        <body>
          <span id="playlist_form">
          <form action="playlist_update.php" method="post" name="create_playlist_form" id="create_playlist_form">
              <fieldset>
              <legend>Create Playlist</legend>
              <p>
                  <label for="p_name">Playlist Name: </label>
                  <input type="text" id="p_name" name="p_name" /><br />
              </p>
              <p>
                  <input type="submit" name="createPlaylist" value="Create Playlist" /> 
              </p>
              </fieldset>
          </form>
          </span>
        </body>
        </html>
        FORM;
        
    } else if($action === "rename"){
    
        //displays Rename Playlist form
        $html .= <<< FORM
        <!DOCTYPE html>
        <html>
        <head>
            <title>Sign-up</title>
        </head>
        <body>
            <form action="playlist_update.php" method="post" name="manage_playlist" id="manage_playlist" >
                <fieldset>
                    <p>
                        <label for="new_name">New Name: </label>
                        <input type="text" id="new_name" name="new_name" />
                        <input type="hidden" id="list" name="list" value="$list"/>
                    </p>
                    <p>
                        <input type="submit" name="renamePlaylist" value="Rename Playlist" />
                    </p>
                </fieldset>
            </form>
        </body>
        </html>
        FORM;
        
    } else if($action === "removeMedia"){
        //gets list id
        $sql = "SELECT `list_id` FROM Playlists WHERE `p_name`='$list'";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $list_id = $row['list_id'];

        // Removes media from playlist data
        $sql = "DELETE FROM Playlist_Data WHERE media_id='$id' AND `list_id`='$list_id'";
        $result = $conn->query($sql);
    
        if ($result === TRUE) {
            $msg = "Media removed from <i>$list</i>.";
        } else {
            $msg = "Error removing media from <i>$list</i>.";
        }
    
        header('Location: '. $url . $path . 'index.php?page=playlists&list=' . $list . '&msg=' . $msg);
    
    } else if($action === "addMedia"){
        // Adds media to playlist

        //checks if media is already in playlist
        $sql = "SELECT `media_id` FROM Playlist_Data
        INNER JOIN Playlists ON Playlist_Data.list_id = Playlists.list_id
        WHERE Playlists.p_name='$list'";
        $result = $conn->query($sql);
       
        if($result->num_rows > 0){
            $msg = "Media already in <i>$list</i>";
        } else {
             //calculates appropriate id for a unique value (to allow deletions)
            $eid = 0;
            $sql = "SELECT MAX(entry_num) AS id FROM Playlist_Data";
            $result = $conn->query($sql);
            if($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $eid = $row["id"] + 1;
            }
        
            // gets playlist id
            $sql = "SELECT `list_id` FROM Playlists WHERE `p_name`='$list'";
            $result = $conn->query($sql);
            $row = $result->fetch_assoc();
            $list_id = $row['list_id'];
            
            // add info to 'Playlist_Data' table
            $sql2 = "INSERT INTO Playlist_Data VALUES ('$list_id', '$id', '$eid')";
            $result2 = $conn->query($sql2);
        
            if ($result2 === TRUE) {
                $msg = "Media successfully added to <i>$list</i>.";        
            } else {
                $msg = "Error adding media to <i>$list</i>.";
            }
        }
    
        header('Location: '. $url . $path . 'index.php?page=playlists&list=' . $list . '&msg=' . $msg);
    } else {
        $msg = "Button didn't work";
        header('Location: '. $url . $path . 'index.php?page=playlists&list=all&msg=' . $msg);

    }

} else if ($_SERVER['REQUEST_METHOD']=="POST"){

    if(isset($_POST['renamePlaylist'])){
        //edits playlist name
        $nname = $_POST['new_name'];
        $list = $_POST['list'];
        $isRenamed = FALSE;

        //queries the entered playlist with current user
        $sql2 = "SELECT * FROM Playlists WHERE p_name=\"$list\" AND user_id=$session_user LIMIT 0 , 30";
        $result2 = $conn->query($sql2);
        $row = $result2->fetch_assoc();
        $pid = $row['list_id'];

        //queries the entered playlist with current user
        $sql2 = "SELECT * FROM Playlists WHERE p_name=\"$nname\" AND user_id=$session_user LIMIT 0 , 30";
        $result2 = $conn->query($sql2);
        
        if($result2->num_rows > 0){
            $msg = "There is already a playlist named <i>$nname</i> on your account. <br>Please try another name.<br>";
        }else{
            $sql3 = "UPDATE Playlists SET p_name=\"$nname\" WHERE list_id=\"$pid\"";
            $result3 = $conn->query($sql3);

            if ($result3 === TRUE) {
                $isRenamed = TRUE;
                $msg = "<br>$list renamed to $nname.<br>";
            } else {
                $msg .= "Error: $sql3 <br> $conn->error";
            } 
        }

        if($isRenamed === TRUE){
            header('Location: '. $url . $path . 'index.php?page=playlists&list=' . $nname . '&msg=' . $msg);
        } else {
            header('Location: '. $url . $path . 'index.php?page=playlists&list=' . $list . '&msg=' . $msg);
        }

    } else if(isset($_POST['createPlaylist'])){
        $list = $_POST['p_name'];

        //queries the entered playlist with current user
        $sql2 = "SELECT p_name FROM Playlists WHERE p_name=\"$list\" AND user_id=$session_user LIMIT 0 , 30";
        $result2 = $conn->query($sql2);
        
        if($result2->num_rows > 0){
            $msg = "There is already a playlist named <i>$list</i> on your account. <br>";
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
            $sql = "INSERT INTO Playlists VALUES ('$id', '$session_user', '$list')";
            $result3 = $conn->query($sql);

            if ($result3 === TRUE) {
                $msg = "<i>$list</i> added to your playlists.";
            } else {
                $msg .= "Error: $sql <br> $conn->error";
            }            
        }   

        header('Location: '. $url . $path . 'index.php?page=playlists&list=all&msg=' . $msg);
    }
}

closeCon($conn);

echo $html;

?>