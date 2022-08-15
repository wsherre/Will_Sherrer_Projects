<?php
@ob_start();
session_start();
include_once "mysqlClass.inc.php";
$database = new dbh();
$GLOBALS['d'] = $database;
?>
<link rel="stylesheet" type="text/css" href="css/default.css" />
<script type="text/javascript" src="js/jquery-latest.pack.js"></script>
<title>Register</title>
<?php

if(isset($_POST['submit'])) {
    $sql = 'SELECT username FROM account';
    $stmt = $GLOBALS['d']->query($sql);
    $exist = 0;

    foreach ($stmt as $s){
        if($_POST['username'] == $s['username']){
            $exist = 1;
            break;
        }
    }

    if($_POST['username'] == "" || $_POST['password'] == "" || $_POST['confirm_password'] == "") {
        $login_error = "One or more fields are missing.";
    }
    else if($_POST['password'] != $_POST['confirm_password']){
        $login_error = "Passwords do not match!";
    }
    else if($exist){
        $login_error = "This username already exists!";
    }
    else if(!(strpos($_POST['email'], '@'))){
        $login_error = "Not a Valid Email!";
    }
    else{
        $sql = 'INSERT INTO account VALUES (?, ?, ?, 1)';
        $pass = hash('ripemd160', $_POST['password']);
        $var = [$_POST['username'], $pass, $_POST['email']];
        $database->insert($sql, $var);
        $_SESSION['username']=$_POST['username']; //Set the $_SESSION['username']
        $_SESSION['login'] = true;
		header('Location: browse.php');
		exit();
    }

}

if(isset($_POST['reset'])){
    $_POST['username'] = "";
    $_POST['email'] = "";
}

?>
<div id="header"><h1>Register</h1></div>
<div id='navbar'>
	<ul class='ul'>
		<li><button class="nav_button" onclick="back('index.php');">Home Page</button></li>
		<li><button class="nav_button" onclick="back('login.php');">Log In</button></li>
	</ul>
</div>
<div id='inline'>
    <?php
    if(isset($login_error))
        {  echo "<div id='passwd_result'>".$login_error."</div>";}
    ?>
    <form method="post" action="register.php">

        <table width="100%">
            <tr>
                <td width="20%">Username:</td>
                <td width="80%"><input class="text"  type="text" name="username" value="<?php if (isset($_POST['username'])) {echo $_POST['username'];} else{ echo "";}?>" placeholder='Username'><br /></td>
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
                <td width="80%"><input class="text"  type="text" name="email" value="<?php if(isset($_POST['email'])) {echo $_POST['email'];} else{echo "";}?>" placeholder='Email'><br /></td>
            </tr>
            <tr>
                <td>
                    <input class="button" name="submit" type="submit" value="Register">
                    <button class="button" name="reset" type="submit">Reset</button>
                    <br/>
                </td>
            </tr>  
        </table>
    </form>
</div>