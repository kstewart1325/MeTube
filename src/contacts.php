<!-- 
    Contacts page 
    Allow users to organize their contacts into a contact list, 
    to (1) add a user, and to (2) remove a user from their contact lists. 
    BONUS: Allow users to organize their contacts into different categories.
-->

<?php

  $path = "MeTube/src/";
  $url = "http://localhost:8070/";

  include_once 'db_connection.php';

  $conn = OpenCon();

  $session_user = $_SESSION['user_id'];
  //$session_user = 2;

  $resubmit = false;
  $error_message = "";

  $contacts_html = "";
  
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
          $sql2 = "SELECT cid FROM Contacts WHERE contact_id=\"$contact\" AND user_id=\"$session_user\"";
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
                  $contacts_html .= "Error: $sql <br> $conn->error";
              }

          }else{
              $error_message = "<br>Contact <i>$username</i> does not exist.<br>";
              $resubmit = true;
          }          
            
      }else if($resubmit === false){
          // add to contact list

          //queries the entered contact and current user
          $sql2 = "SELECT contact_id FROM Contacts WHERE contact_id=$contact AND user_id=$session_user LIMIT 0 , 30";
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
              $sql = "INSERT INTO Contacts VALUES ('$id', '$session_user', '$contact')";
              $result3 = $conn->query($sql);
      
              if ($result3 === TRUE) {
                  $error_message = "<br><i>$username</i> added to Contact List<br>";
                  header('Location: '. $url . $path . 'index.php?page=contacts&msg=' . $error_message);
              } else {
                 $contacts_html .= "Error: $sql <br> $conn->error";
              }            
          }      
      }
  }

  if($resubmit === true){
    header('Location: '. $url . $path . 'index.php?page=contacts&msg=' . $error_message);
  }

  $contacts_html = <<< PAGE
      <span id="contact_form">
        <form action="contacts.php" method="post" name="contact_form" id="contact_form">
          <fieldset>
            <legend>Add or Remove Contact</legend>
            <p>
              <label for="username">Desired contact's username to add/remove: </label>
              <input type="text" id="username" name="username" /><br />
            </p>
            <p>
  PAGE;
            
  $contacts_html .= "<input type=\"submit\" name=\"addContact\" value=\"Add\" /> ";
  $contacts_html .= "<input type=\"submit\" name=\"removeContact\" value=\"Remove\" /> ";

  $contacts_html .= <<< PAGE
            </p>
          </fieldset>
        </form>
      </span>
  PAGE;

  // queries existing contacts for the current user
  $sql = "SELECT contact_id FROM Contacts WHERE user_id=\"$session_user\"";
  $result = $conn->query($sql);
  $contacts_html .= "<h3><u>Contacts</u></h3>";
  
  // print out into a table v
  if ($result->num_rows > 0) {
       $contacts_html .= "<table style=\"width:100%\"><tr><th>Username</th><th>Name</th></tr>";
    
      // output data of each row
      while($row = $result->fetch_assoc()) {
          $c_id = $row['contact_id'];
        
          $sql = "SELECT `username`, `first_name`, `last_name` FROM Account WHERE user_id=\"$c_id\"";
          $result2 = $conn->query($sql);
          $row2 = $result2->fetch_assoc();

          $contacts_html .= "<tr><td>".$row2["username"]."</td> <td>".$row2["first_name"]." ".$row2["last_name"]."</td></tr>";
      }
      $contacts_html .= "</table>";       
  } else {
    $contacts_html .= "You have no Contacts.";
  }

  CloseCon($conn);
?>
