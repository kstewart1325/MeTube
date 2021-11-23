
<?php

function getMailbox($msg){

      if(!session_id()) session_start();

      $current_user_id = $_SESSION['user_id'];
      $isLoggedIn = $_SESSION['isLoggedIn'];
      //test variable below
      //$current_user_id = 2;

      $html = "";

      if(!$isLoggedIn){
          $html .= "You are not logged in. Please log-in to view this page.";
      }else{

          $path = "MeTube/src/";
          $url = "http://localhost:8070/";

          include_once 'db_connection.php';
          $conn = OpenCon();

          if(isset($_GET['box'])){
            $box = $_GET['box'];

            // checks if outbox was requested
            if($box === 'out'){

                $html = <<< PAGE
                <span id="Message pick">
                  <h3>Welcome to your Outbox</h3> 
                  <h4> 
                    <a href="index.php?page=mailbox">Inbox</a>   //    <a href="index.php?page=mailbox&box=new">New Message</a>
                  </h4>
                </span>
                <span id="Inbox Directions">
                  <i>Click on a message to view the conversation.</i><br>
                  <br><b>!! Alert: $msg</b><br>
                </span>
                PAGE;

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
                    $html .= "<br><table><tr><th>To</th><th>Message</th><th>Time</th></tr>";
                
                    // output data of each row
                    while($row = $result->fetch_assoc()) {
                      $receiver = $row['Receiver_ID'];
                      $sender = $row['username'];
                      $message = $row['Message'];
                      $time = $row['Timestamp'];
                      $id = $row['Conversation_ID'];

                      $html .= "<tr><td>$sender</td><td><a href=\"/MeTube/src/index.php?page=mailbox&convo=$id\">$message</a></td><td>$time</td></tr>";
                    }

                    $html .= "</table>";
                }else{
                    $html .= "You have sent no messages.";
                }

            // new message
            }else if($box === 'new'){
                $html .= <<< PAGE
                <p><a href="index.php?page=mailbox">Return to Inbox</a></p>
                <form method="post" name="new_message" id="new_message" action="mailbox_update.php">
                    <fieldset>
                    <p>$msg</p>
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

          //shows whole conversation's 
          }else if(isset($_GET['convo'])){
              $id = $_GET['convo'];

              $sql = "SELECT Account.username, Account.user_id, Messages.Receiver_ID, Messages.Message, Messages.Timestamp
              FROM (
                Messages 
                INNER JOIN Account ON Messages.Sender_ID = Account.user_id
              ) 
              WHERE Conversation_ID =\"$id\" 
              ORDER BY Messages.Timestamp ASC";

              $result = $conn->query($sql);

              if ($result->num_rows > 0) {
                  $html .= "<p><a href=\"index.php?page=mailbox\">Return to Inbox</a></p>
                          <table><tr><th>From</th><th>Message</th><th>Time</th></tr>";
                  $receiver = 0;
                  // output data of each row
                  while($row = $result->fetch_assoc()) {
                    
                    $sender = $row['username'];
                    $sender_id = $row['user_id'];
                    $message = $row['Message'];
                    $time = $row['Timestamp'];

                    // get the other user
                    if($sender_id !== $current_user_id){
                       $receiver = $sender;
                    }

                    $html .= "<tr><td>$sender</td><td>$message<td>$time</td></tr>";
                  }
                  
                  $html .= "</table><br>";
                  if($receiver !== 0){
                      $html .= "<a href=\"/MeTube/src/index.php?page=mailbox&reply=$receiver\">Reply</a>";
                  }
              }else {
                $html .= "You have no messages.";
              }

          //replying to messages 
          }else if(isset($_GET['reply'])){
            $username = $_GET['reply'];

            $html .= "<p><a href=\"index.php?page=mailbox\">Return to Inbox</a></p>";

            $html .= <<< PAGE
            <form method="post" name="new_message" id="new_message" action="mailbox_update.php">
                <fieldset>
                <p>
                  To: $username
                </p>
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
          
          // by default mailbox goes to the inbox
          }else{ 

              $html = <<< PAGE
              <span id="Message pick">
                <h3>Welcome to your Inbox</h3> 
                <h4> 
                  <a href="index.php?page=mailbox&box=out">Outbox</a>   //    <a href="index.php?page=mailbox&box=new">New Message</a>
                </h4>
              </span>
              <span id="Inbox Directions">
                <i>Click on a message to view the conversation.</i><br>
              </span>
              PAGE;

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
                  $html .= "<br><table><tr><th>Reply</th><th>To</th><th>Message</th><th>Time</th></tr>";
              
                  // output data of each row
                  while($row = $result->fetch_assoc()) {
                    $receiver = $row['Receiver_ID'];
                    $sender = $row['username'];
                    $message = $row['Message'];
                    $time = $row['Timestamp'];
                    $id = $row['Conversation_ID'];

                    $html .= "<tr><td><a href=\"/MeTube/src/index.php?page=mailbox&reply=$sender\">Reply</a></td><td>$sender</td><td><a href=\"index.php?page=mailbox&convo=$id\">$message</a></td><td>$time</td></tr>";
                  }

                  $html .= "</table>";
              }else {
                  $html .= "You have no messages.";
              }
          } 

          // $html .= <<< PAGE
          // </body>
          // </html>
          // PAGE;

          CloseCon($conn);
      }

      return $html;
  }

?>