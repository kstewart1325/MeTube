<?php
  $path = "MeTube/src/index.php";
  $url = "http://localhost:8070/";

  include_once 'db_connection.php';
  $conn = OpenCon();
  
  $error_message = "";

  $search_html = "";

//   $search_html = <<< PAGE
//       <!DOCTYPE html>
//         <html>
//         <head>
//           <title>Playlists</title>
//           <style>
//             table, th, td {
//               border: 1px solid black;
//               border-collapse: collapse;
//             }
//           </style>
//         </head>
//         <body>
//     PAGE;
  
  if($_SERVER['REQUEST_METHOD']=="GET"){

    if(isset($_GET['content'])){
        $terms = $_GET['content'];

        $myterms = explode(" ", $terms);

        $found = false;
        $search_html .= "<h3>Your Search Results...</h3><ul>";

        foreach ($myterms as $val){
            $sql = "SELECT `media_id`, `media_title` FROM Mediafiles WHERE keywords LIKE '%$val%'";
            $result = $conn->query($sql);
            if($result->num_rows > 0){
                $found = true;

                // get each mediafile name and print 
                while($row = $result->fetch_assoc()) {
                    $mid = $row['media_id'];
                    
                    $mname = $row['media_title'];
                    $search_html .= <<< PAGE
                        <li><a href=\"/MeTube/src/index.php?page=media&id=$mid\">$mname</a></li>
                    PAGE;
        
                }
            }
        }

        if(!$found){
            $search_html .= "<li>No search results.</li>";
        }

        $search_html .= "</ul><br>";

    } 
  }

 

//   $search_html .= <<< PAGE
//     </body>
//     </html>
//   PAGE;

//   echo $search_html;
  
  CloseCon($conn);

?>


