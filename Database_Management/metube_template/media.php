<?php
	@ob_start();
	session_start();
	include_once "function.php";
	include_once "mysqlClass.inc.php";
	$database = new dbh();
	$GLOBALS['d'] = $database;
	if(isset($_POST['logout'])){
		$_SESSION['login'] = false;
		$_SESSION['username'] = '';
		header('Location: index.php');
		exit();
	}
	if(isset($_POST['add_comment'])){
		if($_POST['comment'] == ""){
			$error = "Comment must not be empty!";
		}else{
			$comment = $_POST['comment'];
			$filepath = $_POST['add_comment'];
			$user = $_SESSION['username'];
			add_comment($user, $filepath, $comment);
		}
	}
	if(isset($_POST['confirm'])){
		$arr = explode('.', $_POST['confirm']);
		$filepath = $arr[0].'.'.$arr[1];
		$comment = $arr[2];
		$user = $arr[3];
		echo $user;
		delete_comment($filepath, $comment, $user);
	}
	if(isset($_POST['like'])){
		insert_like($_GET['id']);
		$GLOBALS['d']->insert('update view set views = views - 1 where mediaid = ?', [$_GET['id']]);
	}
	if(isset($_POST['unlike'])){
		delete_like($_GET['id']);
		$GLOBALS['d']->insert('update view set views = views - 1 where mediaid = ?', [$_GET['id']]);
	}
	$like = liked($_GET['id']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">	
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Media</title>
<link rel="stylesheet" type="text/css" href="css/default.css" />
<script type="text/javascript" src="js/jquery-latest.pack.js"></script>
<script src="Scripts/AC_ActiveX.js" type="text/javascript"></script>
<script src="Scripts/AC_RunActiveContent.js" type="text/javascript"></script>
</head>

<body>
<div id='header'>
    <?php
	if(isset($_GET['id'])) {
		$query = "select media.*, upload.username, upload.uploadtime from media join upload using(mediaid) where mediaid =". $_GET['id'];
		$result = $GLOBALS['d']->query($query);
		$result_row = $result[0];
		$GLOBALS['d']->insert('update view set views = views + 1 where mediaid = ?', [$_GET['id']]);
		updateMediaTime($_GET['id']);
		
		$user = $result_row['username'];
		$uploadtime = explode(' ', $result_row['uploadtime'])[0];
		$filename=$result_row['filename'];
		$filepath=$result_row['filepath'];
		$id = $_GET['id'];
		$type=$result_row['type'];
		if(substr($type,0,5)=="image") //view image
		{
			echo "<h3>Viewing Picture: ";
			echo $result_row['filename'] . "</h3>";
		}
		else{
			echo "<h3>Viewing Video: " .  $result_row['filename'] . "</h3>";
		}
		?>
</div>
<div id='navbar'>
	<ul class='ul'>
		<li><button class="nav_button" onclick="back('index.php');">Go To Main Page</button></li>
		<?php
			if(isset($_SESSION['login'])){
				if($_SESSION['login'] == true){
					echo "<li><button class=\"nav_button\" onclick=\"back('browse.php');\">Go To Homepage</button></li>";
					echo "<li><form action='' method='post'><button name='logout' class=\"nav_button\">Log Out</button></form></li>";
				}else{
					echo "<li><button class=\"nav_button\" onclick=\"back('login.php?id=".$_GET['id']."');\">Sign in</button></li>";
				 }
			}else{
				echo "<li><button class=\"nav_button\" onclick=\"back('login.php?id=".$_GET['id']."');\">Sign in</button></li>";
		 }?>
	</ul>
</div>
<div id='inline' style='padding-bottom: 200px; '>
        <?php 
		if(substr($type,0,5)=="image") //view image
		{
			echo "<img style='width: 500px; height: auto; margin-left: 41px; margin-top: 15px;' src='".$filepath.$filename."'/>";
		}
		else //view movie
		{	
		echo "<video style='width: 800px; height: auto; margin-left: 30px; margin-top: 15px;' controls>";
		echo "<source src=\"" . $filepath.$filename . "\" type=\"" . $type. "\"></source>";
		echo "</video>";
	            
        }
	if(isset($_POST['add_to_list'])){
		$files = get_playlist_files($_POST['play']);
		$arr = array();
		foreach($files as $f){
			array_push($arr, $f['mediaid']);
		}
		if ($_POST['play'] == ''){
			echo "<div id='passwd_result'>Must select a playlist</div>";
		}
		else if(in_array($_POST['add_to_list'], $arr)){
			echo "<div id='passwd_result' style='margin-top: 10px;'>File already exists in playlist:  " . $_POST['play'] . "</div>";
		}else{
			echo "<div id='passwd_result' style='margin-top: 10px;'>File successfully added to playlist  \"" . $_POST['play'] . "\"</div>";
			add_to_playlist($_POST['add_to_list'], $_POST['play']);
		}
    }
    $info = get_title($_GET['id']);
	$comments = get_comments($filepath.$filename);
	$count = 0;
	$views = $GLOBALS['d']->query('select views from view where mediaid = ?', [$_GET['id']])[0]['views'];
    foreach($comments as $c){ $count++;}
?>  
	<!-- title and description-->
        <div style='padding-top: 10px;'>
			<textarea type='text' name="new_title" class="title_input" maxlength='100' readonly><?php echo $info[0]['title'];?></textarea>
			<?php if(isset($_SESSION['login']) && $_SESSION['login'] == true){?>
				<?php if($like){?>
					<form action='' method='post'>
						<button class='a' name='unlike' style='display: block; margin-left: 40px;'>Liked &#10004;</button>
					</form>
				<?php }else{?>
					<form action='' method='post'>
						<button class='button' name='like' style='display: block; margin-left: 40px;' value="<?php echo $_GET['id'];?>">Like</button>
					</form>
				<?php }
				}else{ ?>
					<form action='' method='post'>
					<br><a href="login.php?id=<?php echo $_GET['id'];?>" class="a" style='background-color: blue; margin-left: 40px;'>Like</a><br><br>
					</form>
				<?php } ?>
		</div>
		<div style='display: block;'>
			<p class="media_user">Views: <?php echo $views;?></p>
		</div>
        <div>
			<div>
                <a href='channel.php?user=<?php echo $user;?>' target='_self' class='media_user' style='display: block'>By: <?php echo $user;?> </a>
                <p class='src_name' style='margin-left: 250px; display: block; text-decoration: none'> Uploaded on: <?php echo $uploadtime;?></p>
			</div>
			<hr style='width: 708px;float:left; margin-left: 40px;'><br>
			<textarea type='text' name="new_description" class="description_input" readonly><?php echo $info[0]['description'];?></textarea>
			<hr style='width: 708px;float:left; margin-left: 40px;'><br><br>
        </div>
	<!--error message-->
	<?php if(isset($error)){  echo "<div id='passwd_result'>".$error."</div>"; }?>
	<!--keyword-->
	<div style='display: block;'>
		<p class='category'>Keywords: </p>
		<?php $keywords = get_keywords($_GET['id']); 
		foreach($keywords as $key){
		?>
			<a class='cat_link' href='search.php?search=<?php echo urlencode($key['word']);?> 'style='display: inline;'><?php echo $key['word'] . ', '; ?></a>
		<?php 
		}?>

	</div>
	<!--category-->
	<div style='display: block;'>
		<p class='category'>Categories: </p>
		<?php $cat = get_categories($_GET['id']);
			 $all_cat = get_all_categories($_GET['id']); 
		foreach($cat as $c){
		?>
			<a class='cat_link' href='index.php?category=<?php echo urlencode($c['cat']);?> 'style='display: inline;'><?php echo $c['cat'] . ', '; ?></a>
		<?php 
		}?>

	</div>
	<!--copy link-->
	<div  style='padding-bottom: 20px; padding-top: 20px;'>
		<?php $link = $_SERVER['SERVER_NAME'];
			if(isset($_SERVER['SERVER_PORT'])){ $link .= ':' . $_SERVER['SERVER_PORT'];}
			$link .= $_SERVER['REQUEST_URI']; 
			$strlen = strlen($link) * 6.8; ?>
		<p style='display: inline; margin-left: 40px;'>Link: </p>
		<input class='link_input' style='width: <?php echo $strlen;?>px;' value='<?php echo $link;?>' readonly></input>
		<a class='a' href="<?php echo $filepath.$filename;?>" target="_self" onclick="javascript:saveDownload(<?php echo $id;?>);" download>Download</a>
	</div>
	<!--comment count-->
	<div>
		<?php echo "<div style='margin-left: 40px; margin-top: 10px;'><h2 class='comment_num'>".$count." comment"; if($count > 1 || $count == 0) {echo "s";}  echo "</h2></div>";?>
	</div>
	<!--add a comment and add to playlist if signed in-->
	<div style='margin-top: 20px; height: 30px; margin-left: 10px;'>
		<?php if (!isset($_SESSION['login']) || $_SESSION['login'] == false){ ?>
			<a href="login.php?id=<?php echo $_GET['id'];?>" class="a" style='background-color: blue; margin-left: 40px;'>Comment</a>
		<?php }else{?>
			<form action='' method='post'>
                <input name='comment' type='text' placeholder='Insert Comment here...' size='100' style='margin-left: 30px;'></input>
				<button name='add_comment' value='<?php echo $filepath.$filename;?>'class="button" style='background-color: #00B8CC;'>Add Comment</button>
			</form>
				<div id='<?php echo $filename . '!';?>' style='display: inline'>
					<button class='button' onclick="show_inline('<?php echo $filename;?>');dis('<?php echo $filename . '!';?>');">Add to Playlist</button>
				</div>
				<div id='<?php echo $filename;?>' style='display: none'>
					<form action='' method='post'>
						<select name='play' id='play'>
							<option value='' selected disabled>Select Playlist</option>
								<?php 
									$list = get_playlists();
									foreach($list as $l){
									echo "<option value=\"". $l['title'] ."\">" . $l['title'] . "</option>";
									}
								?>
						</select>
						<button class='button' name='add_to_list' value="<?php echo $_GET['id'];?>">Add to playlist</button>
					</form>
					<button class='red_button' onclick="dis('<?php echo $filename;?>');show_inline('<?php echo $filename . '!';?>');">Cancel</button>
				</div>
		<?php } ?>
	</div>	
	<div style='margin-left: 40px;'>
		<hr style='width: 708px;float:left;'><br>
		<?php 
		foreach($comments as $c){
			$comment = $c['comment'];
			$user = $c['user'];
			$time = explode(' ', $c['time'])[0];
			if(isset($_SESSION['username']) && $user == $_SESSION['username']){
				echo "<p class='comment_title_red'>" . $user . "</p>";
			}else{
				echo "<p class='comment_title' style='font: bold;'>" . $user . "</p>";
			}
			echo "<p class='comment_time' style='margin-left: 10px;'>" . $time . "</p><br>";
			echo "<p class='comment_comment'>" . $comment . "</p><br>";
			//will only show if delete has been clicked
			if(isset($_SESSION['username']) && $user == $_SESSION['username']){
				echo "<div id='confirm" . $comment . "' style='margin-top: 5px; display: none'><form action='' method='post'><button name='confirm' class='red_button' value=\"" . $filepath.$filename.'.'.$comment.'.'.$user."\">Confirm Delete</button></form>";
				echo "<button name='delete' class='red_button' onclick=\"show('delete" . $comment . "');hide('confirm" . $comment . "');\">Cancel</button></div>";
				echo "<div id='delete" . $comment . "' style='margin-top: 5px; display: block'><button class='button' onclick=\"show('confirm" . $comment . "');hide('delete" . $comment . "');\">Delete</button></div>";
			}
			echo "<hr style='width: 708px;float:left;'><br>";
        }
		?>
    </div>
    <?php 
    }else{
        echo "Media file does not exist";
    } ?>
</div>
</body>
</html>
