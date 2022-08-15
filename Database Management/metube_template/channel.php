<?php
@ob_start();
session_start();
include "function.php";
//<!-- COMMENT OUT SHELL EXEC WHEN RUNNING ON LOCALOST!!!!!!!!!!!!!!!!! -->
//echo shell_exec("sh permis.sh");
if(!isset($_SESSION['login'])){
    $_SESSION['login'] = false;
}

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

$link = explode('?', $_SERVER['REQUEST_URI'])[1];
$category = '';
$user = '';
$keyword = '';
$link = $_SERVER['REQUEST_URI'];
if(strpos($_SERVER['REQUEST_URI'], '=')){
    $link = explode('?', $link)[1];
    $type = explode('=', $link)[0];
    $value = explode('=', $link)[1];
    if(strcmp($type,'category') == 0){
        $category = $value;
    }else{
        $category = '';
    }
    if(strcmp($type,'user') == 0){
        $user = $value;
    }else{ $user = ''; }
    if(strcmp($type,'keyword') == 0){
        $keyword = $value;
    }else{ 
        $keyword = '';
    } 
}
if(isset($_POST['search_button'])){
    if(strcmp($_POST['search_val'], '') == 0){
        $error = "Search can't be empty";
    }else{
        $sentence = str_replace(' ', '+', $_POST['search_val']);
        echo $sentence;
        header('Location: search.php?search=' . $sentence);
        exit();
    }
}
?>
<html>
<link rel="stylesheet" type="text/css" href="css/default.css" />
<script type="text/javascript" src="js/jquery-latest.pack.js"></script>
<title>Home</title>
<body>

<div id='header' >
    <a href='index.php' style='text-decoration: none; float: left;'><h1><?php echo $user;?>'s Channel</h1></a>
    <form action='' method='post'>
        <input class='search' name='search_val' placeholder='Enter search here'>
        <button class='search_button' name='search_button'>Search</button>
    </form>
</div>

<div id='navbar'>
<ul class='ul'>
    <div id='signIn' style='display: block'>
        <li><button class="nav_button" onclick="login()">Sign In</button></li>
    </div>
    <div id='home' style='display: none'>
        <li><button class="nav_button" onclick="back('browse.php')">Go To Homepage</button></li>
    </div>
    <li><button class="nav_button" onclick="back('index.php')">Go to Main page</button></li>
    <?php 
        if($_SESSION['login'] === true){
            echo "<script type='text/javascript'> show('home'); hide('signIn'); </script>";
        }
        else{
            echo "<script type='text/javascript'> show('signIn'); hide('home'); </script>";
        }
    ?>
</ul>
</div>

