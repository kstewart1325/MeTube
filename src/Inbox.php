








<?php

$path = "MeTube/src/";
$url = "http://localhost:8070/";

include 'db_connection.php';
$conn = OpenCon();

$resubmit = false;
$error_message = "";

$subpage = false;

$html = <<< PAGE
<!DOCTYPE html>
  <html>
  <head>
    <title>Messages</title>
    <style>
      table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
      }
    </style>
  </head>
  <body>
PAGE;


//$session_user = $_SESSION['user_id'];
$session_user = 2;



if(isset($_GET['box'])){
  $subpage = true;
  $box = $_GET['box'];

  //inbox 
  if($box === 'in'){
    $html .= "inbox";
    $sql = "SELECT Account.username, Messages.Receiver_ID, Messages.Message
    FROM (
      Messages 
      INNER JOIN Account ON Messages.Sender_ID = Account.user_id
    ) 
    WHERE Receiver_ID =\"$session_user\" 
    ORDER BY Messages.Timestamp DESC 
    LIMIT 0 , 30";
    $result = $conn->query($sql);
    

    if ($result->num_rows > 0) {
      $html .= "<table><tr><th>From</th><th>Message</th></tr>";
  
    // output data of each row
    while($row = $result->fetch_assoc()) {
      $receiver = $row['Receiver_ID'];
      $sender = $row['username'];
      $message = $row['Message'];

      $html .= "<tr><td>$sender</td><td>$message</td></tr>";
    }
    $html .= "</table>";
    } 
    else {
      $html .= "0 results";
    } 
  }
  //outbox
  else if($box === 'out'){
    $html .= "outbox";
    $sql = "SELECT Account.username, Messages.Receiver_ID, Messages.Message
    FROM (
      Messages 
      INNER JOIN Account ON Messages.Receiver_ID = Account.user_id
    ) 
    WHERE Sender_ID =\"$session_user\" 
    ORDER BY Messages.Timestamp DESC 
    LIMIT 0 , 30";
    $result = $conn->query($sql);
    

    if ($result->num_rows > 0) {
      $html .= "<table><tr><th>To</th><th>Message</th></tr>";
  
    // output data of each row
    while($row = $result->fetch_assoc()) {
      $receiver = $row['Receiver_ID'];
      $sender = $row['username'];
      $message = $row['Message'];

      $html .= "<tr><td>$sender</td><td>$message</td></tr>";
    }
    $html .= "</table>";
    } 
    else {
      $html .= "0 results";
    }
  }
  //new message
  else if($box === 'new'){
    $html .= "new";
    $html .= <<< PAGE
    <form method="post" name="new_message" id="new_message" >
        <fieldset>
        <p>
            <label for="username">To: </label>
            <input type="text" id="username" name="username" />
        </p>
        <p>
            <label for="message">Message: </label> <br>
            <TEXTAREA name="message" rows="10" cols="80"></TEXTAREA>
        </p>
        <p>
            <input type="submit" name="newMessage" value="New Message" />
            <input type="hidden" id="convo_id" name="convo_id" value="$id"/>
        </p>
        </fieldset>
    </form>
PAGE;
  }
  $html .= "<a href=\"/inbox.php\">Return to mailbox</a> ";
}
else{ 
  $html = <<< PAGE
  <span id="Message pick">
  <h3><a href="/inbox.php?box=in">Inbox</a>   
  <br><a href="/inbox.php?box=out">Outbox</a> 
  <br><a href="/inbox.php?box=new">New Message</a>
  </h3>
  </span>
  PAGE;
} 

$html .= <<< PAGE
</body>
</html>
PAGE;

echo $html;
CloseCon($conn);

?>