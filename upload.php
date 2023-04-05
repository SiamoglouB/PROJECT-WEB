<?php
	session_start();
	if(!isset($_SESSION["email"])){
		header("Location:index.php");
	}
	if($_SESSION["usertype"] != 0){
		header("Location:admin_form.php");
	}
	include_once("connect.php");
	$entries = array();
	$headers = array();
	$data = json_decode($_POST['entries']);
	//print_r($data);
	
	$result = $mysql_link->query("SELECT server_ip_adress,lat_server, longt_server FROM data");
	
	$foundAdresses = array();
	$ip = file_get_contents('https://api.ipify.org');
	$dat = file_get_contents('http://ip-api.com/json/'.$ip);
	$obj = json_decode($dat);
	while($row =$result->fetch_array()){
		$foundAdresses[$row["server_ip_adress"]] =  array("lat"=> $row["lat_server"], "longt" => $row["longt_server"]);
	}
	$query = "SELECT AUTO_INCREMENT as next_id
			FROM information_schema.TABLES
			WHERE TABLE_SCHEMA = 'web_summer2021'
			AND TABLE_NAME = 'data'";
	$result = $mysql_link->query($query);
	$row = $result->fetch_array();
	$entry_id = $row["next_id"];
	foreach($data as $entry){
		
		if(!empty($entry->requestHeaders)){
			$headers[] = getHeaderQuery($entry->requestHeaders, $entry_id, 0);
		}
		if(!empty($entry->responseHeaders)){
			$headers[] = getHeaderQuery($entry->responseHeaders, $entry_id, 1);
		}
		$timing = (!empty($entry->timing)) ? $entry->timing : 0;
		$started = date ('Y-m-d H:i:s', strtotime($entry->startedDateTime));
		$serverip = $entry->serverIPAddress;
		$serverIPAddress = (!empty($entry->serverIPAddress)) ? "'$serverip'" : "NULL";
		if($serverIPAddress != "NULL"){
			$newServerIp = rtrim($serverIPAddress, "]'");
			$newServerIp = ltrim($newServerIp, "'[");
			if(!isset($foundAdresses[$serverIPAddress])){
				if($newServerIp == "::1"){
					$foundAdresses[$serverIPAddress] =  array("lat"=> $obj->lat, "longt" => $obj->lon);
				}
				else{
					$loc2 = file_get_contents('https://api.ipgeolocation.io/ipgeo?apiKey=aa794bf278504b53beabd05c801cfc41&ip='.$newServerIp.'');
					$obj2 = json_decode($loc2);
					$foundAdresses[$serverIPAddress] =  array("lat"=> $obj2->latitude, "longt" => $obj2->longitude);
				}
			}
			$entries[] = "('".$_SESSION["email"]."', '".$started."', ".$serverIPAddress.", '".$entry->method."', '".$entry->url."', ".$entry->status.", '".$entry->statusText."', '".$obj->isp."', '".$obj->city."', '".date('Y-m-d H:i:s')."', ".$obj->lat.", ".$obj->lon.", ".$foundAdresses[$serverIPAddress]["lat"].", ".$foundAdresses[$serverIPAddress]["longt"].", ".$timing.")";
		}
		else{
			$entries[] = "('".$_SESSION["email"]."', '".$started."', ".$serverIPAddress.", '".$entry->method."', '".$entry->url."', ".$entry->status.", '".$entry->statusText."', '".$obj->isp."', '".$obj->city."', '".date('Y-m-d H:i:s')."', ".$obj->lat.", ".$obj->lon.", NULL, NULL, ".$timing.")";
		}
		$entry_id++;
	}
	if(!$mysql_link->query("insert into data (user_email, starteddatetime,server_ip_adress, request_method, request_url, response_status, status_text,provider, city, latest_upload, lat_user, longt_user, lat_server, longt_server,timing) values".implode(", ",$entries))){
		echo 2;
	}
	//print_r("insert into headers (data_id ,content_type, cache_control, pragma, expires, age, last_modified, host, header_type) values ".implode(", ",$headers));
	if(!$mysql_link->query("insert into headers (data_id ,content_type, cache_control, pragma, expires, age, last_modified, host, header_type) values ".implode(", ",$headers))){
		echo 3;
	}

	echo 1;

	
	

	function getHeaderQuery($header, $next_id, $type){
		$content_type = $header->content_type;
		$content_type = ($content_type != null) ? "'$content_type'" : "NULL";
		$age = ($header->age != null) ? $header->age : 0;
		$cache_control = $header->cache_control;
		$cache_control = ($cache_control != null) ? "'$cache_control'" : "NULL";
		$expires = $header->expires;
		$expires = ($expires != null && $expires != "0") ? date ('Y-m-d H:i:s', strtotime($expires)) : "NULL";
		if($expires != "NULL"){
			$expires = "'$expires'";
		}
		$host = $header->host;
		$host = ($host != null) ? "'$host'" : "NULL";
		$last_modified = $header->last_modified;
		$last_modified = ($last_modified != null) ? date ('Y-m-d H:i:s', strtotime($last_modified)) : "NULL";
		if($last_modified != "NULL"){
			$last_modified = "'$last_modified'";
		}
		$pragma = $header->pragma;
		$pragma = ($pragma != null) ? "'$pragma'" : "NULL";
		return "( ".$next_id.", ".$content_type.", ".$cache_control.", ".$pragma.", ".$expires.", ".$age.", ".$last_modified.", ".$host.", ".$type.")";
	}
?>