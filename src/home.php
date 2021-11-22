<?php

function getHomePage(){
     include 'db_connection.php';

     $conn = openCon();

     if(!session_id()) session_start();
     $current_user_id = $_SESSION['user_id'];
     $isLoggedIn = $_SESSION['isLoggedIn'];

     $html = <<< PAGE
     <div class="home">
       <div class="row">
            <h3>Recent Uploads</h3>
     PAGE;

     //sorts and displays media uploaded by current user and sorted by date
     $sql = "SELECT * FROM Mediafiles ORDER BY date_created DESC";
     $result = $conn->query($sql);
     if($result->num_rows > 0){
          while($row = $result->fetch_assoc()){
               $title = $row['media_title'];
               $desc = $row['description'];
               $media_id = $row['media_id'];
               $media_user_id = $row['user_id'];

               $newsql = "SELECT `first_name`, `last_name` FROM Account WHERE `user_id`=\"$media_user_id\"";
               $newresult = $conn->query($newsql);
               $newrow = $newresult->fetch_assoc();
               $fullname = $newrow['first_name'] . " " . $newrow['last_name'];

               $html .= <<< PAGE
               <a href="index.php?page=media&id=$media_id">
               <div class="media">
                    <h4>$title</h4>
                    <h5>$fullname</h5>
                    <p>$desc</p>
               </div></a>
               PAGE;
          }
     } else {
          $html .= "<div class=\"media\" style=\"border: 0px\"></div>";
     }

     $html .= <<< PAGE
       </div>
       <div class="row">
            <h3>Most Popular </h3>
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