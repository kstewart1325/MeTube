<?php
include_once 'db_connection.php';

$path = "MeTube/src/";
$url = "http://webapp.computing.clemson.edu/~cgstewa/";

if(!session_id()) session_start();

$isLoggedIn = $_SESSION['isLoggedIn'];
$current_user_id = $_SESSION['user_id'];

$conn = openCon();

if($_SERVER['REQUEST_METHOD']=="GET"){
    $user_id = $_GET['id'];
    $page = $_GET['page'];
    $media = "";

    if($page == "media"){
        $media = $_GET['media'];
    }

    // adds or removes subscription from database
    $sql = "SELECT * FROM Subscriptions WHERE `channel`=\"$user_id\" AND `subscriber`=\"$current_user_id\"";
    $result = $conn->query($sql);
    if($result->num_rows > 0){
        $sql = "DELETE FROM Subscriptions WHERE `channel`=\"$user_id\" AND `subscriber`=\"$current_user_id\"";
        $result = $conn->query($sql);

        // decrements subscriber count
        $sql = "SELECT `num_subs` FROM Account WHERE `user_id`=\"$user_id\"";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $new_count = $row['num_subs'] - 1;

        $sql = "UPDATE Account SET `num_subs`=\"$new_count\" WHERE `user_id`=\"$user_id\"";
        $result = $conn->query($sql);
    } else {
        $sql = "INSERT INTO Subscriptions VALUES ('$user_id','$current_user_id')";
        $result = $conn->query($sql);

        // increments subscriber count
        $sql = "SELECT `num_subs` FROM Account WHERE `user_id`=\"$user_id\"";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $new_count = $row['num_subs'] + 1;

        $sql = "UPDATE Account SET `num_subs`=\"$new_count\" WHERE `user_id`=\"$user_id\"";
        $result = $conn->query($sql);
    }
    
    if($page == "channel"){
        header('Location: '. $url . $path . 'index.php?page=' . $page .'&id=' . $user_id);
    } else {
        header('Location: '. $url . $path . 'index.php?page=' . $page .'&id=' . $media);
    }
}

closeCon($conn);

?>