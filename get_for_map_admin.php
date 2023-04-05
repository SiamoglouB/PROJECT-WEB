<?php
	session_start();
	if(!isset($_SESSION["email"])){
		header("Location:index.php");
	}
	if($_SESSION["usertype"] != 1){
		header("Location:user_form.php");
	}
	include_once("connect.php");
    $result = $mysql_link->query("SELECT DISTINCT lat_server, longt_server, lat_user, longt_user, COUNT(*) AS count FROM data GROUP BY lat_server, longt_server");
	$locations = array();

	while($row = $result->fetch_array()){
		$locations[] = array('latitude' => $row["lat_server"], 'longitude' => $row["longt_server"], 'count' => $row["count"], "user_lat" => $row["lat_user"], "user_longt" => $row["longt_user"]);
	}
	echo json_encode($locations);
?>