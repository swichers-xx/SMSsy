<?php
	//createuser.php - updates dB to include new user information

require '../auth/cookiecheck.php';
require '../auth/interviewercheck.php';
require '../auth/cred.php';

if(!isset($_GET['username']) || !isset($_GET['password']) || !isset($_GET['usergroup'])) {
	echo "ERROR: Unable to create the user. Please try again.";
}
else {
	$conn = new mysqli($servername, $username, $password, $dbname);
	$username = $conn -> real_escape_string($_GET['username']);
	$temppassword = $conn -> real_escape_string($_GET['password']);
	$password = password_hash($temppassword, PASSWORD_DEFAULT);
	$user_group = 0;

	if($_GET['usergroup'] == 'manager') {
		$user_group = 1;
	}
	else {
		$user_group = 2;
	}

	$sql = "INSERT INTO logins (username, password, user_group) VALUES ('".$username."', '".$password."', ".$user_group.")";
	if($conn -> query($sql) === true) {
			header('Location: users.php');
		}
		else {
			echo "ERROR: " .$conn -> error;
		}

}
?>