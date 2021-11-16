<?php

//include all pages
include 'db_connection.php';
include 'home.php';
include 'channel.php';

$path = "MeTube/src/";
$url = "http://localhost:8070/";

$conn = openCon();

if(!session_id()) session_start();

if(!isset($_SESSION['isLoggedIn'])){
    $_SESSION['isLoggedIn'] = false;
    $_SESSION['user_id'] = -1;
}

$isLoggedIn = $_SESSION['isLoggedIn'];
$currentPage = "home";

if($_SERVER['REQUEST_METHOD']=="GET"){
    if(isset($_GET['page'])){
        $currentPage = $_GET['page'];
    }
}

$html = <<< PAGE
<!DOCTYPE html>
<head>
    <title>MeTube</title>
</head>

<body>
    <div class="header">
        <div class="header-left">
            <a href="" class="logo">MeTube</a>
        </div>
        <div class="header-middle">
            <input type="text" placeholder="Search...">
        </div>
        <div class="header-right">
        <a class="active" class="link" href="index.php?page=home">Home</a>
PAGE;

if(!$isLoggedIn){
    $html .= "<a class=\"link\" href=\"login.php\">Log-in</a>";

} else {
    $html .= "<a class=\"link\" style=\"margin-right: 0px\" href=\"index.php?page=channel\">Account</a>";
    $html .= "<a class=\"link\" style=\"margin-left: 0px\" href=\"index.php?page=logout\">Log-out</a>";
}

$html .= <<< PAGE
        </div>
    </div>
PAGE;

if($currentPage === "home"){
    $html .= getHomePage();
} else if($currentPage === "channel"){
    //gets user's name
    $id = $_SESSION['user_id'];
    $sql = "SELECT `first_name` FROM Account WHERE `user_id`=\"$id\"";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $name = $row["first_name"];

    $html .= "<div class=\"welcome\" style=\"padding-left: 20px\">";
    $html .= "<h3>Hello $name!</h3>";
    $html .= "</div>";
    
    $html .= getChannelPage();
} else if($currentPage === "logout") {
    unset($_SESSION['isLoggedIn']);
    unset($_SESSION['id']);
    header('Location: '. $url . $path . 'index.php');
}

$html .= <<< PAGE
</body>
</html>
PAGE;

$css = <<< HEADER_STYLE
<style>
    .header {
        overflow: hidden;
        background-color: #f1f1f1;
        padding: 20px 10px;
    }

    .header input[type=text] {
        float: left;
        padding: 6px;
        border: none;
        margin-top: 8px;
        margin-right: 16px;
        font-size: 17px;
        width: 40%;
    }

    .header a {
        float: left;
        color: black;
        text-align: center;
        padding: 12px;
        text-decoration: none;
        font-size: 18px;
        line-height: 25px;
        border-radius: 4px;
    }

    .header a.logo {
        font-size: 25px;
        font-weight: bold;
        text-align: left;
    }

    .header a.link {
        margin-left: 8px;
        margin-right: 8px;
    }

    .header a:hover {
        background-color: #ddd;
        color: black;
    }

    .header a.active {
        background-color: dodgerblue;
        color: white;
    }

    .header a.active:hover {
        background-color: #85c3ff;
        color: white;
    }

    .header-right {
        float: right;
    }

    .header-left {
        float: left;
        width: 27.5%;
    }

    @media screen and (max-width: 500px) {
        .header a {
            float: none;
            display: block;
            text-align: left;
        }
        .header input[type=text] {
            float: none;
            display: block;
            text-align: left;
            width: 100%;
            margin: 0;
            padding: 14px;
            border: 1px solid #ccc;
          }
        .header-right {
            float: none;
        }
    }
HEADER_STYLE;

$css .= <<< HOME_STYLE
.row {
    overflow: hidden;
    padding-left: 20px;
}

.media {
    float: left;
    width: 300px;
    height: 200px;
    padding: 10px 10px;
}

</style>
HOME_STYLE;

echo $html;
echo $css;

closeCon($conn);

?>