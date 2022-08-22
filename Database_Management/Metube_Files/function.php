<?php
include_once "mysqlClass.inc.php";
$GLOBALS['d'] = new dbh();
?>

<?php
function user_pass_check($username, $pass)
{
	$password = hash('ripemd160', $pass);
	$database = new dbh();
	$sql = "SELECT password from account where username = ?";
	$val = [$username];
	$row = $database->query($sql, $val);
	if (!$row[0]){
		return 1;	   
	}else{
		if(strcmp($row[0]['password'],$password))
			return 2; //wrong password
		else 
			return 0; //Checked.
	}
}

function updateMediaTime($mediaid)
{
	$database = new dbh();
	$query = "update media set lastaccesstime = NOW()";
	$database->insert($query);
}

function upload_error($result)
{
	//view erorr description in http://us2.php.net/manual/en/features.file-upload.errors.php
	switch ($result){
	case 1:
		return "UPLOAD_ERR_INI_SIZE";
	case 2:
		return "UPLOAD_ERR_FORM_SIZE";
	case 3:
		return "UPLOAD_ERR_PARTIAL";
	case 4:
		return "UPLOAD_ERR_NO_FILE";
	case 5:
		return "File has already been uploaded";
	case 6:
		return  "Failed to move file from temporary directory";
	case 7:
		return  "Upload file failed";
	case 8:
		return "Video format not supported. Must be 'mp4' or 'webm'";
	case 9:
		return "Image format not supported. Must be '.apng' or 'gif' or 'ico' or 'cur' or 'jpg' or 'jpeg' or 'jfif' or 'pjpeg' or 'pjp' or 'png' or 'svg'";
	}
}

