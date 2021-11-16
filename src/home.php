<?php

function getHomePage(){
    $html = <<< PAGE
    <div class="home">
       <div class="row">
            <h3>New Uploads</h3>
            <img class="media" src="../media/image-placeholder.png" alt="Image Placeholder">
            <img class="media" src="../media/image-placeholder.png" alt="Image Placeholder">
            <img class="media" src="../media/video-placeholder.png" alt="Video Placeholder">
       </div>
       <div class="row">
            <h3>Most Popular</h3>
            <img class="media" src="../media/video-placeholder.png" alt="Video Placeholder">
            <img class="media" src="../media/video-placeholder.png" alt="Video Placeholder">
            <img class="media" src="../media/image-placeholder.png" alt="Image Placeholder">
       </div>
    </div>
    PAGE;

    return $html;
}

?>