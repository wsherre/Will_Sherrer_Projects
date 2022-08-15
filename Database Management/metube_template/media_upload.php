<?php
session_start();
?>
<?php
if(isset($_POST['submit'])){ header('Location: browse.php');}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="css/default.css" />
<script type="text/javascript" src="js/jquery-latest.pack.js"></script>
<title>Media Upload</title>
</head>

<body>
<div id="header"><h1>Upload File</h1></div>
<div id='navbar'>
  <button class="nav_button" onclick="back('browse.php');">Go to Home Page</button>
</div>

<div id="inline">
      <form method="post" action="media_upload_process.php" enctype="multipart/form-data" >
      
        <p style="margin:0; padding:0; color: white;">
        <input type="hidden" name="MAX_FILE_SIZE" value="10485760" />
        Add a Media: <label style="color: white"><em> (Each file limit 10M)</em></label><br/>
        <input  name="file" type="file" size="10000000" />
        
        <input  class="button" value="Upload" name="submit" type="submit" />
        
        </p>
      
                      
      </form>
</div>
</body>
</html>
