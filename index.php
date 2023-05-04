<!DOCTYPE html>
<html>
	<head>
		<meta name="google-site-verification" content="a_f1yuh-I95l9TsU_PU-VIlfnzB7OKSt6UMQOUp_jTU" />
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet"></link>
		<link href = "https://use.fontawesome.com/releases/v5.8.2/css/all.css" rel="stylesheet"></link>
		<link href = "form.css" rel="stylesheet"></link>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.slim.min.js"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
		<script>
			function registerUserForm(){
				location.href = "register.php";
			}
			
			function loginUser(){
				var email = $('#inputEmail').val();
				var pass = $('#inputPassword').val();
				var data = {email: email, pass: pass};
				const jsonData = JSON.stringify(data);
				var xhttp = new XMLHttpRequest();
				xhttp.onreadystatechange = function(){
					if(this.readyState == 4 && this.status == 200){
						if(this.responseText == 0){
							console.log(document.cookie);
							location.href = "user_form.php";
						}
						else if(this.responseText == 1){
							location.href = "admin_form.php";
						}
						else{
							alert("Λάθος όνομα χρήστη ή κωδικός πρόσβασης");
							
						}
					}
				}
				xhttp.open("POST", "login_user.php");
				xhttp.setRequestHeader("Content-Type", "application/json");
				console.log(jsonData);
				xhttp.send(jsonData);
			}
		</script>
	</head>
	<body>
		<div class="container">
			<div class="row">
				<div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
					<div class="card card-signin my-5">
						<div class="card-body">
							<h5 class="card-title text-center">Είσοδος</h5>
							<form class="form-signin" onsubmit="loginUser(); return false;">
								<div class="form-label-group">
									<input type="email" id="inputEmail" class="form-control" placeholder="Email" required autofocus>
									<label for="inputEmail">Email</label>
								</div>
								<div class="form-label-group">
									<input type="password" id="inputPassword" class="form-control" placeholder="Κωδικός Πρόσβασης" required>
									<label for="inputPassword">Κωδικός Πρόσβασης</label>
								</div>
								<div class="custom-control custom-checkbox mb-3">
									<a href = "#" onclick = "registerUserForm()">Εγγραφή Χρήστη</a>
								</div>
								<button class="btn btn-lg btn-primary btn-block text-uppercase" type="sumbit" >Είσοδος</button>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>
