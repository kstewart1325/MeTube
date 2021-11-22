<?php 

function getChannelPage(){
     include 'db_connection.php';

     $conn = openCon();

     if(!session_id()) session_start();
     $user_id = $_SESSION['user_id'];

     //gets user's name
     $sql = "SELECT `first_name` FROM Account WHERE `user_id`=\"$user_id\"";
     $result = $conn->query($sql);
     $row = $result->fetch_assoc();
     $name = $row["first_name"];

     $html = "<div class=\"welcome\" style=\"padding-left: 20px\">";
     $html .= "<h2>Hello $name!</h2>";
     $html .= "</div>";

     $html .= <<< PAGE
     <div class="home">
       <div class="row">
            <h3>My Uploads</h3>
     PAGE;

     //sorts and displays media uploaded by current user and sorted by date
     $sql = "SELECT * FROM Mediafiles WHERE `user_id`=\"$user_id\" ORDER BY date_created DESC";
     $result = $conn->query($sql);
     if($result->num_rows > 0){
          while($row = $result->fetch_assoc()){
               $title = $row['media_title'];
               $desc = $row['description'];
               $media_id = $row['media_id'];

               $html .= "<a href=\"index.php?page=media&id=$media_id\">";
               $html .= "<div class=\"media\">";
               $html .= "<h4>$title</h4>";
               $html .= "<p>$desc</p>";
               $html .= "</div></a>";
          }
     } else {
          $html .= "<div class=\"media\" style=\"border: 0px\"></div>";
     }

     $html .= <<< PAGE
       </div>
       <div class="row">
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