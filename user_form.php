<!DOCTYPE html>
<?php
	session_start();
	if(!isset($_SESSION["email"])){
		header("Location:index.php");
	}
	if($_SESSION["usertype"] != 0){
		header("Location:admin_form.php");
	}
?>
<html>
	<head>
		<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet"></link>
		<link href = "https://use.fontawesome.com/releases/v5.8.2/css/all.css" rel="stylesheet"></link>
		<link href = "user.css" rel="stylesheet"></link>
		<link href = "tables.css" rel="stylesheet"></link>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.slim.min.js"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
		<link rel="stylesheet" href="https://unpkg.com/leaflet@1.3.4/dist/leaflet.css"/>
		<script src="https://unpkg.com/leaflet@1.3.4/dist/leaflet.js"></script>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/0.4.2/leaflet.draw.css"/>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/0.4.2/leaflet.draw.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/heatmapjs@2.0.2/heatmap.js"> </script>
		<script src="https://raw.githubusercontent.com/pa7/heatmap.js/develop/plugins/leaflet-heatmap/leaflet-heatmap.js"></script>
		<script src="map.js"></script>
		<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
		<script>
			var allEntries = [];
			$(document).ready(function(){
				$.ajax({
						type: "post",
						url: "get_user.php",
						dataType: "json",
						success: function(response){
							console.log(response);
							$('#latest').val(response.latest);
							$('#count').val(response.plithos);
							$('#username').val(response.email);
						},
						error: function(response){
							console.log(response);
						}
				});
				create_map();
			});
			function file_upload(){
				var fileInput = document.getElementById("har_file"); 
				if(fileInput.files.length == 0 ){
					alert("Παρακαλώ επιλέξτε ένα αρχείο");
				}
				else{
					var reader = new FileReader();
					var data = [];
					var form = new FormData();
					var json = "";
					reader.onload = function (evt) {
						json = evt.target.result;
					}
					reader.onloadend= function(){
						var result = JSON.parse(json); // Parse the result into an object 
						
						result.log.entries.forEach(function(entry){

							var newEntry = {
								startedDateTime: entry.startedDateTime, 
								timing: entry.timings.wait, 
								serverIPAddress: entry.serverIPAddress, 
								method: entry.request.method,
								url: entry.request.url.replace('http://','').replace('https://','').split(/[/?#]/)[0],
								status:  entry.response.status,
								statusText: entry.response.statusText, 
								requestHeaders: makeHeaders(entry.request.headers), 
								responseHeaders:makeHeaders(entry.response.headers)
							}
							allEntries.push(newEntry);
						});

						var data_server = new FormData();
						data_server.append('entries', JSON.stringify(allEntries));
						console.log(data_server);
						//var req_headers_server = makeHeaders();
						$.ajax({
							type: "post",
							url: "upload.php",
							dataType: "json",
							data: data_server,
							cache: false,
							contentType: false,
							processData: false,
							success: function(response){
								console.log(response);
								if(response == "1"){
									alert("Επιτυχής εισαγωγή αρχείου");
									location.reload();
								 }
								
							},
							error: function(response){
								console.log(response);
							}
						});
					};

					
					reader.readAsText(fileInput.files[0], "UTF-8");
				}
			}

			function makeHeaders(data){
				var headers = {content_type: null, cache_control: null, pragma: null, expires: null, last_modified: null, host: null, age: null};
				var found = false;
				data.forEach(function(header){
					
					if(header.name == "content-type"){
						headers.content_type = header.value;
						
					}
					if(header.name == "cache-control"){
						headers.cache_control = header.value;
						found = true;
					}
					if(header.name == "pragma"){
						headers.pragma = header.value;
						found = true;
					}
					if(header.name == "expires"){
						headers.expires = header.value;
						found = true;
					}
					if(header.name == "last-modified"){
						headers.last_modified = header.value;
						found = true;
					}
					if(header.name == "host"){
						headers.host = header.value;
						found = true;
					} 
					if(header.name == "age"){
						headers.age = header.value;
						found = true;
					} 
				});
				if(!found){
					return null;
				}
				else{
					return headers;
				}

			}

			function changePassword(){
				var password = $('#inputPassword').val();
				if(password == ''){
					alert("Παρακαλώ δώστε έναν κωδικός πρόσβασης");
				}
				else{
					$.ajax({
						type: "post",
						url: "change_password.php",
						dataType: "json",
						data: {password: password},
						success: function(response){
							if(response == 1){
								alert("Επιτυχής αλλαγή κωδικού πρόσβασης");
							}
							
						},
						error: function(response){
							console.log(response);
						}
					});
				}
			}
			
			function create_map(){
				$.ajax({
					type: "post",
					url: "get_locations_user.php",
					dataType: "json",
					success: function(response){
						document.getElementById("map-container").innerHTML = "";
						$('<div id = "map"></div>').appendTo($("#map-container"));
						var map = L.map('map');
						L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
							attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
						}).addTo(map);
						map.setView([38.2462420, 21.7350847], 1);
						var drawnItems = new L.FeatureGroup();
						map.addLayer(drawnItems); 
						var mapData = {
							max: 10,
							data: response
						};
						console.log(mapData);
						var cfg = {
							"radius": 40,
							"max_opacity": 0.8,
							"scaleRadius": false,
							"useLocalExtrema": false,
							latField: 'lat',
							lngField: 'longt',
							valueField: 'c'
						};
						var heatmapLayer = new HeatmapOverlay(cfg);
						map.addLayer(heatmapLayer);
						heatmapLayer.setData(mapData);
					},
					error: function(response){
						console.log(response);
					}
				});
			}
		
	</script>
	</head>
	<body>
		<div class="container-fluid" >
			<hr>
			<div class="row">
				<div class = "col-lg-3">
					<label>Upload Δεδομένων</label>
				</div>
				<div class = "col-lg-9">
				<form>
					<div class="form-group">
						<input type="file" id="har_file" name="avatar" accept="application/HAR" class = "form-control">
					</div>
					<button type="button" class="btn btn-primary" onclick= "file_upload()">Ανέβασμα δεδομένων</button>

				</form>
				</div>
			</div>
			<hr>
			<div class = "row element">
				<form>
					<div class = "container">
						<div class = "row">
							<label>Διαχείριση Προφιλ</label>
						</div>
						<br>
						<div class = "row">
							<div class = "col-lg-5 col-lg-offset-2">
								<div class = "form-group">
									<label class = "control-label">Ημερομηνία τελευταίου Upload</label> 
								</div>
							</div>
							<div class = "col-lg-5">
								<input type="text" id="latest"  disabled>
							</div>
						</div>
						<div class = "row">
							<div class = "col-lg-5 col-lg-offset-2">
								<div class = "form-group">
									<label class = "control-label">Πλήθος εγγραφών</label> 
								</div>
							</div>
							<div class = "col-lg-5">
								<input type="text" id="count"  disabled>
							</div>
						</div>
						<div class = "row">
							<div class = "col-lg-5 col-lg-offset-2">
								<div class = "form-group">
									<label class = "control-label">username</label> 
								</div>
							</div>
							<div class = "col-lg-5">
								<input type="text" id="username"  disabled>
							</div>
						</div>
						<div class = "row">
							<div class = "col-lg-5 col-lg-offset-2">
								<div class = "form-group">
									<label class = "control-label">Νέος κωδικός</label> 
								</div>
							</div>
							<div class = "col-lg-5">
								<input type="password" id="inputPassword" pattern="(?=^.{8,}$)(?=.*[0-9])(?=.*[A-Z])(?=.*[^A-Za-z0-9]).*" class="form-control" placeholder="Κωδικός Πρόσβασης" required>
							</div>
						</div>
					</div>
					<button type = "button" class = "col-lg-offset-2 btn btn-primary" onclick = "changePassword()">Αλλαγή</button>
				</form>
			</div>

			<hr>
			
			<div class = "row element">
				<div id="map-container">
					<div id = "map"></div>
				</div>
			</div>
		</div>
	</body>
</html>