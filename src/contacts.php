<!-- 
    Contacts page 
    Allow users to organize their contacts into a contact list, 
    to (1) add a user, and to (2) remove a user from their contact lists. 
    BONUS: Allow users to organize their contacts into different categories.
-->

<?php

  $path = "MeTube/src/";
  $url = "http://localhost:8070/";

  include 'db_connection.php';
  $conn = OpenCon();

  $session_user = $_SESSION['user_id'];
  $session_user = 2;
  $resubmit = false;
  $error_message = "";
  
  if($_SERVER['REQUEST_METHOD']=="POST"){
      //stores data from form
      $username = $_POST['username'];
      //$func = $_POST['func'];

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
              $error_message = "<br>The username \"$username\" is invalid. Please try again. <br>";
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
                  $error_message = "<br>$username removed from Contact List<br>";
              } else {
                  $echo("Error: " . $sql . "<br>" . $conn->error);
              }

          }else{
              $error_message = "<br>Contact $username does not exist.<br>";
              $resubmit = true;
          }          
            
      }else if($resubmit === false){
          // add to contact list

          //queries the entered contact and current user
          $sql2 = "SELECT contact_id FROM Contacts WHERE contact_id=$contact AND user_id=$session_user LIMIT 0 , 30";
          $result2 = $conn->query($sql2);
          
          if($result2->num_rows > 0){
              $error_message = "<br>Contact already added. <br>";
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
                  //header('Location: '. $url . $path . 'contacts.php');
                  $error_message = "<br>$username added to Contact List<br>";
              } else {
                  $echo("Error: " . $sql . "<br>" . $conn->error);
              }            
          }      
      }
  }

  $html = <<< PAGE
  <!DOCTYPE html>
    <html>
    <head>
      <title>Contacts</title>
    </head>
    <body>
      <span id="contact_form">
        <form method="post" name="contact_form" id="contact_form">
          <fieldset>
            <legend>Add or Remove Contact</legend>
            <p>
              <label for="username">Desired contact's username to add/remove: </label>
              <input type="text" id="username" name="username" /><br />
            </p>
            <p>
  PAGE;
            
  //$html .= "<input id=\"add\" name=\"add\" type=\"submit\" value=\"Add\" href=\"contacts.php?func=add\" /> ";
  //$html .= "<input id=\"remove\" name=\"remove\" type=\"submit\" value=\"Remove\" href=\"contacts.php?func=remove&username\" />";

  $html .= "<input type=\"submit\" name=\"addContact\" value=\"Add\" /> ";
  $html .= "<input type=\"submit\" name=\"removeContact\" value=\"Remove\" /> ";

  $html .= <<< PAGE
            </p>
          </fieldset>
        </form>
      </span>
    </body>
  </html>
  PAGE;

  //displays contact page with appropriate error message
  $html .= $error_message;

  echo $html;

  // queries existing contacts for the current user
  $sql = "SELECT * FROM Contacts WHERE user_id=\"$session_user\" LIMIT 0 , 30";
  $result = $conn->query($sql);

  // print out into a table
  echo "<table>\n";

  while($line = mysqli_fetch_array($result, MYSQLI_ASSOC)){
      echo "\t<tr>\n";
      foreach($line as $col_value){
          echo "\t\t<td>$col_value</td>\n";
      }
      echo "\t<tr>\n";
  }
  echo "</table>\n";

  CloseCon($conn);
?>
