<?php
@ob_start();
session_start();
include_once "function.php";
include_once "mysqlClass.inc.php";
$database = new dbh();
?>
<?php
if(isset($_POST['back'])){
	header('Location: browse.php');
}
?>
<?php

/******************************************************
*
* upload document from user
*
*******************************************************/

$username=$_SESSION['username'];


//Create Directory if doesn't exist
if(!file_exists('uploads/'))
	mkdir('uploads/', 0744);
$dirfile = 'uploads/'.$username.'/';
if(!file_exists($dirfile))
	mkdir($dirfile, 0744);


	if($_FILES["file"]["error"] > 0 )
	{ $result=$_FILES["file"]["error"];} //error from 1-4
	else
	{
	  $upfile = $dirfile.urlencode($_FILES["file"]["name"]);
	  $type = explode('/', $_FILES['file']['type'])[0];
	  $format = explode('/', $_FILES['file']['type'])[1];
	  if($type == 'video' && $format != 'mp4' && $format != 'webm'){
			$result='8';
	  }
	  else if($type == 'image' && $format != '.apng' && $format != 'gif' && $format != 'ico' && $format != 'cur' && $format != 'jpg' && $format != 'jpeg' && $format != 'jfif' && $format != 'pjpeg' && $format != 'pjp' && $format != 'png' && $format != 'svg'){
			$result='9';
	  }
	  else if(file_exists($upfile))
	  {
	  		$result="5"; //The file has been uploaded.
	  }
	  else{
			if(is_uploaded_file($_FILES["file"]["tmp_name"]))
			{
				if(!move_uploaded_file($_FILES["file"]["tmp_name"],$upfile))
				{
					$result="6"; //Failed to move file from temporary directory
				}
				else /*Successfully upload file*/
				{
					//insert into media table
					//insert into view
					$insert = "insert into media(filename, filepath, type) values(?, ?, ?)";
					$views = 'insert into view(mediaid) select mediaid from media where filename = ? and filepath = ? and type = ?';
					$vals = [urlencode($_FILES["file"]["name"]), $dirfile, $_FILES["file"]["type"]];
					$database->insert($insert, $vals);
					$database->insert($views, $vals);
					$result="0";
					
					//insert into upload table
					$max_mediaid = $database->query('SELECT MAX(mediaid) FROM media');
					$insertUpload="insert into upload(username, mediaid) values(?,?)";
					$vals = [$username, $max_mediaid[0]['MAX(mediaid)']];
					$database->insert($insertUpload, $vals);

					//insert into title
					$database->insert("INSERT INTO title VALUES (?, 'No description', ?)", [urlencode($_FILES["file"]["name"]), $max_mediaid[0]['MAX(mediaid)']]);
				}
			}
			else  
			{
					$result="7"; //upload file failed
			}
		}
	}
	
	//You can process the error code of the $result here.
?>

<meta http-equiv="refresh" content="0;url=browse.php?result=<?php echo $result;?>">
