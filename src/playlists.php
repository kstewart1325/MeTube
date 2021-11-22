<?php

function getPlaylists($list, $msg){
    $path = "MeTube/src/";
    $url = "http://localhost:8070/";
  
    include_once 'db_connection.php';
    $conn = OpenCon();
    
    if(!session_id()) session_start();    
    $isLoggedIn = $_SESSION['isLoggedIn'];  
    $current_user_id = $_SESSION['user_id'];

    $html = "";
    $sql = "";
    $result = "";
      
    if($list === "all"){
        $html .= <<< HEAD
        <div style="width: 90%; margin-left: 5%;" class="media-header">
             <div class="media-header-left">
                <h3 style="float: left; margin-left: 5px">Playlists</h3>
             </div>
             <div style="float: right; width: 70%;" class="media-header-right">
                <a style="float: right;" href="playlist_update.php?action=create">Create Playlist</a>
             </div>
        </div>
        HEAD;
    } else if($list === "favorites"){
        $html .= <<< HEAD
        <div style="width: 90%; margin-left: 5%;" class="media-header">
            <div class="media-header-left">
                <h3 style="float: left; margin-left: 5px">Favorites</h3>
            </div>
            <div style="float: right; width: 70%;" class="media-header-right">
            </div>
        </div>
        HEAD;
    } else {
        $html .= <<< HEAD
        <div style="width: 90%; margin-left: 5%;" class="media-header">
            <div class="media-header-left">
                <h3 style="float: left; margin-left: 5px">$list</h3>
            </div>
            <div style="float: right; width: 70%;" class="media-header-right">
                <a style="float: right;" href="playlist_update.php?action=delete&list=$list">Delete Playlist</a>
                <a style="float: right;" href="playlist_update.php?action=rename&list=$list">Rename Playlist</a>
            </div>
        </div>
        HEAD;
    }

    $html .= <<< PAGE
     <div class="playlists">
     <br><p style="float: left; margin-left: 5%;">$msg</p>
    PAGE;

    if($list === "all"){
        $html .= <<< PAGE
        <div style="float: left" class="row">
            <a href="index.php?page=playlists&list=favorites"><h3>Favorites </h3></a>
        PAGE;

        //sorts and displays favorites playlist
        $sql = "SELECT Mediafiles.media_id, `description` , `media_title` FROM Mediafiles
        INNER JOIN Favorites ON Mediafiles.media_id = Favorites.media_id
        WHERE Favorites.user_id='$current_user_id'
        LIMIT 0 , 8";
        $result = $conn->query($sql);
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                    $title = $row['media_title'];
                    $desc = $row['description'];
                    $media_id = $row['media_id'];

                    $html .= <<< MEDIA
                    <a href="index.php?page=media&id=$media_id">
                    <div class="media">
                        <h4>$title</h4>
                        <p>$desc</p>
                    </div></a>
                    MEDIA;
            }
        } else {
            $html .= "<div class=\"media\" style=\"border: 0px; height: 50px;\"><h4>This playlist is empty.</h4></div>";
        }

        $html .= "</div>";

    } else if($list === "favorites"){
        $html .= <<< PAGE
        <div style="float: left" class="row">
            <h3>Favorites </h3>
        PAGE;

        //sorts and displays favorites playlist
        $sql = "SELECT Mediafiles.media_id, `description` , `media_title` FROM Mediafiles
        INNER JOIN Favorites ON Mediafiles.media_id = Favorites.media_id
        WHERE Favorites.user_id='$current_user_id'
        LIMIT 0 , 8";
        $result = $conn->query($sql);
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                    $title = $row['media_title'];
                    $desc = $row['description'];
                    $media_id = $row['media_id'];

                    $html .= <<< MEDIA
                    <div class="media">
                        <h4>$title</h4>
                        <p>$desc</p>
                        <div class="media-buttons">
                            <a href="index.php?page=media&id=$media_id">View</a>
                            <a href="favorites_update.php?id=$media_id">Remove</a>
                        </div>
                    </div>
                    MEDIA;
            }
        } else {
            $html .= "<div class=\"media\" style=\"border: 0px; height: 50px;\"><h4>This playlist is empty.</h4></div>";
        }

        $html .= "</div>";
    }

    if($list !== "all" && $list !== "favorites"){
        $html .= <<< PAGE
        <div style="float: left" class="row">
            <a href="index.php?page=playlists&list=$list"><h3>$list</h3></a>
        PAGE;

        //displays specific playlist
        $sql = "SELECT `media_title` , `description` , Mediafiles.media_id, Playlists.list_id, Playlist_Data.entry_num
        FROM Mediafiles
        INNER JOIN Playlist_Data ON Mediafiles.media_id = Playlist_Data.media_id
        INNER JOIN Playlists ON Playlist_Data.list_id = Playlists.list_id
        WHERE Playlists.user_id = '$current_user_id'
        AND Playlists.p_name = '$list'
        LIMIT 0 , 8";
        $result = $conn->query($sql);

        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                 $title = $row['media_title'];
                 $desc = $row['description'];
                 $media_id = $row['media_id'];
                 $list_id = $row['list_id'];
                 $entry_num = $row['entry_num'];
  
                 $html .= <<< MEDIA
                 <div class="media">
                    <h4>$title</h4>
                    <p>$desc</p>
                    <div class="media-buttons">
                        <a href="index.php?page=media&id=$media_id">View</a>
                        <a href="platlist_update.php?action=removeMedia&list=$list&id=$media_id">Remove</a>
                    </div>
                 </div>
                 MEDIA;
            }
        } else {
            $html .= "<div class=\"media\" style=\"border: 0px; height: 50px;\"><h4>This playlist is empty.</h4></div>";
        }

       $html .= "</div>";

    } else if($list === "all"){
        //displays all playlists
        $sql = "SELECT * FROM Playlists WHERE `user_id`=\"$current_user_id\"";
        $result = $conn->query($sql);

        if($result->num_rows > 0){
           while($row = $result->fetch_assoc()){
                $playlist_name = $row['p_name'];
                $list_id = $row['list_id'];

                $html .= <<< PAGE
                <div style="float: left" class="row">
                    <a href="index.php?page=playlists&list=$playlist_name"><h3>$playlist_name</h3></a>
                PAGE;
        
                //displays specific playlist
                $sql2 = "SELECT `media_title` , `description` , Mediafiles.media_id, Playlists.list_id, Playlist_Data.entry_num
                FROM Mediafiles
                INNER JOIN Playlist_Data ON Mediafiles.media_id = Playlist_Data.media_id
                INNER JOIN Playlists ON Playlist_Data.list_id = Playlists.list_id
                WHERE Playlists.user_id = '$current_user_id'
                AND Playlists.p_name = '$playlist_name'
                LIMIT 0 , 8";
                $result2 = $conn->query($sql2);
                
                if($result2->num_rows > 0){
                    while($row2 = $result2->fetch_assoc()){
                        $title = $row2['media_title'];
                        $desc = $row2['description'];
                        $media_id = $row2['media_id'];
                        $list_id = $row2['list_id'];
                        $entry_num = $row2['entry_num'];

        
                        $html .= <<< MEDIA
                        <a href="index.php?page=media&id=$media_id">
                        <div class="media">
                            <h4>$title</h4>
                            <p>$desc</p>
                        </div></a>
                        MEDIA;
                    }
                } else {
                        $html .= "<div class=\"media\" style=\"border: 0px;  height: 50px;\"><h4>This playlist is empty.</h4></div>";
                }

                $html .= "</div>";
           }
        }

    }

     $html .= <<< PAGE
     </div>
     PAGE;

    CloseCon($conn);
    
    return $html;
}

?>