<div id='home_inline'>
    <!-- display all the videos -->
    <?php if(isset($error)){  echo "<div id='passwd_result'>".$error."</div>"; }?>
    <?php  $videos = get_all_videos($vdate_up, $vdate_down, $vtitle_up, $vtitle_down, $vuser_up, $vuser_down, $vviews_down, $vviews_up, $user, $category, $keyword);
    ?>
    <div id='video' stlye='display: block'> 
        <table class='video-scroll'>
            <?php
                $num_vids = sizeof($videos);
                $num_rows = $num_vids / 4;
                if(($num_rows % 1) > 0){
                    $num_rows = $num_rows + 1;
                }
                ?>
                <h1 style='padding-left: 10px; padding-top: 10px;'>Videos</h1>
                    <form action='#video' method='post'>
                        <ul class='home'><?php
                              if(isset($_POST['v-date'])){
                                if ($_POST['v-date'] == 0){ ?>
                                    <li><button name='v-date' value="1" class='order_button' style="width:200px;background-color: black;">Order by Date Added &#9660;</button></li>
                                <?php }else{?>
                                    <li><button name='v-date' value="0" class='order_button' style="width:200px;background-color: black;">Order by Date Added &#9650;</button></li>
                                <?php }
                                } else{?>
                                    <li><button name='v-date' value="0" class='order_button' style="width:200px;">Order by Date Added &#9660;</button></li>
                                <?php
                                }
                                
                                if(isset($_POST['v-title'])){
                                if ($_POST['v-title'] == 0){ ?>
                                    <li><button name='v-title' value="1" class='order_button' style="width:150px;background-color: black;">Order by Title &#9660;</button></li>
                                <?php }else{ ?>
                                    <li><button name='v-title' value="0" class='order_button' style="width:150px;background-color: black;">Order by Title &#9650;</button></li>
                                <?php }
                                } else{?>
                                    <li><button name='v-title' value="0" class='order_button' style="width:150px;">Order by Title &#9660;</button></li>
                                <?php
                                }
                                
                                if(isset($_POST['v-user'])){
                                if ($_POST['v-user'] == 0){ ?>
                                    <li><button name='v-user' value="1" class='order_button' style="width:150px;background-color: black;">Order by User &#9660;</button></li>
                                <?php }else{?>
                                    <li><button name='v-user' value="0" class='order_button' style="width:150px;background-color: black;">Order by User &#9650;</button></li>
                                <?php }
                                } else{?>
                                    <li><button name='v-user' value="0" class='order_button' style="width:150px;">Order by User &#9660;</button></li>
                                <?php
                                }

                                if(isset($_POST['v-views'])){
                                    if ($_POST['v-views'] == 0){ ?>
                                        <li><button name='v-views' value="1" class='order_button' style="width:160px;background-color: black;">Order by Views &#9660;</button></li>
                                    <?php }else{?>
                                        <li><button name='v-views' value="0" class='order_button' style="width:160px;background-color: black;">Order by Views &#9650;</button></li>
                                    <?php }
                                    } else{?>
                                        <li><button name='v-views' value="0" class='order_button' style="width:160px;">Order by Views &#9660;</button></li>
                                    <?php
                                    }
                                ?>
                        </ul>
                    </form>
                <?php
                $vid_num = 0;
                for($i = 0; $i < $num_rows; $i++){
                    echo "<tr style='padding-left: 10px;'>";
                    for ($k = 0; $k < 4; $k++){
                        if($vid_num == $num_vids){
                            break;
                        }
                        $username = explode('/', $videos[$vid_num]['filepath'])[1];
                        $video_name = $videos[$vid_num]['title'];
                        $upload_date = explode(' ', $videos[$vid_num]['uploadtime'])[0];
                        $id = $videos[$vid_num]['mediaid'];
                        $views = get_views($id);
                        $len = 100 - (strlen($username));
                        ?>
                        <td>
                            <a href="<?php if(is_own_media($id)){echo "own_media.php";}else{echo"media.php";}?>?id=<?php echo $videos[$vid_num]['mediaid'];?>" target='_self'>
                                <video>
		                            <source src="<?php echo $videos[$vid_num]['filepath'].$videos[$vid_num]['filename'];?>" type="<?php echo $videos[$vid_num]['type'];?>"></source>
                                </video>
                            </a><br>
                            <h3 class='src_title'><?php echo $video_name;?></h3>
                            <div>
                                <p class='src_name'>Views: <?php echo $views;?></p><br>
                                <a href='channel.php?user=<?php echo $username;?>' target='_self' class='src_name'>By: <?php echo $username;?> </a>
                                <p class='src_name' style='text-align: center; margin-left: <?php echo $len;?>'> Uploaded on: <?php echo $upload_date;?></p>
                            </div>
                        </td>
                        <?php $vid_num++; ?>
                            <?php 
                    } ?>
                    </tr>
                    <?php
                }
            ?>
        </table>
    </div>
    <!-- display all of the images -->
    <?php $images = get_all_images($idate_up, $idate_down, $ititle_up, $ititle_down, $iuser_up, $iuser_down, $iviews_down, $iviews_up, $user, $category, $keyword);
    ?>
    <div id='image'> 
        <table class='image-scroll'>
            <?php
                $num_ims = sizeof($images);
                $num_rows = $num_ims / 4;
                if(($num_rows % 1) > 0){
                    $num_rows = $num_rows + 1;
                }
                ?>
                <h1 style='padding-left: 10px;'>Images</h1>
                    <form action='#image' method='post'>
                            <ul class='home'><?php
                                if(isset($_POST['i-date'])){
                                    if ($_POST['i-date'] == 0){ ?>
                                        <li><button name='i-date' value="1" class='order_button' style="width:200px;background-color: black;">Order by Date Added &#9660;</button></li>
                                    <?php }else{?>
                                        <li><button name='i-date' value="0" class='order_button' style="width:200px;background-color: black;">Order by Date Added &#9650;</button></li>
                                    <?php }
                                    } else{?>
                                        <li><button name='i-date' value="0" class='order_button' style="width:200px;">Order by Date Added &#9660;</button></li>
                                    <?php
                                    }
                                    
                                    if(isset($_POST['i-title'])){
                                    if ($_POST['i-title'] == 0){ ?>
                                        <li><button name='i-title' value="1" class='order_button' style="width:150px;background-color: black;">Order by Title &#9660;</button></li>
                                    <?php }else{?>
                                        <li><button name='i-title' value="0" class='order_button' style="width:150px;background-color: black;">Order by Title &#9650;</button></li>
                                    <?php }
                                    } else{?>
                                        <li><button name='i-title' value="0" class='order_button' style="width:150px;">Order by Title &#9660;</button></li>
                                    <?php
                                    }
                                    
                                    if(isset($_POST['i-user'])){
                                    if ($_POST['i-user'] == 0){ ?>
                                        <li><button name='i-user' value="1" class='order_button' style="width:150px;background-color: black;">Order by User &#9660;</button></li>
                                    <?php }else{?>
                                        <li><button name='i-user' value="0" class='order_button' style="width:150px;background-color: black;">Order by User &#9650;</button></li>
                                    <?php }
                                    } else{?>
                                        <li><button name='i-user' value="0" class='order_button' style="width:150px;">Order by User &#9660;</button></li>
                                    <?php
                                    }

                                    if(isset($_POST['i-views'])){
                                        if ($_POST['i-views'] == 0){ ?>
                                            <li><button name='i-views' value="1" class='order_button' style="width:160px;background-color: black;">Order by Views &#9660;</button></li>
                                        <?php }else{?>
                                            <li><button name='i-views' value="0" class='order_button' style="width:160px;background-color: black;">Order by Views &#9650;</button></li>
                                        <?php }
                                        } else{?>
                                            <li><button name='i-views' value="0" class='order_button' style="width:160px;">Order by Views &#9660;</button></li>
                                        <?php
                                        }
                                    ?>
                            </ul>
                        </form>
                <?php
                $im_num = 0;
                for($i = 0; $i < $num_rows; $i++){
                    echo "<tr>";
                    for ($k = 0; $k < 4; $k++){
                        if($im_num == $num_ims){
                            break;
                        }
                        $username = explode('/', $images[$im_num]['filepath'])[1];
                        $image_name = $images[$im_num]['title'];
                        $upload_date = explode(' ', $images[$im_num]['uploadtime'])[0];
                        $id = $images[$im_num]['mediaid'];
                        $views = get_views($id);
                        $len = 100 - strlen($username);
                        ?>
                        <td style='padding-left: 10px;'>
                            <div class='img_background'>
                                <a href="<?php if(is_own_media($id)){echo "own_media.php";}else{echo"media.php";}?>?id=<?php echo $images[$im_num]['mediaid']; ?>" target='_self'>  
                                    <img src="<?php echo $images[$im_num]['filepath'].$images[$im_num]['filename'];?>" class='home_img'></img>
                                </a>
                            </div>
                        <br>
                            <h3 class='src_title'><?php echo $image_name;?></h3>
                            <div>
                                <p class='src_name'>Views: <?php echo $views;?></p><br>
                                <a href='channel.php?user=<?php echo $username;?>' target='_self' class='src_name'>By: <?php echo $username;?> </a>
                                <p class='src_name' style='text-align: center; margin-left: <?php echo $len;?>'> Uploaded on: <?php echo $upload_date;?></p>
                            </div>
                        </td>
                        <?php $im_num++; ?>
                            <?php 
                    } ?>
                    </tr>
                    <?php
                }
            ?>
            <tr style='height:50px;'></tr>
        </table>
    </div>
</div>




</body>
</html>
