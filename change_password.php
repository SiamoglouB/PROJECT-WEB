<?php
	session_start();
	if(!isset($_SESSION["email"])){
		header("Location:index.php");
	}
	if($_SESSION["usertype"] != 0){
		header("Location:admin_form.php");
	}
	include_once("connect.php");
	$result = $mysql_link->query("UPDATE users SET password = '".md5($_POST['password'])."' WHERE email = '".$_SESSION["email"]."'");
	
	echo 1;
?>