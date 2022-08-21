<?php
@ob_start();
session_start();
include_once "mysqlClass.inc.php";
$database = new dbh();
$GLOBALS['d'] = $database;
?>

<link rel="stylesheet" type="text/css" href="css/default.css" />
<script type="text/javascript" src="js/jquery-latest.pack.js"></script>

<?php
    $sql = 'SELECT password, email FROM account WHERE username = ?';
    $val = [$_SESSION['username']];
    $GLOBALS['info'] = $GLOBALS['d']->query($sql, $val);
?>

<?php
    if (isset($_POST['submit'])){
        if ($pass = hash('ripemd160', $_POST['old_password']) != $GLOBALS['info'][0]['password']){
            $login_error = 'Old password does not match!';
        }
        else if ($_POST['password'] != $_POST['confirm_password']){
            $login_error = "Passwords do not match!";
        }
        else if (($_POST['password'] == "" and $_POST['confirm_password'] != "") or ($_POST['password'] != "" and $_POST['confirm_password'] == "")){
            $login_error = 'Must Enter both Passwords';
        }
        else if ($_POST['password'] == "" and $_POST['confirm_password'] == ""){
            $sql = 'UPDATE account SET email = ? WHERE username = ?';
            $val = [$_POST['email'], $_SESSION['username']];
            $GLOBALS['d']->insert($sql, $val);
            header('Location: browse.php');
            exit();
        }
        else{
            $sql = 'UPDATE account SET email = ?, password = ? WHERE username = ?';
            $pass = hash('ripemd160', $_POST['password']);
            $val = [$_POST['email'], $pass, $_SESSION['username']];
            $GLOBALS['d']->insert($sql, $val);
            header('Location: browse.php');
            exit();
        }
    }

    if(isset($_POST['back'])){
        header('Location: browse.php');
        exit();
    }
?>
<div id='header'>
<?php echo "<h1>Update Info for: " . $_SESSION['username'] , "</h1>"; ?>
</div>
<div id='navbar'>
    <ul class='ul'>
        <li><button class="nav_button" onclick="back('browse.php');">Go To Homepage</button></li>
    </ul>
</div>
<div id='inline'>
    <form method="post" action="update.php">

        <table width="100%">
            <tr>
                <td width="20%"> Old Password:</td>
                <td width="80%"><input class="text"  type="password" name="old_password" placeholder="Old Password"><br /></td>
            </tr>
            <tr>
                <td width="20%"> New Password:</td>
                <td width="80%"><input class="text"  type="password" name="password" placeholder="New Password"><br /></td>
            </tr>
            <tr>
                <td width="20%">Confirm New Password:</td>
                <td width="80%"><input class="text"  type="password" name="confirm_password" placeholder="Confirm Password"><br /></td>
            </tr>
            <tr>
                <td width="20%">Email:</td>
                <td width="80%"><input class="text"  type="text" name="email" value="<?php echo $GLOBALS['info'][0]['email'];?>"><br /></td>
            </tr>
            <tr>
                <td>
                    <input class="button"name="submit" type="submit" value="Update">
                    <button class="button"name="reset" type="submit">Reset</button>
                    <br />
                </td>
            </tr>  
        </table>
    </form>
</div>

<?php
  if(isset($login_error))
   {  echo "<div id='passwd_result'>".$login_error."</div>";}
?>