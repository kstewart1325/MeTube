<?php 

function getMediaPage($media_id, $msg){
    include_once 'db_connection.php';

    $conn = openCon();

    $imageTypes = array("image/jpeg", "image/pjpeg", "image/png");
    $audioTypes = array("audio/mp3", "audio/wma");
    $videoTypes = array("video/mp4");

    if(!session_id()) session_start();
    $current_user_id = $_SESSION['user_id'];
    $isLoggedIn = $_SESSION['isLoggedIn'];

    $isSubscribed = false;
    $error_message = "";

    if($msg === "sub"){
        $error_message = "You must be logged in to subscribe.";
    }

    $html = <<< PAGE
     <div class="media-page">
    PAGE;

    //display appropriate media file
    $sql = "SELECT * FROM Mediafiles WHERE `media_id`=\"$media_id\"";
    $result = $conn->query($sql);
    if($result->num_rows > 0){
        $row = $result->fetch_assoc();
        $media = $row['media_path'];
        $media_type = $row['media_type'];
        $media_title = $row['media_title'];
        $desc = $row['description'];
        $date_uploaded = $row['date_created'];
        $media_user_id = $row['user_id'];
        $view_count = $row['view_count'];
        $category = $row['category'];
        $media_path = $row['media_path'];
        $keywords = $row['keywords'];

        $sql = "SELECT * FROM Account WHERE `user_id`=\"$media_user_id\"";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $fullname = $row['first_name'] . " " . $row['last_name'];

        // checks if user is subscibed to channel
        if($isLoggedIn && $current_user_id != $media_user_id){
            $sql = "SELECT * FROM Subscriptions WHERE `channel`=\"$media_user_id\" AND `subscriber`=\"$current_user_id\"";
            $result = $conn->query($sql);
            if($result->num_rows >0){
                $isSubscribed = true;
            }
        }

        $html .= "<div id=\"media-container\" class=\"display\">";

        if(in_array($media_type, $imageTypes)){
            $html .= "<img height=\"445\" src=\"$media\" alt=\"$media_title\">";
        } else if(in_array($media_type, $videoTypes)){
            $html .= "<video width=\"65%\" height=\"440\" autoplay>";
            $html .= "<source src=\"$media\" type=\"$media_type\">";
            $html .= "Your browser doesn't support the video element.</br></video>";
        } else if(in_array($media_type, $audioTypes)){
            $html .= "<audio controls>";
            $html .= "<source src=\"$media\" type=\"$media_type\">";
            $html .= "Your browser doesn't support the audio element.</br></audio>";
        } else {
            $html .= "<p>Unable to display media.</p>";
        }
    
        $html .= <<< PAGE
        </div>
        PAGE;
         
        // displays owner and subscribe button
        $html .= <<< HEADER
        <div class="media-header">
            <a href = "index.php?page=channel&id=$media_user_id">
                <div class="media-header-left">
                <img style="float: left; width: 40px; height: 40px" src="../media/profile-icon.png">
                <h3 style="float: left; margin-left: 5px">$fullname</h3>
                </div>
            </a>
            <div style="width: 60%;" class="media-header-right">
                <div style="float: left;" class="info">
                    <p>$error_message</p>
                <div>
        HEADER;

        if($isLoggedIn && $current_user_id == $media_user_id){
            $html .= "";
        } else if($isSubscribed){
            $html .= "<a style=\"background-color: dodgerblue; color: white;\" href=\"subscribe.php?page=media&id=$media_user_id&media=$media_id\" >Subscribed</a>";
        } else if(!$isLoggedIn){
            $html .= "<a href=\"index.php?page=media&id=$media_id&msg=sub\">Subscribe</a>";
        }

        //displays metadata of media file
        $html .= <<< DATA
            </div>
        </div>
        <div class="meta-data">
            <hr style="margin-bottom: 10px;" class="solid">
            <div style="float: left; margin-left: 10px;" class="data-left">
                <h3>$media_title</h3>
                $desc<br>
                <p>$view_count views  |  Date uploaded: $date_uploaded</p>
                <p>Category: $category</p>
                <p>Keywords: $keywords</p><br>
            </div>
            <div style="float: right" class="data-right">
                <a href="$media_path" download>Download</a>"
                <a href="">Add to Playlist</a>
            </div>
        </div>
        DATA;        

        //diplays comments in hierarchial order
        $html .= <<< COMMENTS
        <div class="comments">
        </div>
        COMMENTS;
    }

    $html .= <<< PAGE
    </div>
    PAGE;

    closeCon($conn);
    return $html;
}

?>