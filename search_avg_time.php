<?php
	session_start();
	if(!isset($_SESSION["email"])){
		header("Location:index.php");
	}
	if($_SESSION["usertype"] != 1){
		header("Location:user_form.php");
	}
	include_once("connect.php");
	$requestPayload = file_get_contents("php://input");
	$terms = [];
	
	if(($_POST["day_from"] != -1 && $_POST["day_to"] != -1)){
		//$time_terms .= " DAYOFWEEK(startedDateTime) >= ".$data->day_from." AND DAYOFWEEK(startedDateTime) <= ".$data->day_to.""; 
		$terms[] = " DAYOFWEEK(startedDateTime) >= ".$_POST["day_from"]." AND DAYOFWEEK(startedDateTime) <= ".$_POST["day_to"]."";
	}
	
	if(isset($_POST["content"])){
		for($i = 0; $i < count($_POST["content"]); $i++){
			$_POST["content"][$i] = "content_type = '".$_POST["content"][$i]."'";
		}
		$terms[] = implode(" OR ",$_POST["content"]);
	}
	
	
	if(isset($_POST["method"])){
		for($i = 0; $i < count($_POST["method"]); $i++){
			$_POST["method"][$i] = "request_method = '".$_POST["method"][$i]."'";
		}
		$terms[] = implode(" OR ",$_POST["method"]);
	}
	
	
	if(isset($_POST["isp"])){
		for($i = 0; $i < count($_POST["isp"]); $i++){
			$_POST["isp"][$i] = "provider = '".$_POST["isp"][$i]."'";
		}
		$terms[] = implode(" OR ",$_POST["isp"]);
	}
	
	
	$final = implode(" AND ", $terms);
	
	$query = "SELECT AVG(timing) AS average_time, HOUR(startedDateTime) AS hour
			  FROM data
			  INNER JOIN headers ON data.id = headers.data_id ";
			 
	if($final != null){
		$query .= "WHERE ".$final;
	}
	$query .= " GROUP BY HOUR(startedDateTime)" ;
    $result = $mysql_link->query($query);
	$data = array();

	while($row = $result->fetch_array()){
		$data[] = array('average_time' => $row["average_time"], 'hour' => $row["hour"]);
	}
	echo json_encode($data);
?>