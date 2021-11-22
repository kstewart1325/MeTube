<?php

//include all pages
include 'home.php';
include 'channel.php';
include 'media.php';
include 'search.php';
include 'playlists.php';
include 'contacts.php';
include 'favorites.php';


$path = "MeTube/src/";
$url = "http://localhost:8070/";

if(!session_id()) session_start();

if(!isset($_SESSION['isLoggedIn'])){
    $_SESSION['isLoggedIn'] = false;
    $_SESSION['user_id'] = -1;
}

$isLoggedIn = $_SESSION['isLoggedIn'];
$currentPage = "home";
$id = "";
$keyword = "";

if($_SERVER['REQUEST_METHOD']=="GET"){
    if(isset($_GET['page'])){
        $currentPage = $_GET['page'];
    }

    if(isset($_GET['id'])){
        $id = $_GET['id'];
    }

    if(isset($_GET['content'])){
        $keyword = $_GET['content'];
        $currentPage = "search";
    }
}

if($_SERVER['REQUEST_METHOD']=="POST"){
    $keyword = $_POST['content'];
    $currentPage = "search";
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
        <form class="form-inline" method="GET" action="index.php">
             <input type="text" class="form-control" placeholder="Search by keyword..." name="content" required="required"/>
        </form>
        </div>
        <div class="header-right">
        <a class="active" class="link" style="margin-right: 2px" href="index.php?page=home">Home</a>
PAGE;

if(!$isLoggedIn){
    $html .= "<a class=\"link\" href=\"login.php\">Log-in</a>";

} else {
    $user_id = $_SESSION['user_id'];
    $html .= <<< PAGE
    <a class="link" href="index.php?page=channel&id=$user_id">Account</a>
    <a class="link" href="upload.php">Upload</a>
    <a class="link" href="profile_update.php">Settings</a>
    <a class="link" href="index.php?page=logout">Log-out</a>
    PAGE;
}

$html .= <<< PAGE
        </div>
    </div>
    <div style="margin-bottom: 15px; margin-top: 15px;" class="page">
PAGE;

if($currentPage === "home"){
    $html .= getHomePage();
} else if($currentPage === "channel"){
    $html .= getChannelPage($id);
} else if($currentPage === "media"){
    $html .= getMediaPage($id);
} else if($currentPage === "logout") {
    unset($_SESSION['isLoggedIn']);
    unset($_SESSION['id']);
    header('Location: '. $url . $path . 'index.php');
}else if($currentPage === "search"){
    $html .= search($keyword);
}else if($currentPage === "playlists"){
    // $html .= "<i>$currentMsg</i><br>";
    // $html .= $play_html;
}else if($currentPage === "contacts"){
    // $html .= "<i>$currentMsg</i><br>";
    // $html .= $contacts_html;
}else if($currentPage === "favorites"){
    // $html .= "<i>$currentMsg</i><br>";
    // $html .= $fav_html;
}


$html .= <<< PAGE
    </div>
</body>
</html>
PAGE;

echo $html;


?>