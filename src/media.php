<?php 

function getMediaPage($media_id){
    include 'db_connection.php';

    $conn = openCon();

    $imageTypes = array("image/jpeg", "image/pjpeg", "image/png");
    $audioTypes = array("audio/mp3", "audio/wma");
    $videoTypes = array("video/mp4");

    if(!session_id()) session_start();
    $user_id = $_SESSION['user_id'];
    $isLoggedIn = $_SESSION['isLoggedIn'];

    $html = <<< PAGE
     <div class="media-page">
    PAGE;

    //display appropriate media file
    $file = "";
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
        $keywords = "";

        $sql = "SELECT * FROM Account WHERE `user_id`=\"$media_user_id\"";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $fullname = $row['first_name'] . " " . $row['last_name'];

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
    
        $html .= "</div>";

        // displays owner and subscribe button
        $html .= "<div class=\"media-header\">";
        $html .= "<a href = \"index.php?page=channel&id=$media_user_id\"><div class=\"media-header-left\">";
        $html .= "<img style=\"float: left; width: 40px; height: 40px\" src=\"../media/profile-icon.png\">";
        $html .= "<h3 style=\"float: left; margin-left: 5px\">$fullname</h3>";

        $html .= <<< HEAD
            </div></a>
            <div class="media-header-right">
                <a href="" >Subscribe</a>
            </div>
        </div>
        HEAD;

        //displays metadata of media file
        $html .= <<< DATA
        <div class="meta-data">
            <hr style="margin-bottom: 10px;" class="solid">
            <div style="float: left; margin-left: 10px;" class="data-left">
        DATA;

        $html .= "<h3>$media_title</h3>";
        $html .= $desc;
        $html .= "<br>";
        $html .= "<p>$view_count views  |  Date uploaded: $date_uploaded</p>";
        $html .= "<p>Category: $category</p>";
        $html .= "<p>Keywords: $keywords</p><br>";

        $html .= <<< DATA
            </div>
            <div style="float: right" class="data-right">
        DATA;

        $html .= "<a href=\"$media_path\" download>Download</a>";

        $html .= <<< DATA
                <a href="" >Add to Playlist</a>
            </div>
        </div>
        DATA;        

        //diplays comments in hierarchial order
        $html .= <<< PAGE
        <div class="comments">
        </div>
        PAGE;
    }

    $html .= <<< PAGE
    </div>
    PAGE;

    closeCon($conn);
    return $html;
}

?>