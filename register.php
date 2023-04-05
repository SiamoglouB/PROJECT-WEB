<!DOCTYPE html>
<html>
	<head>
		<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet"></link>
		<link href = "https://use.fontawesome.com/releases/v5.8.2/css/all.css" rel="stylesheet"></link>
		<link href = "form.css" rel="stylesheet"></link>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.slim.min.js"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
		<script>
			//$('#reg_form').submit(function(e){
			function submitRegisterForm(){
				var email = $('#inputEmail').val();
				var pass = $('#inputPassword').val();
				var first = $('#inputFirst').val();
				var last = $('#inputLast').val();
				var data = {email: email, pass: pass, first: first,last: last};
				const jsonData = JSON.stringify(data);
				var xhttp = new XMLHttpRequest();
				xhttp.onreadystatechange = function(){
					if(this.readyState == 4 && this.status == 200){
						if(this.responseText == 1){
							alert("Επιτυχής εγγραφή");
							location.href = "user_form.php";
						}
						else{
							alert("Υπήρξε ένα μη αναμενόμενο πρόβλημα με την εγγραφή");
							location.href = "user_form.php";
						}
					}
				}
				xhttp.open("POST", "reg_user.php");
				xhttp.setRequestHeader("Content-Type", "application/json");
				xhttp.send(jsonData);
			}
			//});
		</script>
	</head>
	<body>
		<div class="container">
			<div class="row">
				<div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
					<div class="card card-signin my-5">
						<div class="card-body">
							<h5 class="card-title text-center">Εγγραφή Χρήστη</h5>
							<form id = "reg_form" class="form-signin"  onsubmit="submitRegisterForm(); return false;">
								<div class="form-label-group">
									<input type="email" id="inputEmail" class="form-control" placeholder="Email" required autofocus>
									<label for="inputEmail">Email</label>
								</div>
								<div class="form-label-group">
									<input type="password" id="inputPassword" pattern="(?=^.{8,}$)(?=.*[0-9])(?=.*[A-Z])(?=.*[^A-Za-z0-9]).*" class="form-control" placeholder="Κωδικός Πρόσβασης" required>
									<label for="inputPassword">Κωδικός Πρόσβασης</label>
								</div>
								<div class="form-label-group">
									<input type="text" id="inputFirst" class="form-control" placeholder="Όνομα" required>
									<label for="inputFirst">Όνομα</label>
								</div>
								<div class="form-label-group">
									<input type="text" id="inputLast" class="form-control" placeholder="Επώνυμο" required>
									<label for="inputLast">Επώνυμο</label>
								</div>
								<button class="btn btn-lg btn-primary btn-block text-uppercase" type="submit">Εγγραφη</button>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>