<!-- Where user uploads media file -->

<?php
// script to pull category options

?>

<!DOCTYPE html>
<html>
  <head>
    <title>MeTube File Upload</title>
  </head>
  <body>
    <span id="upload_form">
      <form enctype="multipart/form-data" action="uploadprocess.php" method="post">
        <fieldset>
          <legend>Upload File</legend>
          <input type="hidden" name="MAX_FILE_SIZE" value="4000000000" />
          <p>
            <label for="mediafile">File: </label>
            <input type="file" id="mediafile" name="mediafile" /><br />
          </p>
          <p>
            <label for="title">Title: </label>
            <input type="text" id="title" name="title" /><br />
          </p>
          <p>
            <!-- THESE EVENTUALLY NEED TO BE STORED IN A DB TABLE AND QUERIED TO POPULATE -->
            <label for="category">Category: </label>
            <select name="category">
              <option value="blank">     </option>
              <option value=”crafts”>Crafts</option>
              <option value=”fashion”>Fashion</option>
              <option value=”beauty”>Beauty</option>
              <option value=”gaming”>Gaming</option>              
              <option value=”learning”>Learning</option>            
              <option value=”music”>Music</option>
              <option value=”nature”>Nature</option>
              <option value=”news”>News</option>
              <option value=”sports”>Sports</option>
              <option value=”other”>Other</option>
            </select>
          </p>
          <p>
            <label for="description">Description: </label><br>
            500 character max.<br>
            <TEXTAREA name="description" rows="10" cols="80"></TEXTAREA>
          </p>
          <p>
            <label for="keywords">Search Keywords: </label><br>
            Separate keywords with a comma (",").<br>
            <TEXTAREA name="keywords" rows="2" cols="80"></TEXTAREA>
          </p>          
          <p><input type="submit" value="Upload" /> <input type="reset" value="Clear"/></p>
        </fieldset>
      </form>
    </span>
  </body>
</html>
