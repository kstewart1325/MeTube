<?php 

function getMediaPage(){
    include 'db_connection.php';

     $conn = openCon();

     if(!session_id()) session_start();
     $user_id = $_SESSION['user_id'];

     $html .= <<< PAGE
     <div class="media-page">
       <div class="display">
     PAGE;

     //display appropriate media file
     $sql = "SELECT * FROM Mediafiles WHERE `user_id`=\"$user_id\" ORDER BY date_created DESC";
     $result = $conn->query($sql);
     if($result->num_rows > 0){
          while($row = $result->fetch_assoc()){
               $title = $row['media_title'];
               $desc = $row['description'];

               $html .= "<div class=\"media\">";
               $html .= "<h4>$title</h4>";
               $html .= "<p>$desc</p>";
               $html .= "</div>";
          }
     }

    $html .= <<< PAGE
       </div>
       <div class="meta-data">
    PAGE;

    //displays metadata of media file

    $html .= <<< PAGE
       </div>
       <div class="comments">
    PAGE;

    //diplays comments in hierarchial order

    $html .= <<< PAGE
       </div>
    </div>
    PAGE;

    closeCon($conn);
    return $html;
}

?>