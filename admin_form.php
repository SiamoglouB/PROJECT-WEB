<!DOCTYPE html>
<?php
	session_start();
	if(!isset($_SESSION["email"])){
		header("Location:index.php");
	}
	if($_SESSION["usertype"] != 1){
		header("Location:user_form.php");
	}
?>
<html>
	<head>
		<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet"></link>
		<link href = "https://use.fontawesome.com/releases/v5.8.2/css/all.css" rel="stylesheet"></link>
		<link href = "user.css" rel="stylesheet"></link>
		<link href = "tables.css" rel="stylesheet"></link>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.bundle.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.bundle.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.css"></script>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.css"></link>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.slim.min.js"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
		<link rel="stylesheet" href="https://unpkg.com/leaflet@1.3.4/dist/leaflet.css"/>
		<script src="https://unpkg.com/leaflet@1.3.4/dist/leaflet.js"></script>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/0.4.2/leaflet.draw.css"/>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/0.4.2/leaflet.draw.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/heatmapjs@2.0.2/heatmap.js"> </script>
		<script src="https://raw.githubusercontent.com/pa7/heatmap.js/develop/plugins/leaflet-heatmap/leaflet-heatmap.js"></script>
		<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
		<script src="map.js"></script>
		<script>
			$(document).ready(function(){
				$.ajax({
					type: "post",
					url: "admin_onload.php",
					dataType: "json",
					success: function(response){
						$('#user_count').val(response.data1);
						$('#domains').val(response.data4);
						$('#isps').val(response.data5);
						set_table_method(response.data2);
						set_table_status(response.data3);
						set_table_content(response.data6);
					},
					error: function(response){
						console.log(response);
					}
				});
				
				$.ajax({
					type: "post",
					url: "data_for_dropdown.php",
					dataType: "json",
					success: function(response){
						response.content_type.forEach(function(type){
							$('#type').append($('<option/>', { 
								value: type,
								text: type
							}));
							$('#type2').append($('<option/>', { 
								value: type,
								text: type
							}));
						});
						response.method.forEach(function(method){
							$('#http_type').append($('<option/>', { 
								value: method,
								text: method
							}));
						});
						response.isp.forEach(function(isp){
							$('#isp').append($('<option/>', { 
								value: isp,
								text: isp
							}));
							$('#isp2').append($('<option/>', { 
								value: isp,
								text: isp
							}));
						});
					},
					error: function(response){
						console.log(response);
					}
				});
				
				var map = L.map('map');
				L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
					attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
				}).addTo(map);
				map.setView([38.2462420, 21.7350847], 1);
				$.ajax({
					type: 'POST',
					url: 'get_for_map_admin.php',
					success: function(response){
						var data = JSON.parse(response);
						
						var user_lat = data[0].user_lat;
						var user_longt = data[0].user_longt;
						var mark = L.marker ([user_lat, user_longt]);
						mark.addTo(map);
						for(var i = 0; i < data.length; i++){
							if(data[i].latitude != null && data[i].longitude != null){
								var marker2 = L.marker ([data[i].latitude, data[i].longitude]);
								marker2.addTo(map);
								var latlngs = Array();
								latlngs.push(mark.getLatLng());
								latlngs.push(marker2.getLatLng());
								var polyline = L.polyline(latlngs, {color: 'red', weight: data[i].count / 100}).addTo(map);
							}
						}
					}
				})
			});
			
			function set_table_method(data){
				$("#table_method tr").remove();
				var table_content = '<tr><th>Μέθοδος</th><th>Πλήθος</th></tr>';
				for(var i = 0; i < data.length; i++){
					table_content += '<tr><td>'+data[i]["method"]+'</td><td>'+data[i]["plithos"]+'</td></tr>';
					
				}
				$('#table_method').append(table_content);	
			}
			
			function set_table_status(data){
				$("#table_status tr").remove();
				var table_content = '<tr><th>Status</th><th>Πλήθος</th></tr>';
				for(var i = 0; i < data.length; i++){
					table_content += '<tr><td>'+data[i]["status"]+'</td><td>'+data[i]["plithos"]+'</td></tr>';
					
				}
				$('#table_status').append(table_content);	
			}
			
			function set_table_content(data){
				$("#table_content tr").remove();
				var table_content = '<tr><th>Content</th><th>Μέση ηλικία</th></tr>';
				for(var i = 0; i < data.length; i++){
					table_content += '<tr><td>'+data[i]["type"]+'</td><td>'+data[i]["avg_age"]+'</td></tr>';
					
				}
				$('#table_content').append(table_content);	
			}
			
			function search(){
				if(!$('#all_day').is(":checked")){
					var day_from = $("#d_from")[0].selectedIndex + 1;
					var day_to = $("#d_to")[0].selectedIndex + 1;
					if(day_from > day_to){
						var temp = day_from;
						day_from = day_to;
						day_to = temp;
					}
				}
				else{
					var day_from = -1;
					var day_to = -1;
				}
				
				var content = document.getElementById("type").selectedOptions;
				var all_content = [];
				if(content.length != 0){
					for(var i = 0; i < content.length; i++){	
						all_content.push(content[i].value);
					}
				}
				
				var method = document.getElementById("http_type").selectedOptions;
				var all_method = [];
				if(method.length != 0){
					for(var i = 0; i < method.length; i++){	
						all_method.push(method[i].value);
					}
				}
				
				var isp = document.getElementById("isp").selectedOptions;
				var all_isp = [];
				if(isp.length != 0){
					for(var i = 0; i < isp.length; i++){	
						all_isp.push(isp[i].value);
					}
				}
				
				$.ajax({
					type: 'POST',
					url: 'search_avg_time.php',
					data: {day_from: day_from, day_to: day_to, content: all_content, method: all_method, isp: all_isp},
					success: function(response){
						var response = JSON.parse(response);
						 $('#chart_container_search').html('');
						$('#chart_container_search').append('<canvas id="chart_search"></canvas>');
						var labels = [];
						var chart_data = [];
						var colors = [];
						for(i = 0; i < response.length; i++){
							console.log(response[i]);
							labels.push(response[i].hour);
							chart_data.push(response[i].average_time);
							colors.push('rgba('+parseInt((Math.random() * 255) +1)+', '+parseInt((Math.random() * 255) +1)+', '+parseInt((Math.random() * 255) +1)+', 0.4)');
						}
						var ctx = document.getElementById('chart_search').getContext('2d');
						var myChart = new Chart(ctx, {
							type: 'bar',
							data: {
								labels: labels,
								datasets: [{
									label: 'Αριθμός ',
									data: chart_data,
									backgroundColor: colors,
									borderWidth: 1
								}]
							}
						}); 
						$('#data_div').show();
					}
				});
			}
			
			function searchHttp(){
				var content = document.getElementById("type2").selectedOptions;
				var all_content = [];
				if(content.length != 0){
					for(var i = 0; i < content.length; i++){	
						all_content.push(content[i].value);
					}
				}
				
				var isp = document.getElementById("isp2").selectedOptions;
				var all_isp = [];
				if(isp.length != 0){
					for(var i = 0; i < isp.length; i++){	
						all_isp.push(isp[i].value);
					}
				}
				
				$.ajax({
					type: 'POST',
					url: 'search_http_admin.php',
					data: {content: all_content, isp: all_isp},
					success: function(response){
						var response = JSON.parse(response);
						set_table_http(response);
					}
				});
			}
			
			function set_table_http(data){
				$("#table_http tr").remove();
				var table_content = '<tr><th>Τύπος</th><th>Ποσοστό</th></tr>';
				for(var i = 0; i < data.length; i++){
					table_content += '<tr><td>'+data[i]["type"]+'</td><td>'+data[i]["pososto"]+'%</td></tr>';
					
				}
				$('#table_http').append(table_content);	
			}
		
		</script>
	</head>
	<body>
		<center><a href = "index.php">Έξοδος</a></center>
		<div class="container-fluid" >
			<hr>
			<div class = "row element">
				<form>
					<div class = "container">
						<div class = "row">
							<label>Απεικόνιση βασικών πληροφοριών</label>
						</div>
						<br>
						<div class = "row">
							<div class = "col-lg-5 col-lg-offset-2">
								<div class = "form-group">
									<label class = "control-label">Πλήθος εγγεγραμένων χρηστών</label> 
								</div>
							</div>
							<div class = "col-lg-5">
								<input type="text" id="user_count"  disabled>
							</div>
						</div>
						<br>
						<div class = "row">
							<div class = "col-lg-5 col-lg-offset-2">
								<div class = "form-group">
									<label class = "control-label">Πλήθος εγγραφών ανά τύπο</label> 
								</div>
							</div>
							<div class = "col-lg-5">
								<table id = "table_method"></table>
							</div>
						</div>
						<br>
						<div class = "row">
							<div class = "col-lg-5 col-lg-offset-2">
								<div class = "form-group">
									<label class = "control-label">Πλήθος εγγραφών ανά status</label> 
								</div>
							</div>
							<div class = "col-lg-5">
								<table id = "table_status"></table>
							</div>
						</div>
						<br>
						<div class = "row">
							<div class = "col-lg-5 col-lg-offset-2">
								<div class = "form-group">
									<label class = "control-label">Πλήθος μοναδικών domains</label> 
								</div>
							</div>
							<div class = "col-lg-5">
								<input type="text" id="domains"  disabled>
							</div>
						</div>
						<br>
						<div class = "row">
							<div class = "col-lg-5 col-lg-offset-2">
								<div class = "form-group">
									<label class = "control-label">Πλήθος μοναδικών παρόχων</label> 
								</div>
							</div>
							<div class = "col-lg-5">
								<input type="text" id="isps"  disabled>
							</div>
						</div>
						<br>
						<div class = "row">
							<div class = "col-lg-5 col-lg-offset-2">
								<div class = "form-group">
									<label class = "control-label">Μέση ηλικία ανά Content type</label> 
								</div>
							</div>
							<div class = "col-lg-5">
								<table id = "table_content"></table>
							</div>
						</div>
					</div>
					
				</form>
			</div>

			<hr>
			<div class = "row element">
				<form>
					<div class = "container">
						<div class = "row">
							<label>Ανάλυση χρόνων απόκρισης</label>
						</div>
						<br>
						<div class = "row">
							<div class = "col-lg-5 col-lg-offset-2">
								<div class = "form-group">
									<label class = "control-label">Είδος Ιστοαντικειμένου</label> 
								</div>
							</div>
							<div class = "col-lg-5">
								<div class = "form-group">
									<select multiple id = "type">
									</select>
								</div>
							</div>
						</div>
						<div class = "row">
							<div class = "col-4">
								<div class = "form-group">
									<label class = "control-label">Ημέρα Από</label> 
								</div>
							</div>
							<div class = "col-4">
								<div class = "form-group">
									<select id = "d_from">
										<option>Κυριακή</option>
										<option>Δευτέρα</option>
										<option>Τρίτη</option>
										<option>Τετάρτη</option>
										<option>Πέμπτη</option>
										<option>Παρασκευή</option>
										<option>Σάββατο</option>
									</select>
								</div>
							</div>
						</div>
						<div class = "row">
							<div class = "col-4">
								<div class = "form-group">
									<label class = "control-label">Ημέρα Έως</label> 
								</div>
							</div>
							<div class = "col-4">
								<div class = "form-group">
									<select id = "d_to">
										<option>Κυριακή</option>
										<option>Δευτέρα</option>
										<option>Τρίτη</option>
										<option>Τετάρτη</option>
										<option>Πέμπτη</option>
										<option>Παρασκευή</option>
										<option>Σάββατο</option>
									</select>
								</div>
							</div>
							<div class = "col-4">
								<div class = "form-group">
									<label><input type = "checkbox" id = "all_day">όλες οι ημέρες</label>
								</div>
							</div>
						</div>
						<div class = "row">
							<div class = "col-lg-5 col-lg-offset-2">
								<div class = "form-group">
									<label class = "control-label">Είδος HTTP μεθόδου</label> 
								</div>
							</div>
							<div class = "col-lg-5">
								<div class = "form-group">
									<select multiple id = "http_type">
									</select>
								</div>
							</div>
						</div>
						<div class = "row">
							<div class = "col-lg-5 col-lg-offset-2">
								<div class = "form-group">
									<label class = "control-label">Πάροχος συνδεσιμότητας</label> 
								</div>
							</div>
							<div class = "col-lg-5">
								<div class = "form-group">
									<select multiple id = "isp">
									</select>
								</div>
							</div>
						</div>
					<button type = "button" class = "col-lg-offset-2 btn btn-primary" onclick = "search()">Αναζήτηση</button>
				<div class = "row" style = "margin-top: 60px; margin-bottom: 60px" id="data_div">
					<div class = "col-lg-3">
						<label>Δεδομένα: </label>
					</div>
						<div class = "col-lg-9" id = "chart_container_search">
							<!-- <canvas id="chart_search"></canvas> -->
						</div>
				</div>  
				</form>
			</div>
 

		</div>
		
		<hr>
		
		<div class = "row element">
				<form>
					<div class = "container">
						<div class = "row">
							<label>Ανάλυση κεφαλίδων HTTP</label>
						</div>
						<br>
						<div class = "row">
							<div class = "col-lg-5 col-lg-offset-2">
								<div class = "form-group">
									<label class = "control-label">Είδος Ιστοαντικειμένου</label> 
								</div>
							</div>
							<div class = "col-lg-5">
								<div class = "form-group">
									<select multiple id = "type2">
									</select>
								</div>
							</div>
						</div>
						<div class = "row">
							<div class = "col-lg-5 col-lg-offset-2">
								<div class = "form-group">
									<label class = "control-label">Πάροχος συνδεσιμότητας</label> 
								</div>
							</div>
							<div class = "col-lg-5">
								<div class = "form-group">
									<select multiple id = "isp2">
									</select>
								</div>
							</div>
						</div>
					<button type = "button" class = "col-lg-offset-2 btn btn-primary" onclick = "searchHttp()">Αναζήτηση</button>
				</form>
				<div class = "row">
				<div class = "col-lg-5 col-lg-offset-2">
					<div class = "form-group">
						<label class = "control-label">Αποτέλεσμα</label> 
					</div>
				</div>
				<div class = "col-lg-5">
					<table id = "table_http"></table>
				</div>
			</div>
			</div>
 

		</div>
		
		<hr>
		<div class = "row element">
			<div id="map-container">
				<div id = "map"></div>
			</div>
		</div>
	</body>
</html>