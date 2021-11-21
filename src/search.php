<?php
  $path = "MeTube/src/index.php";
  $url = "http://localhost:8070/";

  include 'db_connection.php';
  $conn = OpenCon();

  //$session_user = $_SESSION['user_id'];
  $session_user = 2;
  
  $error_message = "";

//   $search_html = "";

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

        // $myterms = array();
        // parse_str($terms, $myterms);
        $myterms = explode(" ", $terms);

        $found = false;
        $search_results .= "<ul>";

        foreach ($myterms as $val){
            $sql = "SELECT `media_id`, `media_title` FROM Mediafiles WHERE keywords LIKE '%$val%'";
            $result = $conn->query($sql);
            if($result->num_rows > 0){
                $found = true;
                // get each mediafile name and print 
                while($row = $result->fetch_assoc()) {
                    $mid = $row['media_id'];
                
                    // $sql2 = "SELECT media_title FROM Mediafiles WHERE media_id=\"$mid\"";
                    // $result2 = $conn->query($sql2);
                    // $row2 = $result2->fetch_assoc();
                    
                    $mname = $row['media_title'];
                    // make mname a link to the mediafile page
                    $search_results .= <<< PAGE
                        <li><a href=\"/MeTube/src/index.php?page=media&id=$mid\">$mname</a></li>
                    PAGE;
        
                }
            }
        }

        if(!$found){
            $search_results .= "<li>No search results.</li>";
        }

        $search_results .= "</ul><br>";
    } 
  }

//   $search_html .= <<< PAGE
//     </body>
//     </html>
//   PAGE;

//   echo $search_html;
  
  CloseCon($conn);

?>
