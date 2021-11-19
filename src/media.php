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

        $html .= "<h2 style=\"text-align: center\">$media_title</h2>";
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

        //displays options to edit media if logged in and if media belongs to user
        if($isLoggedIn && ($user_id === $media_user_id)){
            $html .= <<< PAGE
            <div class="media-header">
                <div class="media-header-left">
                    <a href="" class="logo">MeTube</a>
                </div>
                <div class="media-header-right">
                    <a class="active" class="link" style="margin-right: 2px" href="index.php?page=home">Home</a>
                </div>
            </div>
            PAGE;
        }

        //displays metadata of media file
        $html .= "<div class=\"meta-data\">";
        $html .= "<p>$desc</p>";
        $html .= "<p>$date_uploaded</p>";

        $html .= <<< PAGE
        </div>
        <div class="comments">
        PAGE;

        //diplays comments in hierarchial order
        $html .= <<< PAGE
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