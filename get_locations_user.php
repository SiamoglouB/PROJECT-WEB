<?php
	session_start();
	if(!isset($_SESSION["email"])){
		header("Location:index.php");
	}
	if($_SESSION["usertype"] != 0){
		header("Location:admin_form.php");
	}
	include_once("connect.php");
	$result = $mysql_link->query("SELECT DISTINCT lat_server, longt_server , COUNT(*) AS plithos FROM data WHERE user_email = '".$_SESSION["email"]."' GROUP BY lat_server, longt_server");
	$data = array();
	while($row = $result->fetch_array()){
		$data[] = array("lat" => $row["lat_server"], "longt"=> $row["longt_server"], "plithos" => $row["plithos"]);
	}
	echo json_encode($data);
?>