<?php
  
$path = "MeTube/src/";
$url = "http://localhost:8070/";

if(!session_id()) session_start();
  
include_once 'db_connection.php';
$conn = OpenCon();

$isLoggedIn = $_SESSION['isLoggedIn'];
$current_user_id = $_SESSION['user_id'];
$media_id = $_SESSION['media_id'];

$resubmit = false;
$error_message = "";

if($_SERVER['REQUEST_METHOD']=="POST"){
    //stores data from form
    $comment = $_POST['comment'];

    //checks if values are empty, ask them to resubmit if not
    if(empty($comment)){
        $error_message = "<br>Field left blank.<br>";
        $resubmit = true;
    }

    if($resubmit === false){
        //calculates appropriate comment_id
        $id = 0;
        $sql = "SELECT MAX(comment_id) AS id FROM Comment";
        $result = $conn->query($sql);
        if($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $id = $row["id"] + 1;
        }

        $sql = "INSERT INTO Comment VALUES ('$id', '$current_user_id', '$media_id', CURRENT_TIMESTAMP, '0', '1', '" . $comment . "')";
        $result = $conn->query($sql);

        if($result === true){
            header('Location: '. $url . $path . 'index.php?page=media&id=' . $media_id);
        } else {
            header('Location: '. $url . $path . 'index.php?page=media&id=' . $media_id . "&msg=comerr");
        }
    } else {
        header('Location: '. $url . $path . 'index.php?page=media&id=' . $media_id . "&msg=nocom");
    }
}

CloseCon($conn);

?>