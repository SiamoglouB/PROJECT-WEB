<?php
	include_once("connect.php");
	$requestPayload = file_get_contents("php://input");
	$data = json_decode($requestPayload);
	$email = $data->email;
	$pass = md5($data->pass);
	$first = $data->first;
	$last = $data->last;
	if($mysql_link->query("INSERT INTO users(email, password, first_name, last_name) VALUES('".$email."', '".$pass."', '".$first."', '".$last."')")){
		session_start();
		$_SESSION["email"] = $email;
		$_SESSION["usertype"] = 0;
		echo 1;
	} 
	else{
		echo 0;
	}
?>