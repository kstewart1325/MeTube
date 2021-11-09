<?php

include 'db_connection.php';
$conn = OpenCon();
echo "Connected Successfully";

if ($conn->connect_error) {
    die("Connection failed: " 
        . $conn->connect_error);
} else {
    echo "Connection established<br>";
}

$firstname = "";
$lastname = "";
$username = "";
$email = "";
$birthday = "";
$password = "";

CloseCon($conn);

//checks if username and/or password already exists
// $sqlquery = ""

// if ($conn->query($sql) === TRUE) {
//     $echo "record inserted successfully";
// } else {
//     $echo "Error: " . $sql . "<br>" . $conn->error;
// }

//adds info to "user" database if not already available
// $sqlquery = "INSERT INTO table VALUES 
// ('John', 'Doe', 'john@example.com')"

// if ($conn->query($sql) === TRUE) {
// $echo "record inserted successfully";
// } else {
// $echo "Error: " . $sql . "<br>" . $conn->error;
// }

// $pageContents = <<< EOPAGE
// <!DOCTYPE html>
// <html>
// <<head>
// <title>Sign-up</title>
// </head>

// <body>
//     <form action="signup.php" method="post">
//       <fieldset>
//         <legend>MeTube Signup</legend>
//         <p>
//           <label for="first_name">First Name: </label>
//           <input type="text" id="first_name" name="first_name" /><br />
//         </p>
//         <p>
//           <label for="last_name">Last Name: </label>
//           <input type="text" id="last_name" name="last_name" /><br />
//         </p>
//         <p>
//           <label for="username">Username: </label>
//           <input type="text" id="username" name="username" /><br />
//         </p>
//         <p>
//           <label for="email">Email: </label>
//           <input type="text" id="email" name="email" /><br />
//         </p>
//         <p>
//           <label for="birthday">Date of Birth: </label>
//           <input type="date" id="birthday" name="birthday" /><br />
//         </p>
//         <p>
//           <label for="password">Password: </label>
//           <input type="text" id="name" name="name" /><br />
//         </p>
//         <!-- user id -->
//         <!-- signup date -->
//         <p><input type="submit" value="Send" /> <input type="reset" /></p>
//       </fieldset>
//     </form>
//   </body>
// EOPAGE;

// echo $pageContents;

 ?>