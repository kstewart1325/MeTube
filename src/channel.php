<?php 

function getChannelPage($user_id){
     include 'db_connection.php';

     $conn = openCon();

     if(!session_id()) session_start();
     $current_user_id = $_SESSION['user_id'];
     $isLoggedIn = $_SESSION['isLoggedIn'];

     $html = "";
     $isSubscribed = false;

     // checks if user is subscibed to channel
     if($isLoggedIn && $current_user_id != $user_id){
          $sql = "SELECT * FROM Subscriptions WHERE `channel`=\"$user_id\" AND `subscriber`=\"$current_user_id\"";
          $result = $conn->query($sql);
          if($result->num_rows >0){
               $isSubscribed = true;
          }
     }

     // displays owner and subscribe button if other user's channel
     $sql = "SELECT `first_name`, `last_name`, `num_subs` FROM Account WHERE `user_id`=\"$user_id\"";
     $result = $conn->query($sql);
     $row = $result->fetch_assoc();
     $fullname = $row["first_name"] . " " . $row['last_name'];
     $num_subs = $row["num_subs"];

     $html .= <<< HEAD
     <div style="width: 90%; margin-left: 5%;" class="media-header">
          <div class="media-header-left">
               <img style="float: left; width: 40px; height: 40px" src="../media/profile-icon.png">
               <h3 style="float: left; margin-left: 5px">$fullname</h3>
          </div>
          <div class="media-header-right">
     HEAD;

     if($isLoggedIn && $current_user_id == $user_id){
          $html .= "<p>Subscribers: $num_subs";
     } else if($isSubscribed){
          $html .= "<a style=\"background-color: dodgerblue; color: white;\" href=\"subscribe.php?page=channel&id=$user_id\" >Subscribed</a>";
     } else {
          $html .= "<a href=\"subscribe.php?page=channel&id=$user_id\" >Subscribe</a>";
     }
     
     $html .= <<< PAGE
          </div>
     </div>
     <div class="home">
       <div style="float: left" class="row">
            <h3>Uploads</h3>
     PAGE;

     //sorts and displays media uploaded by current user and sorted by date
     $sql = "SELECT * FROM Mediafiles WHERE `user_id`=\"$user_id\" ORDER BY date_created DESC";
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
          $html .= "<div class=\"media\" style=\"border: 0px\"></div>";
     }

     $html .= <<< PAGE
       </div>
       <div style="float: left" class="row">
            <h3>Favorites </h3>
            <img class="media" src="../media/image-placeholder.png" alt="Image Placeholder">
            <img class="media" src="../media/video-placeholder.png" alt="Video Placeholder">
            <img class="media" src="../media/video-placeholder.png" alt="Video Placeholder">
       </div>
    </div>
    PAGE;

    closeCon($conn);
    return $html;
}

?>