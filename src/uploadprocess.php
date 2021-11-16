<?php

$path = "MeTube/src/";
$url = "http://localhost:8070/";

include 'db_connection.php';
$conn = OpenCon();

$resubmit = false;
$error_message = "";

// NEED TO GET SESSION USER ID
$session_user = 2;

if($_SERVER['REQUEST_METHOD']=="POST"){
    //stores data from form
    // $file = $_POST['file'];
    $media_title = $_POST['title'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $keywords = $_POST['keywords']; // CANNOT DO THIS, NEED TO PARSE KEYWORDS

    //checks if values are empty, ask them to resubmit if not
    if(empty($file) || empty($media_title) || empty($category)){
        $error_message = "<br>Required fields left blank. Please try again.<br>";
        $resubmit = true;
    }

    // NEED TO GET SIZE AND TYPE
    if($_FILES['mediafile']['size'] > 0){
        $filename = $_FILES['mediafile']['name'];
        $tmpName  = $_FILES['mediafile']['tmp_name'];
        $size = $_FILES['mediafile']['size'];
        $type = $_FILES['mediafile']['type'];
    }else{
        
    }

    //queries username and email
    $sql = "SELECT username FROM Account WHERE username=\"$username\" LIMIT 0 , 30";
    $result = $conn->query($sql);

    $sql2 = "SELECT email FROM Account WHERE email=\"$email\" LIMIT 0 , 30";
    $result2 = $conn->query($sql2);

    //checks if username and/or email already in use
    if($result->num_rows > 0 && $result2->num_rows > 0) {
        $error_message = "<br>Username and email already in use. Please choose another.<br>";
        $resubmit = true;
    } else if($result->num_rows > 0){
        $error_message = "<br>Username already in use. Please choose another.<br>";
        $resubmit = true;
    } else if($result2->num_rows > 0){
        $error_message = "<br>Email already in use. Please choose another.<br>";
        $resubmit = true;
    }

    // adds info to 'Mediafiles' table
    if($resubmit === false){
        //calculates appropriate media_id
        $id = 0;
        $sql = "SELECT MAX(media_id) AS id FROM Mediafiles";
        $result = $conn->query($sql);
        if($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $id = $row["id"] + 1;
        }
    

        $sql = "INSERT INTO Mediafiles VALUES 
        ('$id', '$session_user', '$media_title', '$type', '$size', '$category', CURRENT_TIMESTAMP, '0', '$description', '$file')";
        $result = $conn->query($sql);

        if ($result === TRUE) {
            header('Location: '. $url . $path . 'channel.html');
        } else {
            $echo("Error: " . $sql . "<br>" . $conn->error);
        }
    }

} else {
    echo "POST not submitted<br>";
}

CloseCon($conn);

//displays signup page with appropriate error message
if($resubmit === true){
    include 'upload.php';
    echo $error_message;
}

 ?>