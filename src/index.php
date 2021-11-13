<?php

//include all pages
include 'home.php';
include 'channel.php';

$path = "MeTube/src/";
$url = "http://localhost:8070/";

if(!session_id()) session_start();

if(!isset($_SESSION['isLoggedIn'])){
    $_SESSION['isLoggedIn'] = false;
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
        <a href="" class="logo">MeTube</a>
        <div class="header-right">
        <a class="active" href="index.php?page=home">Home</a>
PAGE;

if(!$isLoggedIn){
    $html .= "<a href=\"login.php\">Log-in</a>";

} else {
    $html .= "<a href=\"index.php?page=channel\">Account</a>";
    $html .= "<a href=\"logout.php\">Log-out</a>";
}

$html .= <<< PAGE
        </div>
    </div>
PAGE;

if($currentPage === "home"){
    $html .= getHomePage();
} else if($currentPage === "channel"){
    $html .= getChannelPage();
}

$html .= <<< PAGE
</body>
</html>
PAGE;

$css = <<< STYLE
<style>
    /* Style the header with a grey background and some padding */
    .header {
    overflow: hidden;
    background-color: #f1f1f1;
    padding: 20px 10px;
    }

    /* Style the header links */
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

    /* Style the logo link (notice that we set the same value of line-height and font-size to prevent the header to increase when the font gets bigger */
    .header a.logo {
    font-size: 25px;
    font-weight: bold;
    }

    /* Change the background color on mouse-over */
    .header a:hover {
    background-color: #ddd;
    color: black;
    }

    /* Style the active/current link*/
    .header a.active {
    background-color: dodgerblue;
    color: white;
    }

    /* Float the link section to the right */
    .header-right {
    float: right;
    }

    /* Add media queries for responsiveness - when the screen is 500px wide or less, stack the links on top of each other */
    @media screen and (max-width: 500px) {
        .header a {
            float: none;
            display: block;
            text-align: left;
        }
        .header-right {
            float: none;
        }
    }
</style>
STYLE;

echo $html;
echo $css;

?>