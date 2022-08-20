<?php
	@ob_start();
	session_start();
	include "function.php";
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
	if(isset($_POST['remove_comment'])){
		$arr = explode('.', $_POST['remove_comment']);
		$filepath = $arr[0].'.'.$arr[1];
		$comment = $arr[2];
		$user = $arr[3];
		hide_comment($filepath, $comment, $user);
		unset($_POST['confirm']);
    }
	if(isset($_POST['confirm'])){
		$arr = explode('.', $_POST['confirm']);
		$filepath = $arr[0].'.'.$arr[1];
		$comment = $arr[2];
		$user = $arr[3];
		print_r($arr);
		delete_comment($filepath, $comment, $user);
    }
    if(isset($_POST['title'])){
        change_title($_POST['new_title'], $_GET['id']);
    }
    if(isset($_POST['description'])){
        change_description($_POST['new_description'], $_GET['id']);
	}
	if(isset($_POST['cat_button'])){
		if(!isset($_POST['cat']) && $_POST['new_category'] == ''){
			$error = "Must choose or create category to add to file";
		}else{
			if(isset($_POST['cat']) && $_POST['cat'] != ''){
				add_category($_POST['cat'], $_GET['id']);
			}
			if(isset($_POST['new_category']) && $_POST['new_category'] != ''){
				add_category($_POST['new_category'], $_GET['id']);
			}
		}
	}
	if(isset($_POST['rm_cat_button'])){
		if($_POST['rm_cat'] == ''){
			$error = "Must select category to remove";
		}else{
			remove_category($_POST['rm_cat'], $_GET['id']);
		}
	}
	if(isset($_POST['key_button'])){
		if($_POST['new_keyword'] == ''){
			$error = "New keyword must not be blank";
		}else{
			add_keyword($_POST['new_keyword'], $_GET['id']);
		}
	}
	if(isset($_POST['rm_key_button'])){
		if(!isset($_POST['rm_key']) || $_POST['rm_key'] == ''){
			$error = "Must select keyword to remove";
		}else{
			remove_keyword($_POST['rm_key'], $_GET['id']);
		}
	}
	if(isset($_POST['like'])){
		insert_like($_GET['id']);
	}
	if(isset($_POST['unlike'])){
		delete_like($_GET['id']);
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
		
		updateMediaTime($_GET['id']);
		
		$user = $result_row['username'];
		$uploadtime = explode(' ', $result_row['uploadtime'])[0];
		$filename=$result_row['filename'];
		$filepath=$result_row['filepath'];
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
<div id='inline' style='padding-bottom: 200px;'>
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
      
    if(isset($_POST['title'])){
        echo "<div id='passwd_result'>Title successfully changed</div>";
    }
    if(isset($_POST['description'])){
        echo "<div id='passwd_result'>Description successfully changed</div>";
    }
	if(isset($_POST['add_to_list'])){
		$files = get_playlist_files($_POST['play']);
		$arr = array();
		foreach($files as $f){
			array_push($arr, $f['filename']);
		}
		if ($_POST['play'] == ''){
			echo "<div id='passwd_result'>Must select a playlist</div>";
		}
		else if(in_array($_POST['add_to_list'], $arr)){
			echo "<div id='passwd_result'>File \"" . $_POST['add_to_list'] . "\" already exists in playlist:  " . $_POST['play'] . "</div>";
		}else{
			echo "<div id='passwd_result'>File \"" . $_POST['add_to_list'] . "\" successfully added to playlist  \"" . $_POST['play'] . "\"</div>";
			add_to_playlist($_POST['add_to_list'], $_POST['play']);
		}
    }
    $info = get_title($_GET['id']);
	$comments = get_comments($filepath.$filename);
	$count = 0;
	$views = $GLOBALS['d']->query('select views from view where mediaid = ?', [$_GET['id']])[0]['views'];
    foreach($comments as $c){ $count++;}
?>  <!--Title and descripstion-->
    <form method="post" action="own_media.php?id=<?php echo$_GET['id'];?>">
        <div style='padding-top: 10px;'>
            <textarea type='text' name="new_title" class="title_input" style='border-color: #303030;' maxlength='100'><?php echo $info[0]['title'];?></textarea>
            <button name = 'title' class='button'>Change Title</button>
            <button class="button" name="reset" type="submit">Reset</button>
        </div>
        <div>
			<div>
                <a href='channel.php?user=<?php echo $user;?>' target='_self' class='media_user' style='display: block'>By: <?php echo $user;?> </a>
                <p class='src_name' style='margin-left: 200px; display: block;'> Uploaded on: <?php echo $uploadtime;?></p>
			</div>
			<hr style='width: 708px;float:left; margin-left: 40px;'><br>
			<textarea type='text' name="new_description" style='border-color: #303030;' class="description_input"><?php echo $info[0]['description'];?></textarea>
			<hr style='width: 708px;float:left; margin-left: 40px;'><br>
            <button name='description'class='button'>Change Description</button>
			<button class="button" name="reset" type="submit">Reset</button>
        </div>
	</form>
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
				} ?>
	<div style='display: block;'>
			<p class="media_user">Views: <?php echo $views;?></p><br>
	</div>
	<!--error message-->
	<?php if(isset($error)){  echo "<div id='passwd_result' style='margin-left: 40px; width: 700px;'>".$error."</div>"; }?>
	<!--keyword-->
	<div style='display: block; margin-top: 10px;'>
		<p class='category'>Keywords: </p>
		<?php $keywords = get_keywords($_GET['id']); 
		foreach($keywords as $word){
		?>
			<p style='display: inline;'><?php echo $word['word'] . ', '; ?></p>
		<?php 
		}?>
		<div  id='add_keyword' style='display: inline;'>
			<form action='' method='post'>
				<input class='new_category' name='new_keyword' placeholder='Type New Keyword Here...' size='25'></input>
				<button name='key_button' class='button'>Add Keyword</button>
			</form>
			<button class='red_button' onclick="show_inline('rm_keyword');dis('add_keyword');">Remove Keyword</button>
		</div>
		
		<div id='rm_keyword' style='display: none;'>
			<form action='' method='post'>
				<select class='cat_select' name='rm_key' id='rm_key'>
					<option value='' selected disabled>Remove Keyword</option>

					<?php foreach($keywords as $key){
					?>
						<option value='<?php echo $key['word'];?>'><?php echo $key['word'];?></option>
					<?php }?>
				</select>
				<button name='rm_key_button' class='red_button'>Remove Keyword</button>
			</form>
			<button class='red_button' onclick="show_inline('add_keyword');dis('rm_keyword');" style='display: inline;'>Cancel</button>
		</div>
	</div>
	<!--category-->
	<div style='display: block; margin-top: 10px;'>
		<p class='category'>Categories: </p>
		<?php $cat = get_categories($_GET['id']);
			 $all_cat = get_all_categories($_GET['id']); 
		foreach($cat as $c){
		?>
			<p style='display: inline;'><?php echo $c['cat'] . ', '; ?></p>
		<?php 
		}?>
		<div  id='add_category' style='display: inline;'>
			<form action='' method='post'>
				<select class='cat_select' name='cat' id='cat'>
					<option value='' selected disabled>Add to category</option>

					<?php foreach($all_cat as $a){
					?>
						<option value='<?php echo $a['cat'];?>'><?php echo $a['cat'];?></option>
					<?php }?>
				</select>
				<input class='new_category' name='new_category' placeholder='Or type new category here...' size='25'></input>
				<button name='cat_button' class='button'>Add Category to File</button>
			</form>
			<button class='red_button' onclick="show_inline('rm_category');dis('add_category');">Remove Category</button>
		</div>
		
		<div id='rm_category' style='display: none;'>
			<form action='' method='post'>
				<select class='cat_select' name='rm_cat' id='rm_cat'>
					<option value='' selected disabled>Remove category</option>

					<?php foreach($cat as $a){
					?>
						<option value='<?php echo $a['cat'];?>'><?php echo $a['cat'];?></option>
					<?php }?>
				</select>
				<button name='rm_cat_button' class='red_button'>Remove Category from File</button>
			</form>
			<button class='red_button' onclick="show_inline('add_category');dis('rm_category');" style='display: inline;'>Cancel</button>
		</div>
	</div>
	<!--copy link-->
	<div  style='padding-bottom: 20px; padding-top: 20px;'>
		<?php $link = $_SERVER['SERVER_NAME'];
			if(isset($_SERVER['SERVER_PORT'])){ $link .= ':' . $_SERVER['SERVER_PORT'];}
			$link .= str_replace("own_", "" , $_SERVER['REQUEST_URI']); 
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
	<!--the comments-->
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
				echo "<p class='comment_time' style='margin-left: 10px;'>" . $time . "</p><br>";
				echo "<p class='comment_comment'>" . $comment . "</p><br>";
				echo "<div id='confirm" . $comment . "' style='margin-top: 5px; display: none'><form action='' method='post'><button name='confirm' class='red_button' value=\"" . $filepath.$filename.'.'.$comment.'.'.$user."\">Confirm Delete</button></form>";
				echo "<button name='delete' class='red_button' onclick=\"show('delete" . $comment . "');hide('confirm" . $comment . "');\">Cancel</button></div>";
				echo "<div id='delete" . $comment . "' style='margin-top: 5px; display: block'><button class='button' onclick=\"show('confirm" . $comment . "');hide('delete" . $comment . "');\">Delete</button></div>";
				echo "<hr style='width: 708px;float:left;'><br>";
			}else{
				echo "<p class='comment_title' style='font: bold;'>" . $user . "</p>";
				echo "<p class='comment_time' style='margin-left: 10px;'>" . $time . "</p><br>";
				echo "<p class='comment_comment'>" . $comment . "</p><br>";
				if(strcmp($comment, "This comment was removed by the owner of this video") != 0){
					echo "<div id='confirm" . $comment . "' style='margin-top: 5px; display: none'><form action='' method='post'><button name='remove_comment' class='red_button' value=\"" . $filepath.$filename.'.'.$comment.'.'.$user."\">Confirm Remove</button></form>";
					echo "<button name='delete' class='red_button' onclick=\"show('delete" . $comment . "');hide('confirm" . $comment . "');\">Cancel</button></div>";
					echo "<div id='delete" . $comment . "' style='margin-top: 5px; display: block'><button class='button' onclick=\"show('confirm" . $comment . "');hide('delete" . $comment . "');\">Remove Comment</button></div>";
				}
				echo "<hr style='width: 708px;float:left;'><br>";
			}
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
