<?php
	//changepass.php - changes password in DB
require '../auth/cookiecheck.php';
require '../auth/interviewercheck.php';
require '../auth/cred.php';

if(!isset($_GET['userid']) || !isset($_GET['password'])) {
	echo "ERROR: Unable to change user's password. Please try again.";
	die();
}
else {
	$conn = new mysqli($servername, $username, $password, $dbname);
	$userid = $conn -> real_escape_string($_GET['userid']);
	$temppassword = $conn -> real_escape_string($_GET['password']);
	$password = password_hash($temppassword, PASSWORD_DEFAULT);

	$sql = "UPDATE logins SET password='".$password."' WHERE id=".$userid;
	if($conn -> query($sql) === true) {
		header('Location: users.php');
	}
	else {
		echo "Error: Unable to change user's password. Please try again";
	}
	$conn -> close();
}
?>