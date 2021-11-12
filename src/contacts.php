<!-- 
    Contacts page 
    Allow users to organize their contacts into a contact list, 
    to (1) add a user, and to (2) remove a user from their contact lists. 
    BONUS: Allow users to organize their contacts into different categories.
-->

<!DOCTYPE html>
<html>
  <head>
    <title>Contacts</title>
  </head>
  <body>
    <span id="contact_form">
      <form action="contacts_update.php" method="post">
        <fieldset>
          <legend>Add or Remove Contact</legend>
          <p>
            <label for="username">Desired contact's username to add/remove: </label>
            <input type="text" id="username" name="username" /><br />
          </p>
          <p><input type="submit" name="addContact" value="Add" /> <input type="submit" name="removeContact" value="Remove" /> <input type="reset" /></p>
        </fieldset>
      </form>
    </span>

    <?php
      $path = "MeTube/src/";
      $url = "http://localhost:8070/";

      include 'db_connection.php';
      $conn = OpenCon();

      $resubmit = false;  

      $session_user=2;

      // KATE: NEED THE CURRENT SESSION'S USER'S ID # STORED IN A VAR CALLED "session_user"
      // or occurences of session_user replaced by w/e you're storing it. thx ; )

      // queries existing contacts for the current user
      $sql = "SELECT * FROM Contacts WHERE user_id=\"$session_user\" LIMIT 0 , 30";
      $result = $conn->query($sql);

      // print out into a table
      // THIS IS NOT THE FINISHED TABLE & IDEK IF THIS WORKS
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
  </body>
</html>
