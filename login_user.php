<?php
	include_once("connect.php");
	$requestPayload = file_get_contents("php://input");
	$data = json_decode($requestPayload);
	$email = $data->email;
	$pass = md5($data->pass);
	$result = $mysql_link->query("SELECT * FROM users WHERE email = '".$email."' AND password = '".$pass."'");
	if(mysqli_num_rows($result) != 0){
		$row = $result->fetch_array();
		session_start();
		$_SESSION["email"] = $row["email"];
		$_SESSION["usertype"] = $row["role"];
		echo $row["role"];
	}
	else{
		echo 2;
	}
?>