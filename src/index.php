<?php

//include all pages
include 'home.php';
include 'channel.php';
include 'media.php';
include 'search.php';
include 'playlists.php';
include 'contacts.php';
include 'browse.php';
include 'mailbox.php';

$path = "MeTube/src/";
$url = "http://webapp.computing.clemson.edu/~cgstewa/";

if(!session_id()) session_start();

if(!isset($_SESSION['isLoggedIn'])){
    $_SESSION['isLoggedIn'] = false;
    $_SESSION['user_id'] = -1;
}

$isLoggedIn = $_SESSION['isLoggedIn'];
$currentPage = "home";
$list = "all";
$id = "";
$keyword = "";
$msg = "";

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

    if(isset($_GET['msg'])){
        $msg = $_GET['msg'];
    }

    if(isset($_GET['list'])){
        $list = $_GET['list'];
    }

    if(isset($_GET['cat'])){
        $cat = $_GET['cat'];
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
            <a href="index.php?page=home" class="logo">MeTube</a>
        </div>
        <div class="header-middle">
        <form class="form-inline" method="GET" action="index.php">
             <input type="text" class="form-control" placeholder="Search by keyword..." name="content" required="required"/>
        </form>
        </div>
        <div class="header-right">
        <a class="active" href="index.php?page=browse&cat=all">Browse</a>
PAGE;

if(!$isLoggedIn){
    $html .= "<a class=\"link\" href=\"login.php\">Log-in</a>";
} else {
    $user_id = $_SESSION['user_id'];
    $html .= <<< PAGE
    <a class="link" href="index.php?page=channel&id=$user_id">Channel</a>
    <a class="link" href="upload.php">Upload</a>
    <a class="link" href="index.php?page=playlists&list=all">Playlists</a>
    <a class="link" href="index.php?page=mailbox">Mail</a>
    <a class="link" href="index.php?page=contacts">Contacts</a>
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
    $html .= getChannelPage($id, $msg);
} else if($currentPage === "media"){
    $html .= getMediaPage($id, $msg);
} else if($currentPage === "logout") {
    unset($_SESSION['isLoggedIn']);
    unset($_SESSION['id']);
    header('Location: '. $url . $path . 'index.php');
}else if($currentPage === "search"){
    $html .= search($keyword);
}else if($currentPage === "playlists"){
    $html .= getPlaylists($list, $msg);
}else if($currentPage === "contacts"){
    $html .= getContactsPage($msg);
}else if($currentPage === "browse"){
    $html .= getBrowse($cat);
}else if($currentPage === "mailbox"){
    $html .= getMailbox($msg);
}


$html .= <<< PAGE
    </div>
</body>
</html>
PAGE;

echo $html;


?>