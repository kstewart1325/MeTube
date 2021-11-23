<?php

function getBrowse($cat){
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

    $html .= <<< HEAD
    <div style="width: 90%; margin-left: 5%;" class="media-header">
        <div class="media-header-left">
            <h3 style="float: left; margin-left: 5px">Browse by Category</h3>
        </div>
    </div>
    HEAD;

    $html .= <<< PAGE
     <div class="playlists">
    
    PAGE;

    //  <br><p style="float: left; margin-left: 5%;">$msg</p>

    if($cat !== "all"){
        $html .= <<< PAGE
        <div style="float: left" class="row">
            <a href="index.php?page=browse&cat=$cat"><h3>$cat</h3></a>
        PAGE;

        //displays specific category
        $sql = "SELECT `media_title` , `description` , `media_id`
        FROM Mediafiles
        WHERE category = '$cat'
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
                    </div>
                 </div>
                 MEDIA;
            }
        } else {
            $html .= "<div class=\"media\" style=\"border: 0px; height: 50px;\"><h4>This playlist is empty.</h4></div>";
        }

       $html .= "</div>";

    } else if($cat === "all"){

        //displays all categories
        $sql = "SELECT `name` FROM Categories";
        $result = $conn->query($sql);
        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()){
                $cname = $row["name"];

                $html .= <<< PAGE
                <div style="float: left" class="row">
                    <a href="index.php?page=browse&cat=$cname"><h3>$cname</h3></a>
                PAGE;
        
                //displays specific category's files
                $sql2 = "SELECT `media_title` , `description` , `media_id`
                FROM Mediafiles
                WHERE category = '$cname'
                LIMIT 0 , 8";
                $result2 = $conn->query($sql2);
                
                if($result2->num_rows > 0){
                    while($row2 = $result2->fetch_assoc()){
                        $title = $row2['media_title'];
                        $desc = $row2['description'];
                        $media_id = $row2['media_id'];

                        $html .= <<< MEDIA
                        <a href="index.php?page=media&id=$media_id">
                        <div class="media">
                            <h4>$title</h4>
                            <p>$desc</p>
                        </div></a>
                        MEDIA;
                    }
                } else {
                        $html .= "<div class=\"media\" style=\"border: 0px;  height: 50px;\"><h4>This category is empty.</h4></div>";
                }

                $html .= "</div>";
                
            }
        }else{
            $html .= "Error fetching Categories.";
        }
    }

     $html .= <<< PAGE
     </div>
     PAGE;

    CloseCon($conn);
    
    return $html;
}

?>


