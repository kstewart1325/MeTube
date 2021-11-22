<?php

//include all pages
include 'home.php';
include 'channel.php';
include 'media.php';
include 'search.php';

$path = "MeTube/src/";
$url = "http://localhost:8070/";

if(!session_id()) session_start();

if(!isset($_SESSION['isLoggedIn'])){
    $_SESSION['isLoggedIn'] = false;
    $_SESSION['user_id'] = -1;
}

$isLoggedIn = $_SESSION['isLoggedIn'];
$currentPage = "home";
$currentMedia = "";
$currentMsg = "";

if($_SERVER['REQUEST_METHOD']=="GET"){
    if(isset($_GET['page'])){
        $currentPage = $_GET['page'];
    }

    if(isset($_GET['id'])){
        $currentMedia = $_GET['id'];
    }

    if(isset($_GET['msg'])){
        $currentMsg = $_GET['msg'];
    }
}

$html = <<< PAGE
<!DOCTYPE html>
<head>
    <title>MeTube</title>
    <link rel="stylesheet" href="index.css">
</head>

<body>
    <div class="header">
        <div class="header-left">
            <a href="" class="logo">MeTube</a>
        </div>
        <div class="header-middle">
            <form class="form-inline" method="GET" action="search.php">
					<input type="text" class="form-control" placeholder="Search here..." name="content" required="required"/>
			</form>
        </div>
        <div class="header-right">
        <a class="active" class="link" style="margin-right: 2px" href="index.php?page=home">Home</a>
PAGE;

if(!$isLoggedIn){
    $html .= "<a class=\"link\" href=\"login.php\">Log-in</a>";

} else {
    $html .= "<a class=\"link\" href=\"index.php?page=channel\">Account</a>";
    $html .= "<a class=\"link\" href=\"upload.php\">Upload</a>";
    $html .= "<a class=\"link\" href=\"\">Settings</a>";
    $html .= "<a class=\"link\" href=\"index.php?page=logout\">Log-out</a>";
}

$html .= <<< PAGE
        </div>
    </div>
    <div style="margin-bottom: 15px; margin-top: 15px;" class="page">
PAGE;


if($currentPage === "home"){
    $html .= getHomePage();
} else if($currentPage === "channel"){
    $html .= getChannelPage();
} else if($currentPage === "media"){
    $html .= getMediaPage($currentMedia);
} else if($currentPage === "logout") {
    unset($_SESSION['isLoggedIn']);
    unset($_SESSION['id']);
    header('Location: '. $url . $path . 'index.php');
}else if($currentPage === "search"){
    $html .= $search_html;
}

$html .= <<< PAGE
    </div>
</body>
</html>
PAGE;

echo $html;


?>