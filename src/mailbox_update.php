
<?php

    if(!session_id()) session_start();

    $current_user_id = $_SESSION['user_id'];
    $isLoggedIn = $_SESSION['isLoggedIn'];
    //test variable below
    //$current_user_id = 2;



    $path = "MeTube/src/index.php";
    $url = "http://localhost:8070/";

    include_once 'db_connection.php';
    $conn = OpenCon();

    $resubmit = false;
    $error_message = "";

    if(isset($_POST['newMessage'])){
        $sender = $current_user_id;
        $username = $_POST['username'];
        $message = $_POST['message'];
        $id = 0;

        //check if the conversation has already been started
        $sql = "SELECT Messages.Conversation_ID 
        FROM (
            Messages 
            INNER JOIN Account ON Messages.Receiver_ID = Account.user_id
            )
            WHERE Account.username = \"$username\"
            LIMIT 1";

        $result = $conn->query($sql);

        if($result->num_rows > 0){
            $row = $result->fetch_assoc();
            $id = $row['Conversation_ID'];
        }else{
            //assign a new conversation id if it's a new conversation
            $sql = "SELECT MAX(Conversation_ID) AS id FROM Messages";
            $result = $conn->query($sql);
            
            if($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $id = $row["id"] + 1;
            }
        }

        //get the user id for the account we are sending it to
        $sql = "SELECT user_id FROM Account WHERE username=\"$username\"";
        $result = $conn->query($sql);

        if($result->num_rows > 0){
            $row = $result->fetch_assoc();
            $receiver = $row['user_id'];
        }

        $sql = "INSERT INTO Messages VALUES 
        ('$sender', '$receiver', '$message', '$id', CURRENT_TIMESTAMP)";
        $result = $conn->query($sql);

        if ($result === TRUE) {
            $error_message = "Message Sent";
            header('Location: '. $url . $path . '?page=mailbox&box=out&msg=' . $error_message);
        } else {
            echo("Error: " . $sql . "<br>" . $conn->error);
        }
    }

    CloseCon($conn);
 


?>