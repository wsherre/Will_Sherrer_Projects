<?php
@ob_start();
session_start();
?>
<link rel="stylesheet" type="text/css" href="css/default.css" />
<script type="text/javascript" src="js/jquery-latest.pack.js"></script>
<title>Sign in</title>
<?php
include_once "function.php";

if(isset($_POST['submit'])) {
		if($_POST['username'] == "" || $_POST['password'] == "") {
			$login_error = "One or more fields are missing.";
		}
		else {
			$check = user_pass_check($_POST['username'],$_POST['password']); // Call functions from function.php
			if($check == 1) {
				$login_error = "User ".$_POST['username']." not found.";
			}
			elseif($check==2) {
				$login_error = "Incorrect password.";
			}
			else if(isset($_GET['id'])){
				$_SESSION['username']=$_POST['username'];
				$_SESSION['login'] = true;
				$login_error =  $_GET['id'];
				if(is_own_media($_GET['id'])){
					header('Location: own_media.php?id=' . $_GET['id']);
					exit();
				}else{
					header('Location: media.php?id=' . $_GET['id']);
					exit();
				}
			}
			else{
				$_SESSION['username']=$_POST['username']; //Set the $_SESSION['username']
				$_SESSION['login'] = true;
				header('Location: browse.php');
				exit();
			}
		}		
		
}
?>
	<div id="header"><h1>Sign In</h1></div>
	<div id='navbar'>
		<ul class='ul'>
			<li><button class="nav_button" onclick="back('index.php');">Home Page</button></li>
			<li><button class="nav_button" onclick="back('register.php');">Register</button></li>
		</ul>
	</div>
	<div id='inline'>
		<form method="post" action="">

			<table width="100%">
				<tr>
					<td  width="20%">Username:</td>
					<td width="80%"><input class="text"  type="text" name="username" placeholder='Username'><br /></td>
				</tr>
				<tr>
					<td  width="20%">Password:</td>
					<td width="80%"><input class="text"  type="password" name="password" placeholder='Password'><br /></td>
				</tr>
				<tr>
				
					<td>
						<input class="button" name="submit" type="submit" value="Sign in">
						<input class="button"name="reset" type="reset" value="Reset">
					</td>
				</tr>
			</table>
		</form>
	</div>

<?php
  if(isset($login_error))
   {  echo "<div id='passwd_result' style='margin-left: 15%;'>".$login_error."</div>";}
?>
