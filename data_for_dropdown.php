<?php
	session_start();
	if(!isset($_SESSION["email"])){
		header("Location:index.php");
	}
	if($_SESSION["usertype"] != 1){
		header("Location:user_form.php");
	}
	include_once("connect.php");
	$content_type = [];
	$method = [];
	$isp = [];
	$result = $mysql_link->query("SELECT DISTINCT content_type FROM headers");
	while($row = $result->fetch_array()){
		$content_type[] = $row["content_type"];
	}
	
	$result = $mysql_link->query("SELECT DISTINCT request_method FROM data");
	while($row = $result->fetch_array()){
		$method[] = $row["request_method"];
	}
	
	$result = $mysql_link->query("SELECT DISTINCT provider FROM data");
	while($row = $result->fetch_array()){
		$isp[] = $row["provider"];
	}
	echo json_encode(array("content_type" => $content_type, "method" => $method, "isp" => $isp));
?>