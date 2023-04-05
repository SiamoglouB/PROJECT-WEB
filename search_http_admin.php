<?php
	session_start();
	if(!isset($_SESSION["email"])){
		header("Location:index.php");
	}
	if($_SESSION["usertype"] != 1){
		header("Location:user_form.php");
	}
	include_once("connect.php");
	$terms = [];
	
	
	if(isset($_POST["content"])){
		for($i = 0; $i < count($_POST["content"]); $i++){
			$_POST["content"][$i] = "content_type = '".$_POST["content"][$i]."'";
		}
		$terms[] = implode(" OR ",$_POST["content"]);
	}
	
	
	if(isset($_POST["isp"])){
		for($i = 0; $i < count($_POST["isp"]); $i++){
			$_POST["isp"][$i] = "provider = '".$_POST["isp"][$i]."'";
		}
		$terms[] = implode(" OR ",$_POST["isp"]);
	}
	
	
	
	$final = implode(" AND ", $terms);
	
	if($final != null){
		$query = "SELECT COUNT(*) AS plithos, content_type FROM headers INNER JOIN data on headers.data_id = data.id WHERE ".$final." GROUP BY content_type";
	}
	else{
		$query = "SELECT COUNT(*) AS plithos, content_type FROM headers INNER JOIN data on headers.data_id = data.id GROUP BY content_type";
	}
	
	$result = $mysql_link->query($query);
	$all = array();
	while($row = $result->fetch_assoc()){
        $all[$row["content_type"]] = $row["plithos"];
    }
			 
	if($final != null){
		$sql = "SELECT COUNT(*) AS plithos, content_type FROM headers INNER JOIN data on headers.data_id = data.id WHERE ".$final." AND (cache_control LIKE '%public%' OR cache_control LIKE '%private%' OR cache_control LIKE '%no-cache%' OR cache_control LIKE '%no-store%') GROUP BY content_type";
	}
	else{
		$sql = "SELECT COUNT(*) AS plithos, content_type FROM headers INNER JOIN data on headers.data_id = data.id WHERE cache_control LIKE '%public%' OR cache_control LIKE '%private%' OR cache_control LIKE '%no-cache%' OR cache_control LIKE '%no-store%' GROUP BY content_type";
	}
	$result2 = $mysql_link->query($sql);
	$pososto = array();
	while($row2 = $result2->fetch_assoc()){
		$pososto[] = array("pososto" => $row2["plithos"] / $all[$row2["content_type"]] * 100, "type" => $row2["content_type"]);
	}
	echo json_encode($pososto);
?>