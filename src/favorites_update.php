
<?php
  $path = "MeTube/src/";
  $url = "http://localhost:8070/";

  include_once 'db_connection.php';  

  if(!session_id()) session_start();
  $isLoggedIn = $_SESSION['isLoggedIn'];
  $session_user = $_SESSION['user_id'];
  
  $conn = openCon();

  $list = "favorites";
  $id = "";
  $msg = "";
  $action = "";

  if($_SERVER['REQUEST_METHOD']=="GET"){
      if(isset($_GET['id'])){
        $id = $_GET['id'];

        //queries if media is favorited by user
        $sql = "SELECT * FROM Favorites WHERE `media_id`='$id' AND `user_id`='$session_user'";
        $result = $conn->query($sql);
        if($result->num_rows > 0){
          $action = "removeMedia";
        } else {
          $action = "addMedia";
        }
      }

      //adds if not favorited, deletes if it is
      if($action === "removeMedia"){
          // Removes media from favorites
          $sql = "DELETE FROM Favorites WHERE `media_id`='$id' AND `user_id`='$session_user'";
          $result = $conn->query($sql);
      
          if ($result === TRUE) {
              $msg = "Media removed from <i>Favorites</i>.";
          } else {
              // $msg = "Error removing media from <i>Favorites</i>.";
              $msg = $sql;
          }
      
          header('Location: '. $url . $path . 'index.php?page=playlists&list=' . $list . '&msg=' . $msg);
      
      } else if($action = "addMedia"){
          // Adds media to favorites          
          $sql2 = "INSERT INTO Favorites VALUES ('$session_user', '$id')";
          $result2 = $conn->query($sql2);
      
          if ($result2 === TRUE) {
              $msg = "Media successfully added to <i>Favorites</i>.";        
          } else {
              $msg = "Error adding media to <i>Favorites</i>.";
          }
      
          header('Location: '. $url . $path . 'index.php?page=playlists&list=' . $list . '&msg=' . $msg);
      } else {
        $msg = "Error editing Favorites.";
        header('Location: '. $url . $path . 'index.php?page=playlists&list=' . $list . '&msg=' . $msg);
      }
  }
  
  closeCon($conn);

?>


