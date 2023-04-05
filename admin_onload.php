<?php
	session_start();
	if(!isset($_SESSION["email"])){
		header("Location:index.php");
	}
	if($_SESSION["usertype"] != 1){
		header("Location:user_form.php");
	}
	include_once("connect.php");
	$data2 = array();
	$data3 = array();
	$result = $mysql_link->query("SELECT COUNT(*) AS plithos FROM users");
	$row = $result->fetch_array();
	$data1 = $row["plithos"];
	
	$result = $mysql_link->query("SELECT COUNT(*) AS plithos, request_method FROM data GROUP BY request_method");
	while($row = $result->fetch_array()){
		$data2[] = array("method" => $row["request_method"], "plithos" => $row["plithos"]);
	}
	
	$result = $mysql_link->query("SELECT COUNT(*) AS plithos, response_status FROM data GROUP BY response_status");
	while($row = $result->fetch_array()){
		$data3[] = array("status" => $row["response_status"], "plithos" => $row["plithos"]);
	} 
	
	$result = $mysql_link->query("SELECT COUNT(DISTINCT request_url)AS unique_url FROM data");
	$row = $result->fetch_array();
	$data4 = $row["unique_url"];
	
	$result = $mysql_link->query("SELECT COUNT(DISTINCT provider)AS unique_isp FROM data");
	$row = $result->fetch_array();
	$data5 = $row["unique_isp"];
	
	$result = $mysql_link->query("SELECT AVG(age)AS avg_age, content_type FROM headers GROUP BY content_type");
	while($row = $result->fetch_array()){
		$data6[] = array("avg_age" => $row["avg_age"], "type" => $row["content_type"]);
	} 
	
	echo json_encode(array("data1" => $data1, "data2" => $data2, "data3" => $data3, "data4" => $data4, "data5" => $data5, "data6" => $data6));
?>