
<?php

if(!session_id()) session_start();

//$current_user_id = $_SESSION['user_id'];
$isLoggedIn = $_SESSION['isLoggedIn'];

//test variable below
$current_user_id = 2;

$path = "MeTube/src/";
$url = "http://localhost:8070/";

include 'db_connection.php';
$conn = OpenCon();

$resubmit = false;
$error_message = "";


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
  }
  //assign a new conversation id if it's a new conversation
  else{
    $sql = "SELECT MAX(Conversation_ID) AS id FROM Messages";
    $result = $conn->query($sql);
    
    if($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      $id = $row["id"] + 1;
      echo "New Conversation ID: $id";
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
      header('Location: '. $url . $path . 'inbox.php');
      $html .= "Message Sent";
  } else {
      echo("Error: " . $sql . "<br>" . $conn->error);
  }
}


if(isset($_GET['box'])){
  $box = $_GET['box'];

  $html .= "<a href=\"/inbox.php\">Return to mailbox</a> <p></p>";

  //inbox 
  if($box === 'in'){
    $sql = "SELECT Account.username, Messages.Receiver_ID, Messages.Message, Messages.Timestamp, Messages.Conversation_ID
    FROM (
      Messages 
      INNER JOIN Account ON Messages.Sender_ID = Account.user_id
    ) 
    WHERE Receiver_ID =\"$current_user_id\" 
    ORDER BY Messages.Timestamp DESC 
    LIMIT 0 , 30";
    $result = $conn->query($sql);
    

    if ($result->num_rows > 0) {
      $html .= "<table><tr><th>Reply</th><th>To</th><th>Message</th><th>Time</th></tr>";
  
    // output data of each row
    while($row = $result->fetch_assoc()) {
      $receiver = $row['Receiver_ID'];
      $sender = $row['username'];
      $message = $row['Message'];
      $time = $row['Timestamp'];
      $id = $row['Conversation_ID'];

      $html .= "<tr><td><a href=\"inbox.php?reply=$sender\">Reply</a></td><td>$sender</td><td><a href=\"inbox.php?convo=$id\">$message</a></td><td>$time</td></tr>";
    }
    $html .= "</table>";
    } 
    else {
      $html .= "0 results";
    }

  }

  //outbox
  else if($box === 'out'){
    $sql = "SELECT Account.username, Messages.Receiver_ID, Messages.Message, Messages.Timestamp, Messages.Conversation_ID
    FROM (
      Messages 
      INNER JOIN Account ON Messages.Receiver_ID = Account.user_id
    ) 
    WHERE Sender_ID =\"$current_user_id\" 
    ORDER BY Messages.Timestamp DESC 
    LIMIT 0 , 30";
    $result = $conn->query($sql);
    

    if ($result->num_rows > 0) {
      $html .= "<table><tr><th>To</th><th>Message</th><th>Time</th></tr>";
  
    // output data of each row
    while($row = $result->fetch_assoc()) {
      $receiver = $row['Receiver_ID'];
      $sender = $row['username'];
      $message = $row['Message'];
      $time = $row['Timestamp'];
      $id = $row['Conversation_ID'];

      $html .= "<tr><td>$sender</td><td><a href=\"inbox.php?convo=$id\">$message</a></td><td>$time</td></tr>";
    }
    $html .= "</table>";
    } 
    else {
      $html .= "0 results";
    }
  }

  //new message
  else if($box === 'new'){
    $html .= <<< PAGE
    <form method="post" name="new_message" id="new_message" action="Inbox.php">
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
            <input type="submit" name="newMessage" value="Send Message" />
        </p>
        </fieldset>
    </form>
    PAGE;
  }
}

//shows whole conversation's based on outbox
else if(isset($_GET['convo'])){
  $id = $_GET['convo'];

  $sql = "SELECT Account.username, Messages.Receiver_ID, Messages.Message, Messages.Timestamp
  FROM (
    Messages 
    INNER JOIN Account ON Messages.Sender_ID = Account.user_id
  ) 
  WHERE Conversation_ID =\"$id\" 
  ORDER BY Messages.Timestamp ASC";

  $result = $conn->query($sql);

  $html .= "<a href=\"/inbox.php\">Return to mailbox</a> <p></p>";

    

  if ($result->num_rows > 0) {
    $html .= "<table><tr><th>From</th><th>Message</th><th>Time</th></tr>";

  // output data of each row
  while($row = $result->fetch_assoc()) {
    $receiver = $row['Receiver_ID'];
    $sender = $row['username'];
    $message = $row['Message'];
    $time = $row['Timestamp'];

    $html .= "<tr><td>$sender</td><td>$message<td>$time</td></tr>";
  }
    $html .= "</table>";
  }  
  else {
    $html .= "0 results";
  }
}

//replying to messages in the inbox
else if(isset($_GET['reply'])){
  $username = $_GET['reply'];

  $html .= "<a href=\"/inbox.php\">Return to mailbox</a> <p></p>";

  $html .= <<< PAGE
  <form method="post" name="new_message" id="new_message" action="Inbox.php">
      <fieldset>
      <p>
          <label for="message">Message: </label> <br>
          <TEXTAREA name="message" rows="10" cols="80"></TEXTAREA>
      </p>
      <p>
          <input type="hidden" name="username" value="$username" />

          <input type="submit" name="newMessage" value="Send Message" />
      </p>
      </fieldset>
  </form>
  PAGE;
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