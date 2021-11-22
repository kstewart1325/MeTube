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
    $username = $_POST['username'];

    //checks if values are empty, ask them to resubmit if not
    if(empty($username)){
        $error_message = "<br>Username left blank. Please enter a valid username.<br>";
        $resubmit = true;
    }else{
        //queries username entered to get user ID
        $sql = "SELECT user_id FROM Account WHERE username=\"$username\" LIMIT 0 , 30";
        $result = $conn->query($sql);

        //validates the entered username
        if($result->num_rows > 0){
            $row = $result->fetch_assoc();
            $contact = $row['user_id'];
        }else{
            $error_message = "<br>The username <i>$username</i> is invalid. Please try again. <br>";
            $resubmit = true;
        } 
    }

    if(isset($_POST['removeContact']) && $resubmit === false){
        // remove from contact list
        //queries the entered contact and current user in session
        $sql2 = "SELECT cid FROM Contacts WHERE contact_id=\"$contact\" AND user_id=\"$current_user_id\"";
        $result2 = $conn->query($sql2);

        if($result2->num_rows > 0){
            $row = $result2->fetch_assoc();
            $c_id = $row['cid'];

            // remove info from 'Contacts' database
            $sql = "DELETE FROM Contacts WHERE cid=\"$c_id\"";
            $result = $conn->query($sql);

            if ($result === TRUE) {
                // header('Location: '. $url . $path . 'contacts.php');
                $error_message = "<br><i>$username</i> removed from Contact List<br>";
                header('Location: '. $url . $path . 'index.php?page=contacts&msg=' . $error_message);
            } else {
                $error_message .= "Error: $sql <br> $conn->error";
            }

        }else{
            $error_message = "<br>Contact <i>$username</i> does not exist.<br>";
            $resubmit = true;
        }          
            
    }else if($resubmit === false){
        // add to contact list

        //queries the entered contact and current user
        $sql2 = "SELECT contact_id FROM Contacts WHERE contact_id=$contact AND user_id=$current_user_id LIMIT 0 , 30";
        $result2 = $conn->query($sql2);
        
        if($result2->num_rows > 0){
            $error_message = "<br>Contact <i>$username</i> already added. <br>";
            $resubmit = true;
        }else{
            //calculates appropriate cid for a unique value (to allow deletions)
            $id = 0;
            $sql = "SELECT MAX(cid) AS id FROM Contacts";
            $result = $conn->query($sql);
            if($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $id = $row["id"] + 1;
            }
    
            // add info to 'Contacts' database
            $sql = "INSERT INTO Contacts VALUES ('$id', '$current_user_id', '$contact')";
            $result3 = $conn->query($sql);
    
            if ($result3 === TRUE) {
                $error_message = "<br><i>$username</i> added to Contact List<br>";
                header('Location: '. $url . $path . 'index.php?page=contacts&msg=' . $error_message);
            } else {
                $error_message .= "Error: $sql <br> $conn->error";
            }            
        }      
    }
}

if($resubmit === true){
    header('Location: '. $url . $path . 'index.php?page=contacts&msg=' . $error_message);
}

CloseCon($conn);

?>