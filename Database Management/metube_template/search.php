<?php
@ob_start();
session_start();
include "function.php";

//sets the value for the sorting videos/images. only one will be activated at once. 
$vdate_up=false; $vdate_down=false; $vtitle_up=false; $vtitle_down=false; $vuser_up=false; $vuser_down=false; $vviews_down=false; $vviews_up=false;
$idate_up=false; $idate_down=false; $ititle_up=false; $ititle_down=false; $iuser_up=false; $iuser_down=false; $iviews_down=false; $iviews_up=false;
if(!isset($_POST['v-date']) && !isset($_POST['v-title']) && !isset($_POST['v-user']) && !isset($_POST['v-views'])){$_POST['v-views'] = 0;}
if(!isset($_POST['i-date']) && !isset($_POST['i-title']) && !isset($_POST['i-user']) && !isset($_POST['i-views'])){$_POST['i-views'] = 0;}
if(isset($_POST['v-date'])){ if($_POST['v-date'] == 0){$vdate_down=true;}else{$vdate_up=true;}}
if(isset($_POST['v-title'])){ if($_POST['v-title'] == 0){$vtitle_down=true;}else{$vtitle_up=true;}}
if(isset($_POST['v-views'])){ if($_POST['v-views'] == 0){$vviews_down=true;}else{$vviews_up=true;}}
if(isset($_POST['v-user'])){ if($_POST['v-user'] == 0){$vuser_down=true;}else{$vuser_up=true;}}
if(isset($_POST['i-date'])){ if($_POST['i-date'] == 0){$idate_down=true;}else{$idate_up=true;}}
if(isset($_POST['i-title'])){ if($_POST['i-title'] == 0){$ititle_down=true;}else{$ititle_up=true;}}
if(isset($_POST['i-user'])){ if($_POST['i-user'] == 0){$iuser_down=true;}else{$iuser_up=true;}}
if(isset($_POST['i-views'])){ if($_POST['i-views'] == 0){$iviews_down=true;}else{$iviews_up=true;}}

if(isset($_POST['logout'])){$_SESSION['login'] = false; $_SESSION['username'] = '';}
$category = '';
$user = '';
$keyword = '';

if(isset($_GET['category'])){
    $category = $_GET['category'];
}
if(isset($_GET['user'])){
    $user = $_GET['user'];
}
if(isset($_GET['keyword'])){
    $keyword = $_GET['keyword'];
}
if(isset($_POST['search_button'])){
    if(strcmp($_POST['search_val'], '') == 0 && !isset($_POST['category'])){
        $error = "Search can't be empty";
    }else{
        $sentence = str_replace(' ', '+', $_POST['search_val']);
        if(isset($_POST['category'])){ $sentence .= "&category=" . $_POST['category'];}
        echo $sentence;
        header('Location: search.php?search=' . $sentence);
        exit();
    }
}
$categories = get_search_categories();


$search = explode(' ', $_GET['search']);
if($search[0] == ''){
     $search = '';
     $results = category_sort($search, $category);
     $size = sizeof_category($category);
}
else if($category == ''){
    $results = search($search);
    $size = sizeof_keyword($search);
}else{
    $results = category_sort($search, $category);
    $size = sizeof_both($search, $category);
}
?>
<html>
<link rel="stylesheet" type="text/css" href="css/default.css" />
<script type="text/javascript" src="js/jquery-latest.pack.js"></script>
<title>Metube</title>
<body>


<div id='header'>
    <a href='index.php' style='text-decoration: none; float: left;'><h1>Search Results</h1></a>
    <form action='' method='post'>
        <input class='search' name='search_val' height='50' type="text" placeholder='Enter search here'>
        <select class='cat_search'  name='category'>
            <option value='' selected disabled>Search by Category</option>
            <?php foreach($categories as $c){?>
                <option value="<?php echo $c['cat'];?>"><?php echo $c['cat'];?></option>
            <?php } ?>
        </select>
        <button class='search_button' name='search_button'>Search</button>
    </form>
</div>

<div id='navbar'>
<ul class='ul'>
    <li><button class='nav_button' onclick="back('index.php');">Go to Main Page</li>
    <div id='signIn' style='display: block'>
        <li><button class="nav_button" onclick="login()">Sign In</button></li>
    </div>
    <div id='home' style='display: none'>
        <li><button class="nav_button" onclick="back('browse.php')">Go To Homepage</button></li>
    </div>
    <?php 
        if($_SESSION['login'] === true){
            echo "<script type='text/javascript'> show('home'); hide('signIn'); </script>";?>
            <li><form action = '' method='post'><button name='logout' class='nav_button'>Log Out</button></form>
        <?php }
        else{
            echo "<script type='text/javascript'> show('signIn'); hide('home'); </script>";
        }
    ?>
    
</ul>
</div>

<div id='home_inline' style='padding-bottom: 100px;'>
    <!-- display all the videos -->
    <h2 style='margin-bottom: 20px; margin-left: 50px;'><?php echo $size; ?> Result<?php if($size > 1 || $size == 0){echo "s";}?> found for: <?php echo $_GET['search'];?> <?php if(isset($_GET['category'])){echo "<br>Categories: ". $_GET['category'];}?></h2>
    <table style='margin-left: 40px;'>
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
                    <h3 class="title_input" style='float: left; margin-left: 0;'><?php echo $r['title'];?></h3><br>
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




</body>
</html>
