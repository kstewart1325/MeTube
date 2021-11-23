<?php 

function getChannelPage($user_id, $msg){
     include_once 'db_connection.php';

     $conn = openCon();

     if(!session_id()) session_start();
     $current_user_id = $_SESSION['user_id'];
     $isLoggedIn = $_SESSION['isLoggedIn'];

     $html = "";
     $isSubscribed = false;
     $error_message = "";

     if($msg === "sub"){
         $error_message = "You must be logged in to subscribe.";
     }

     // checks if user is subscibed to channel
     if($isLoggedIn && $current_user_id != $user_id){
          $sql = "SELECT * FROM Subscriptions WHERE `channel`=\"$user_id\" AND `subscriber`=\"$current_user_id\"";
          $result = $conn->query($sql);
          if($result->num_rows >0){
               $isSubscribed = true;
          }
     }

     // displays owner and subscribe button if other user's channel
     $sql = "SELECT * FROM Account WHERE `user_id`=\"$user_id\"";
     $result = $conn->query($sql);
     $row = $result->fetch_assoc();
     $fullname = $row["first_name"] . " " . $row['last_name'];
     $num_subs = $row["num_subs"];
     $username = $row["username"];
     $email = $row["email"];
     $birthday = $row["birthday"];

     $html .= <<< HEAD
     <div style="width: 90%; margin-left: 5%;" class="media-header">
          <div class="media-header-left">
               <img style="float: left; width: 40px; height: 40px" src="../media/profile-icon.png">
               <h3 style="float: left; margin-left: 5px">$fullname</h3>
          </div>
          <div style="float: right; width: 70%;" class="media-header-right">
          <a style="float: right; border: 0px;" href="login.php">$error_message</a>
     HEAD;

     if($isLoggedIn && $current_user_id == $user_id){
          $html .= <<< HEAD
               <div class="info">
                    <p>Subscribers: $num_subs</p>
               </div>
               <div class="info">
                    <p>Birthday: $birthday</p>
               </div>
               <div class="info">
                    <p>Email: $email</p>
               </div>
               <div class="info">
                    <p>Username: $username</p>
               </div>
          HEAD;
     } else if(!$isLoggedIn){
          $html .= "<a style=\"float: right;\" href=\"index.php?page=channel&id=$user_id&msg=sub\">Subscribe</a>";
      } else if($isSubscribed){
          $html .= "<a style=\"float: right; background-color: dodgerblue; color: white;\" href=\"subscribe.php?page=channel&id=$user_id\" >Subscribed</a>";
      } else {
          $html .= "<a style=\"float: right;\" href=\"subscribe.php?page=channel&id=$user_id\" >Subscribe</a>";
      }
     
     $html .= <<< PAGE
          </div>
     </div>
     <div class="channel">
       <div style="float: left" class="row">
            <h3>Uploads</h3>
     PAGE;

     //sorts and displays media uploaded by current user and sorted by date
     $sql = "SELECT * FROM Mediafiles WHERE `user_id`=\"$user_id\" ORDER BY date_created DESC LIMIT 0 , 8";
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
          $html .= "<div class=\"media\" style=\"border: 0px\"><h4>No uploads.</h4></div>";
     }

     $html .= <<< PAGE
       </div>
     PAGE;

     if($isLoggedIn && $current_user_id === $user_id){
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
               $html .= "<div class=\"media\" style=\"border: 0px\"><h4>No media saved to Favorites.</h4></div>";
          }

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