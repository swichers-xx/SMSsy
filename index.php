<?php
	//index.php - login page
$message = "";

if(isset($_GET['error'])) {
	$message = "<div id=\"error\"><i class=\"fas fa-exclamation\"></i> Username or password incorrect. Please try again.</div>";
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Luce Research SMS System - Login</title>
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
		<link href="./css/login.css" rel="stylesheet" type="text/css">
		<style>
			#error {
				color: red;
				text-align: center;
			}
		</style>
	</head>
	<body>
		<div class="header-logo">
			<img src="./layout/img/logo.svg">
		</div>
		<div class="login">
			<h1>SMS LOGIN</h1>
			<?php echo $message;?>

			<form action="./auth/authenticate.php" method="post" autocomplete="off">
				<label for="username">
					<i class="fas fa-user"></i>
				</label>
				<input type="text" name="username" placeholder="Username" id="username" required>
				<label for="password">
					<i class="fas fa-lock"></i>
				</label>
				<input type="password" name="password" placeholder="Password" id="password" required autocomplete="new-password">
				<input type="submit" value="Login">
			</form>
		</div>
	</body>
</html>