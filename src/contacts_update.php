
<?php

$path = "MeTube/src/";
$url = "http://localhost:8070/";

if(!session_id()) session_start();

include 'db_connection.php';
$conn = OpenCon();

$resubmit = false;
$error_message = "";

if(isset($_SESSION['user_id'])) $session_user=$_SESSION['user_id'];

// KATE: NEED THE CURRENT SESSION'S USER'S ID # STORED IN A VAR CALLED "session_user"
// or occurences of session_user replaced by w/e you're storing it. thx ; )

if($_SERVER['REQUEST_METHOD']=="POST"){
    //stores data from form
    $username = $_POST['username'];

    //checks if values are empty, ask them to resubmit if not
    if(empty($username)){
        $error_message = "<br>Username left blank. Please enter a valid username.<br>";
        $resubmit = true;
    }

    //queries username entered to get user ID
    $sql = "SELECT user_id FROM Account WHERE username=\"$username\" LIMIT 0 , 30";
    $result_id = $conn->query($sql);

    //validates the entered username
    if($result_id->num_rows <= 0){
        $error_message = "<br>Username is invalid. Please try again. <br>";
        $resubmit = true;
    } 

    if (isset($_POST['removeContact'])) {
        // remove from contact list
        //queries the entered contact and current user in session
        $sql2 = "SELECT contact_id FROM Contact WHERE contact_id=\"$result_id\" AND user_id=\"$session_user\" LIMIT 0 , 30";
        $result2 = $conn->query($sql2);

        if($result2->num_rows <= 0){
            $error_message = "<br>Contact does not exist. Please try again.<br>";
            $resubmit = true;
        }    

        // add info to 'Contacts' database
        if($resubmit === false){
            $sql = "DELETE FROM Contact WHERE contact_id=\"$result_id\" AND user_id=\"$session_user\" LIMIT 0 , 30";
            $result = $conn->query($sql);

            if ($result === TRUE) {
                header('Location: '. $url . $path . 'contacts.html');
            } else {
                $echo("Error: " . $sql . "<br>" . $conn->error);
            }
        } 
        
      } else {
        // add to contact list
        //queries the entered contact and current user
        $sql2 = "SELECT contact_id FROM Contact WHERE contact_id=\"$result_id\" AND user_id=\"$session_user\" LIMIT 0 , 30";
        $result2 = $conn->query($sql2);

        if($result2->num_rows > 0){
            $error_message = "<br>Contact already added. <br>";
            $resubmit = true;
        }    

        //calculates appropriate cid for a unique value (to allow deletions)
        $id = 0;
        $sql = "SELECT MAX(cid) AS id FROM Account";
        $result = $conn->query($sql);
        if($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $id = $row["id"] + 1;
        }

        // add info to 'Contacts' database
        if($resubmit === false){
            $sql = "INSERT INTO Contacts VALUES ('$id', '$session_user', '$result_id')";
            $result = $conn->query($sql);

            if ($result === TRUE) {
                header('Location: '. $url . $path . 'contacts.html');
            } else {
                $echo("Error: " . $sql . "<br>" . $conn->error);
            }
        } 
      } 
        

} else {
    echo "POST not submitted<br>";
}

CloseCon($conn);

//displays contact page with appropriate error message
if($resubmit === true){
    include 'contacts.php';
    echo $error_message;
}

 ?>