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
    $html .= "<a class=\"link\" href=\"index.php?page=channel\">Account</a>";
    $html .= "<a class=\"link\" href=\"logout.php\">Log-out</a>";
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
    .header {
    overflow: hidden;
    background-color: #f1f1f1;
    padding: 20px 10px;
    }

    .row {
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
        margin-left: 4px;
        margin-right: 4px;
    }

    .header a:hover {
    background-color: #ddd;
    color: black;
    }

    .header a.active {
    background-color: dodgerblue;
    color: white;
    }

    .header-right {
    float: right;
    }

    .header-left {
    width: 27.5%;
    float: left;
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
</style>
STYLE;

echo $html;
echo $css;

?>