<?php

function search($keyword){
  $path = "MeTube/src/index.php";
  $url = "http://localhost:8070/";

  include_once 'db_connection.php';
  $conn = OpenCon();
  
  $html = <<< PAGE
  <div class="search">
    <div class="row">
      <h3>Search Results: </h3>
  PAGE;

    $myterms = explode(" ", $keyword);

    $found = false;

    foreach ($myterms as $val){
        $sql = "SELECT * FROM Mediafiles WHERE keywords LIKE '%$val%'";
        $result = $conn->query($sql);
        if($result->num_rows > 0){
            $found = true;

            // get each mediafile name and print 
            while($row = $result->fetch_assoc()) {
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
        }
    }

    if(!$found){
      $html .= "<div class=\"media\" style=\"border: 0px\">";
    } 

  $html .= <<< PAGE
    </div>
  </div>
  PAGE;
  
  CloseCon($conn);

  return $html;
}

?>


