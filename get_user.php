<?php
	session_start();
	if(!isset($_SESSION["email"])){
		header("Location:index.php");
	}
	if($_SESSION["usertype"] != 0){
		header("Location:admin_form.php");
	}
	include_once("connect.php");
	
	$result = $mysql_link->query("SELECT MAX(latest_upload) AS latest, count(*) AS plithos, email FROM data INNER JOIN users ON data.user_email = users.email WHERE email = '".$_SESSION["email"]."'");

	$row = $result->fetch_array();
	echo json_encode(array("latest"=> $row["latest"], "plithos" => $row["plithos"], "email" => $row["email"]));
?>