function test()
{
	$database = new dbh();
	$database = $database->connect();

	$sql = "SELECT * FROM account";
	$stmt = $database->query($sql);
	foreach($stmt as $row){
		echo $row['username'] . '<br>';
		echo $row['password']. '<br>';
	}
}
function compose(){
	echo "Worked!";
}
function getcontacts(){
    $sql = 'select account.username, account.email from contacts inner join account on account.username = contacts.user where contacts.contact = ? and contacts.apply = 0 union select account.username, account.email from contacts inner join account on account.username = contacts.contact where contacts.user = ? and contacts.apply = 0';
    $val = [$_SESSION['username'], $_SESSION['username']];
    return $GLOBALS['d']->query($sql, $val);
}
function getreceived(){
    $sql = 'select contacts.contact, account.email from contacts inner join account on account.username = contacts.contact where contacts.user = ? and contacts.apply = 1';
    $val = [$_SESSION['username']];
    return $GLOBALS['d']->query($sql, $val);
}
function getrequests(){
    $sql = 'select contacts.user, account.email from contacts inner join account on account.username = contacts.user where contacts.contact = ? and contacts.apply = 1';
    $val = [$_SESSION['username']];
    return $GLOBALS['d']->query($sql, $val);
}
function getusers(){
    $sql = 'select account.username, account.email from account where account.username not in (select contacts.user from contacts where contacts.contact = ?) and account.username not in (select contacts.contact from contacts where contacts.user = ?) and account.username != ?';
    $val = [$_SESSION['username'], $_SESSION['username'], $_SESSION['username']];
	return $GLOBALS['d']->query($sql, $val);
}
function create_play($name){
	$sql = 'insert into user_playlists values (?, ?)';
	$val = [$_SESSION['username'], $name];
	$GLOBALS['d']->insert($sql, $val);
}
function get_playlists(){
	$sql = "select * from user_playlists where owner = ?";
	$val = [$_SESSION['username']];
	$list = $GLOBALS['d']->query($sql, $val);
	return $list;
}
function get_playlist_files($title){
	$sql = "select playlist.title, media.mediaid, media.filepath, media.filename, title.title as file_title from playlist inner join media on playlist.id = media.mediaid inner join title on title.mediaid = playlist.id where playlist.owner = ? and playlist.title = ?";
	$val = [$_SESSION['username'], $title];
	return $GLOBALS['d']->query($sql, $val);
}
function delete_playlist($name){
	$sql = 'delete from playlist where owner = ? and title = ?';
	$sql2 = 'delete from user_playlists where owner = ? and title = ?';
	$val = [$_SESSION['username'], $name];
	$GLOBALS['d']->delete($sql, $val);
	$GLOBALS['d']->delete($sql2, $val);
}
function remove_from_playlist($filename){
	$arr = explode('!', $filename);
	$sql = 'delete from playlist where owner = ? and title = ? and id = ?';
	$val = [$_SESSION['username'], $arr[0], $arr[1]];
	$GLOBALS['d']->delete($sql, $val);
}
function add_to_playlist($id, $title){
	$sql = 'insert into playlist values (?, ?, ?)';
	$val = [$_SESSION['username'], $title, $id];
	$GLOBALS['d']->insert($sql, $val);
}
function get_all_videos($date_up=false, $date_down=false, $title_up=false, $title_down=false, $user_up=false, $user_down=false, $views_down=false, $views_up=false, $user='', $category='', $keyword=''){
	$sql = 'select media.*, upload.uploadtime, title.title, view.views from media join upload using(mediaid) join title using(mediaid) join view using(mediaid) ';
	if(strcmp($user, '') != 0){
		$sql .= "where upload.username =\"" . $user . "\" ";
	}
	if(strcmp($category, '') != 0){
		$sql .= " join category using(mediaid) where category.cat = \"" . $category . "\" ";
	}
	$sql .= "ORDER BY ";
	if($date_up){
		$sql .= 'upload.uploadtime DESC';
	}
	else if($date_down){
		$sql .= 'upload.uploadtime ASC';
	}
	else if($title_up){
		$sql .= 'title.title DESC';
	}
	else if($title_down){
		$sql .= 'title.title ASC';
	}
	else if($user_up){
		$sql .= ' media.filepath DESC';
	}
	else if($user_down){
		$sql .= 'media.filepath ASC';
	}
	else if($views_down){
		$sql .= 'view.views DESC';
	}
	else if($views_up){
		$sql .= 'view.views ASC';
	}
	else{
		$sql .= 'upload.uploadtime ASC';
	}
	$files = $GLOBALS['d']->query($sql);
	$arr = array();
	foreach($files as $f){
		$type = $f['type'];
		if(explode('/', $type)[0] == 'video'){
			array_push($arr, $f);
		}
	}
	return $arr;
}
function get_all_images($date_up=false, $date_down=false, $title_up=false, $title_down=false, $user_up=false, $user_down=false, $views_down=false, $views_up=false, $user='', $category='', $keyword=''){
	$sql = 'select media.*, upload.uploadtime, title.title, view.views from media join upload using(mediaid) join title using(mediaid) join view using(mediaid) ';
	if($user != ''){
		$sql .= "where upload.username =\"" . $user . "\" ";
	}
	if(strcmp($category, '') != 0){
		$sql .= " join category using(mediaid) where category.cat = \"" . $category . "\" ";
	}
	$sql .= "ORDER BY ";
	if($date_up){
		$sql .= 'upload.uploadtime DESC';
	}
	else if($date_down){
		$sql .= 'upload.uploadtime ASC';
	}
	else if($title_up){
		$sql .= 'title.title DESC';
	}
	else if($title_down){
		$sql .= 'title.title ASC';
	}
	else if($user_up){
		$sql .= ' media.filepath DESC';
	}
	else if($user_down){
		$sql .= 'media.filepath ASC';
	}
	else if($views_down){
		$sql .= 'view.views DESC';
	}
	else if($views_up){
		$sql .= 'view.views ASC';
	}
	else{
		$sql .= 'upload.uploadtime ASC';
	}

	$files = $GLOBALS['d']->query($sql);
	$arr = array();
	foreach($files as $f){
		$type = $f['type'];
		if(explode('/', $type)[0] == 'image'){
			array_push($arr, $f);
		}
	}
	return $arr;
}
function delete_file($location){
	$ex = explode('/', $location);
	$filepath = $ex[0].'/'.$ex[1].'/';
	$filename = $ex[2];
	$id = $ex[3];
	$val = [$id];
	$sql = 'delete from upload where mediaid = ?';
	$GLOBALS['d']->delete($sql, $val);
	$sql = 'delete from download where mediaid = ?';
	$GLOBALS['d']->delete($sql, $val);
	$sql = 'delete from view where mediaid = ?';
	$GLOBALS['d']->delete($sql, $val);
	$sql = 'delete from fav where mediaid = ?';
	$GLOBALS['d']->delete($sql, $val);
	$sql = 'delete from playlist where id = ?';
	$GLOBALS['d']->delete($sql, $val);
	$sql = 'delete from keywords where mediaid = ?';
	$GLOBALS['d']->delete($sql, $val);
	$sql = 'delete from category where mediaid = ?';
	$GLOBALS['d']->delete($sql, $val);
	$sql = 'delete from title where mediaid = ?';
	$GLOBALS['d']->delete($sql, $val);
	$sql = 'delete from media where filepath = ? and filename = ?';
	$val = [$filepath, $filename];
	unlink($filepath.'/'.$filename);
}
function get_comments($filepath){
	$sql = 'select * from comments where filepath = ? order by time desc';
	$val = [$filepath];
	return $GLOBALS['d']->query($sql, $val);
}
function add_comment($user, $filepath, $comment){
	$sql = 'Insert into comments values (?, ?, ?, NOW())';
	$vals = [$comment, $filepath, $user];
	$GLOBALS['d']->insert($sql, $vals);
}
function delete_comment($filepath, $comment, $user){
	$sql = 'delete from comments where comment = ? and filepath = ? and user = ?';
	$vals = [$comment, $filepath, $user];
	$GLOBALS['d']->delete($sql, $vals);
}
function get_title($id){
	$sql = "select * from title where mediaid = ?";
	$val = [$id];
	return $GLOBALS['d']->query($sql, $val);
}
function change_title($title, $id){
	$sql = 'update title set title = ? where mediaid = ?';
	$val = [$title, $id];
	$GLOBALS['d']->insert($sql, $val);
}
function change_description($des, $id){
	$sql = 'update title set description = ? where mediaid = ?';
	$val = [$des, $id];
	$GLOBALS['d']->insert($sql, $val);
}
function is_own_media($id){
	$sql = 'select * from media where mediaid = ?';
	$vals = [$id];
	$media = $GLOBALS['d']->query($sql, $vals);

	if(isset($_SESSION['login'])){
		if($_SESSION['login'] == true){
			$name = explode('/', $media[0]['filepath'])[1];
			if($_SESSION['username'] == $name){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}else{
		return false;
	}
}
function hide_comment($filepath, $comment, $user){

	$sql = 'update comments set comment = ? where comment = ? and filepath = ? and user = ?';
	$vals = ["This comment was removed by the owner of this video", $comment, $filepath, $user];
	$GLOBALS['d']->delete($sql, $vals);
}
function get_search_categories(){
	return $GLOBALS['d']->query("select distinct cat from category");
}
function get_categories($id){
	$sql = 'select * from category where mediaid = ?';
	$val = [$id];
	return $GLOBALS['d']->query($sql, $val);
}
function get_all_categories($id){
	$sql = 'select distinct cat from category where cat not in (select cat from category where mediaid = ?)';
	$val = [$id];
	return $GLOBALS['d']->query($sql, $val);
}
function add_category($category, $id){
	$sql = 'insert into category values(?, ?)';
	$val = [$id, $category];
	$GLOBALS['d']->insert($sql, $val);
}
function remove_category($category, $id){
	$sql = 'delete from category where mediaid = ? and cat = ?';
	$val = [$id, $category];
	$GLOBALS['d']->insert($sql, $val);
}
function get_views($id){
	return $GLOBALS['d']->query('select views from view where mediaid = ?', [$id])[0]['views'];
}
function get_keywords($id){
	return $GLOBALS['d']->query('select word from keywords where mediaid = ?', [$id]);
}
function add_keyword($word, $id){
	$GLOBALS['d']->insert('insert into keywords values (?, ?)', [$id, $word]);
}
function remove_keyword($word, $id){
	$GLOBALS['d']->delete('delete from keywords where mediaid = ? and word = ?', [$id, $word]);
}
function search($keywords, $vids='none'){
	//if we pass in a video array use that, if not get the media from the database
	if($vids == 'none'){
		$videos = $GLOBALS['d']->query('select media.*, upload.uploadtime, title.title, title.description, view.views from media join upload using(mediaid) join title using(mediaid) join view using(mediaid) order by views desc');
	}else{
		$videos = $vids;
	}
	//select all of the keywords that are passed in order by how many times the id appears in the keywords
	$keyword_string = "select mediaid from keywords where ";
	for($i = 0; $i < sizeof($keywords) - 1; ++$i){
		$keyword_string .= "word = ? or ";
	}
	$keyword_string .= "word = ? ";
	$keyword_string .= "group by mediaid order by count(word) desc";
	$order = $GLOBALS['d']->query($keyword_string, $keywords);
	//reverse the order so we loop in reverse priority
	$order = array_reverse($order);
	$i = 0;
	//list to hold the priority videos
	$list = array();

	//for each id from the order backwards because we're appending to front of array
	//so the most prioritized video will be at the front of the array a the end of the loop
	foreach($order as $o){
		$i = 0;
		//loop through the videos 
		foreach($videos as $v){

			if($v['mediaid'] == $o['mediaid']){
				//if they match take the videos out of the videos array and shift the rest up one
				array_splice($videos, $i, 1);
				//append that video to the front of the list
				array_unshift($list, $v);
				//no need to continue looping through the array so break the for loop
				break;
			}
			$i++;
		}
	}
	$list = array_merge($list, $videos);
	return $list;
}
function category_sort($keywords, $category){
	$cat_array = array();
	$videos = $GLOBALS['d']->query('select media.*, upload.uploadtime, title.title, title.description, view.views from media join upload using(mediaid) join title using(mediaid) join view using(mediaid) order by views desc');
	$cat = $GLOBALS['d']->query("select mediaid from category where cat = ?", [$category]);
	foreach($cat as $c){
		array_push($cat_array, $c['mediaid']);
	}
	//will hold the videos from the category
	$key_cat_arr = array();
	$i = 0;
	//after loop, videos will have left over videos
	foreach($videos as $v){
		if(in_array($v['mediaid'], $cat_array)){
			array_splice($videos, $i, 1);
			array_push($key_cat_arr, $v);
		}else{
			$i++;
		}
	}
	//if there are keywords. sort videos fron category and not in category from the keywords
	if($keywords != ''){
		$key_cat_arr = search($keywords, $key_cat_arr);
		$videos = search($keywords, $videos);
		return array_merge($key_cat_arr, $videos);
	}else{
		return array_merge($key_cat_arr, $videos);
	}
}
function is_video($type){
	return strcmp('video', explode('/', $type)[0]) == 0;
}
function sizeof_keyword($keywords){
	$keyword_string = "select mediaid from keywords where ";
	for($i = 0; $i < sizeof($keywords) - 1; ++$i){
		$keyword_string .= "word = ? or ";
	}
	$keyword_string .= "word = ? ";
	$keyword_string .= "group by mediaid order by count(word) desc";
	$order = $GLOBALS['d']->query($keyword_string, $keywords);
	return sizeof($order);
}
function sizeof_category($category){
	return sizeof($GLOBALS['d']->query("select mediaid from category where cat = ?", [$category]));
}
function sizeof_both($keywords, $category){
	$keyword_string = "select distinct mediaid from keywords where ";
	for($i = 0; $i < sizeof($keywords) - 1; ++$i){
		$keyword_string .= "word = ? or ";
	}
	$keyword_string .= "word = ? ";
	$keyword_string .= "group by mediaid union select mediaid from category where cat = ?";
	array_push($keywords, $category);
	return sizeof($GLOBALS['d']->query($keyword_string, $keywords));
}
function liked($id){
	if(isset($_SESSION['username'])){
		return $GLOBALS['d']->query('select count(*) as c from fav where mediaid = ? and username = ?', [$id, $_SESSION['username']])[0]['c'];
	}else{
		return 0;
	}
	
}
function insert_like($id){
	$GLOBALS['d']->insert('insert into fav values (?, ?)', [$id, $_SESSION['username']]);
}
function delete_like($id){
	$GLOBALS['d']->delete('delete from fav where mediaid = ? and username = ?', [$id, $_SESSION['username']]);
}
function get_liked_videos(){
	$sql = 'select media.*, upload.uploadtime, title.title, title.description, view.views from media join upload using(mediaid) join title using(mediaid) join view using(mediaid) join fav using(mediaid) where fav.username = ?';
	$val = [$_SESSION['username']];
	return $GLOBALS['d']->query($sql, $val);
}
?>
