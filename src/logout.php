<?php
    $path = "MeTube/src/";
    $url = "http://localhost:8070/";
    
    session_destroy();
    header('Location: '. $url . $path . 'index.php');
?>