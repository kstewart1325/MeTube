<!-- 
    Contacts page 
    Allow users to organize their contacts into a contact list, 
    to (1) add a user, and to (2) remove a user from their contact lists. 
    BONUS: Allow users to organize their contacts into different categories.
-->

<?php

  function getContactsPage($msg){

        $path = "MeTube/src/";
        $url = "http://localhost:8070/";

        include_once 'db_connection.php';
        $conn = OpenCon();
        $html = "";
        $error_message = "";

        if(!session_id()) session_start();
        //$current_user_id = 2;
        $current_user_id = $_SESSION['user_id'];
        $isLoggedIn = $_SESSION['isLoggedIn'];
        
        if(!$isLoggedIn){
            $html .= "You are not logged in. Please log-in to view this page.";
        }else{
            $html = <<< PAGE
                <p>$msg</p>
                <span id="contact_form">
                    <form action="contacts_update.php" method="post" name="contact_form" id="contact_form">
                    <fieldset>
                        <legend>Add or Remove Contact</legend>
                        <p>
                        <label for="username">Desired contact's username to add/remove: </label>
                        <input type="text" id="username" name="username" /><br />
                        </p>
                        <p>
            PAGE;
                        
            $html .= "<input type=\"submit\" name=\"addContact\" value=\"Add\" /> ";
            $html .= "<input type=\"submit\" name=\"removeContact\" value=\"Remove\" /> ";

            $html .= <<< PAGE
                        </p>
                    </fieldset>
                    </form>
                </span>
            PAGE;

            // queries existing contacts for the current user
            $sql = "SELECT contact_id FROM Contacts WHERE user_id=\"$current_user_id\"";
            $result = $conn->query($sql);
            $html .= "<h3><u>Contacts</u></h3>";
            
            // print out into a table v
            if ($result->num_rows > 0) {
                $html .= "<table style=\"width:100%\"><tr><th>Username</th><th>Name</th></tr>";
                
                // output data of each row
                while($row = $result->fetch_assoc()) {
                    $c_id = $row['contact_id'];
                    
                    $sql = "SELECT `username`, `first_name`, `last_name` FROM Account WHERE user_id=\"$c_id\"";
                    $result2 = $conn->query($sql);
                    $row2 = $result2->fetch_assoc();

                    $html .= "<tr><td>".$row2["username"]."</td> <td>".$row2["first_name"]." ".$row2["last_name"]."</td></tr>";
                }
                $html .= "</table>";       
            } else {
                $html .= "You have no Contacts.";
            }

            CloseCon($conn);
        }

        return $html;
  }
?>


