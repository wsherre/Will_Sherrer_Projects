<?php
session_start();
include_once "function.php";
include_once "mysqlClass.inc.php";
$database = new dbh();

/******************************************************
*
* download by username
*
*******************************************************/

$username=$_SESSION['username'];
$mediaid=$_REQUEST['id'];

//insert into upload table
$insertDownload="insert into download(username,mediaid) values(?, ?)";
$vals = [$username, $mediaid];
$database->insert($insertDownload, $vals);
	
?>


