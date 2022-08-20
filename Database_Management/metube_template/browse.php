<?php
	@ob_start();
	session_start();
	include "function.php";
	include_once "mysqlClass.inc.php";
	$database = new dbh();
	$GLOBALS['d'] = $database;
	//runs script to give all files permissions to be able to read
	echo shell_exec("sh permis.sh");
	if(isset($_POST['cancel'])){
		unset($_POST['delete']);
	}
	if(isset($_POST['c_play'])){
		create_play($_POST['playlist_title']);
		unset($_POST['c_play']);
	}
	if(isset($_POST['confirm_delete'])){
		delete_playlist($_POST['confirm_delete']);
		unset($_POST['confirm_delete']);
	}
	if(isset($_POST['confirm_remove'])){
		remove_from_playlist($_POST['confirm_remove']);
		unset($_POST['remove_file']);
	} 
	if(isset($_POST['confirm_remove_file'])){
		delete_file($_POST['confirm_remove_file']);
	}
	if(isset($_POST['logout'])){
		$_SESSION['login'] = false;
		$_SESSION['username'] = '';
		header('Location: index.php');
		exit();
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Media browse</title>
<link rel="stylesheet" type="text/css" href="css/default.css" />
<script type="text/javascript" src="js/jquery-latest.pack.js"></script>
<script type="text/javascript">
function saveDownload(id)
{
	$.post("media_download_process.php",
	{
       id: id,
	},
	function(message) 
    { }
 	);
} 
</script>
</head>

<body>
<div id="header"><h1>Welcome <?php echo $_SESSION['username'];?></h1>
</div>


<div id='navbar'>
	<ul>
		<li><button class="nav_button" onclick="back('index.php')">Go To Main Page</button></li>
		<li><button class="nav_button" onclick="inbox()">Inbox</button></li>
		<li><button class="nav_button" onclick="back('contact.php')">Contact List</button></li>
		<li><button class="nav_button" onclick="back('media_upload.php');">Upload File</button></li>
		<li><button class="nav_button" onclick="update()">Update Information</button></li>
		<li><form action='' method='post'><button name='logout' class="nav_button">Log Out</button></form></li>
	</ul>
</div>


<div id='inline' style='padding-left: 10px;'>
	<div id='upload_result'>
	<?php 
		if(isset($_REQUEST['result']) && $_REQUEST['result']!=0)
		{
		
			echo upload_error($_REQUEST['result']);

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
				echo "<div id='passwd_result'>File already exists in playlist:  " . $_POST['play'] . "</div>";
			}else{
				add_to_playlist($_POST['add_to_list'], $_POST['play']);
			}
		}
	?>
	</div>
	<?php

		$query = "SELECT media.*, title.title as file_title FROM media inner join title on media.mediaid = title.mediaid WHERE filepath = ? order by media.mediaid ASC";
		$vals = ["uploads/" . $_SESSION['username'] . '/'];
		$media = $GLOBALS['d']->query($query, $vals);
	?>
    
	<div style="background:#339900;color:#FFFFFF; width:150px; margin-bottom: 10px;">Uploaded Media</div>
		<?php if (!$media)
			{
				echo "You have no uploads";
			}?>
		<table id='table-scroll' >
			<?php
				foreach($media as $result)
				{ 
			?>
        		<tr>			
					<td >
						<?php 
							echo "<p style='color: white;'>" . $result['mediaid'] . "</p>";
						?>
					</td>
            		<td>
						<a class='a' href="own_media.php?id=<?php echo $result['mediaid'];?>" target="_self"><?php echo $result['file_title'];?></a> 
            		</td>
            		<td>
            			<a class='a' href="<?php echo $result['filepath'].$result['filename'];?>" target="_self" onclick="javascript:saveDownload(<?php echo $result['mediaid'];?>);" download>Download</a>
					</td>
					<td>
						<div id='<?php echo $result['filename'] . '!';?>' style='display: block'>
							<button class='button' onclick="show('<?php echo $result['filename'];?>');hide('<?php echo $result['filename'] . '!';?>');">Add to Playlist</button>
						</div>
						<div id='<?php echo $result['filename'];?>' style='display: none'>
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
							<button class='button' name='add_to_list' value="<?php echo $result['mediaid'];?>">Add to playlist</button></form>
							<button class='red_button' onclick="hide('<?php echo $result['filename'];?>');show('<?php echo $result['filename'] . '!';?>');">Cancel</button>
						</div>
					</td>
					<td>
						<div id='<?php echo $result['filename'] . '>';?>' style='display: block'>
							<button class='red_button' onclick="show('<?php echo $result['filename'] . '?';?>');hide('<?php echo $result['filename'] . '>';?>');">Delete</button>
						</div>
						<div id='<?php echo $result['filename'] . '?';?>' style='display: none'>
							<form action='' method='post'><button class='red_button' name='confirm_remove_file' value='<?php echo $result['filepath'].$result['filename'].'/'.$result['mediaid'];?>'>Confirm Delete</button></form>
							<button class='red_button' onclick="show('<?php echo $result['filename'] . '>';?>');hide('<?php echo $result['filename'] . '?';?>');">Cancel</button>
						</div>
					</td>
				</tr>
        	<?php
			}
			?>
		</table>
	<div>
		<button style="margin-top: 50px" class="button" onmouseover="show('createPlay');" onmouseout="hide('createPlay');" onclick="toggle('createPlay');">Create Playlist</button>
		<div id="createPlay" style="display: none;">
			<label for="playlist_title" style='color: white;'>Playlist Title:</label>
			<form action="" method="post"><input class="text" type="text" name="playlist_title" placeholder="Max of 20 characters" maxlength="20" size="30">
			<button class="button" name="c_play" type="submit">Create</button></form>
		</div>
	</div>
	<div>
		<?php $plays = get_playlists();
		if(sizeof($plays) > 0){
			echo "<h2>Playlists</h2>";
		}
		foreach($plays as $p){?>
			<button style="margin-top: 20px;" class='button' onmouseover="show('<?php echo $p['title'];?>');" onmouseout="hide('<?php echo $p['title'];?>');" onclick="toggle('<?php echo $p['title'];?>');"><?php echo $p['title'];?></button>
			<?php echo "<form action = '' method='post'><button style='margin-left: 50px;' class='red_button' name=\""; 
			if(isset($_POST['delete']) && $_POST['delete'] == $p['title']){ 
				echo "confirm_delete\" value=\"" . $p['title'] . "\">Confirm Delete " . $p['title'] . "</button>";
				echo "<button style='margin-left: 20px;' class='red_button' name='cancel'>Cancel</button></form><br>";
			}else{
			
				echo "delete\" value=\"" . $p['title'] . "\">Delete " . $p['title'] . "</button></form><br>";
			}
			?> 
			<div>
				<table width="50%" id="<?php echo $p['title'];?>"style="display: none;">
				<?php if((isset($_POST['remove_file']) && explode("!", $_POST['remove_file'])[1] == $p['title'])){ 
					echo "<script> show('" . $p['title'] . "');</script>";
					} ?>
					<?php 
					$files = get_playlist_files($p['title']);
					if(sizeof($files) == 0){
						echo "<td>Playlist is empty</td>";
					}else{
						foreach($files as $f){
							echo "<tr><td><a class='a' href=\"";
							if(is_own_media($f['mediaid'])){
								echo "own_media.php";
							}else{
								echo "media.php";
							}
							echo "?id=" . $f['mediaid'] .  "\" target=\"_self\">" .  $f['file_title'] . "</a></td>";
							echo "<td><a class='a' href=\"" . $f['filepath'].$f['filename'] . "\" target=\"_self\" onclick=\"javascript:saveDownload(\"" . $f['mediaid']  . "\");\">Download</a></td>";

							echo "<td><form action = '' method='post'><button class='red_button' name=\""; 
							if(isset($_POST['remove_file']) && explode("!", $_POST['remove_file'])[0] == $f['file_title']){ 
								echo "confirm_remove\" value=\"" . $p['title'] . "!" . $f['mediaid'] . "\">Confirm Delete " . $f['file_title'] . "</button>";
								echo "<button style='margin-left: 20px;' class='red_button' name='cancel'>Cancel</button></form></td></tr>";
							}else{
								echo "remove_file\" value=\"" . $f['file_title'] . "!" . $p['title'] .  "\">Remove from playlist</button></form></td></tr>";
							}
						}
					}

					?>
				</table>
			</div>
		<?php
		} $results = get_liked_videos();
		?> 
		<br><h2 style='margin-left: 10px;'>Liked</h2>
		<table>
			<?php foreach($results as $r){
			$username = explode('/', $r['filepath'])[1];?>
			<tr>
				<!--video or image-->
				<td>
					<div style='display: block; max-height: 200px; max-width: 400px; overflow: scroll; margin-bottom: 20px;'>
						<?php if (is_video($r['type'])){ ?>
							<a href="<?php if(is_own_media($r['mediaid'])){echo "own_media.php";}else{echo"media.php";}?>?id=<?php echo $r['mediaid'];?>" target='_self'>
								<video style="width: 400px; height: auto;">
									<source src="<?php echo $r['filepath'].$r['filename'];?>" type="<?php echo $r['type'];?>"></source>
								</video>
							</a>
						<?php }else{ ?>
							<a href="<?php if(is_own_media($r['mediaid'])){echo "own_media.php";}else{echo"media.php";}?>?id=<?php echo $r['mediaid'];?>" target='_self'>
								<img src="<?php echo $r['filepath'].$r['filename'];?>" style='width: 400px; height: auto; margin-left: 10px;'>
							</a>
						<?php } ?>
					</div>
				</td>
				<!--description-->
				<td style='vertical-align: top; padding-left: 20px;'>
					<div style="display: inline-block; max-height: 200px; max-width: 400px; overflow: scroll;">
						<h3 class="title_input" style='float: left; margin-left: 0;'><?php echo $r['title'];?></h3><br><br>
						<p class='src_name' style='padding: 0;'>Views: <?php echo $r['views'];?></p>
						<span style='font-size: 22;'>&#183;</span>
						<p class='src_name' style='padding: 0;'><?php echo explode(' ', $r['uploadtime'])[0];?></p><br><br>
						<a href='channel.php?user=<?php echo $username;?>' target='_self' class='media_user' style='padding: 0; margin: 0;'>By: <?php echo $username;?></a><br><br>
						<p class='src_name' style='padding: 0;'><?php echo $r['description']; ?></p><br><br>
						<!--keywords-->
						<div style='display: block;'>
							<p class='category' style='margin: 0; font-size: 14;'>Keywords: </p>
							<?php $keywords = get_keywords($r['mediaid']); 
							foreach($keywords as $key){
							?>
								<a class='cat_link' href='search.php?search=<?php echo urlencode($key['word']);?> 'style='display: inline;'><?php echo $key['word'] . ', '; ?></a>
							<?php 
							}?>
						</div>
						<!--category-->
						<div style='display: block;'>
							<p class='category' style='margin: 0; font-size: 14;'>Categories: </p>
							<?php $cat = get_categories($r['mediaid']);
							foreach($cat as $c){?>
								<a class='cat_link' href='index.php?category=<?php echo urlencode($c['cat']);?> 'style='display: inline;'><?php echo $c['cat'] . ', '; ?></a>
							<?php 
							}?>
						</div>
					</div>
				</td>
			</tr>
			<?php } ?>
    </table>
	</div>
</div>
</body>
</html>
