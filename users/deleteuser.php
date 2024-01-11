<?php
	//deleteuser.php - removes user's login from dB
require '../auth/cookiecheck.php';
require '../auth/interviewercheck.php';
require '../auth/cred.php';

if(!isset($_GET['userid'])){
	echo "ERROR: Unable to delete this user. Please try again.";
	die();
}
else {
	$conn = new mysqli($servername, $username, $password, $dbname);
	$userid = $conn -> real_escape_string($_GET['userid']);
	$sql = "DELETE FROM logins WHERE id=" .$userid;

	if($conn -> query($sql) ===true) {
		header('Location: users.php');
	}
	else {
		echo "ERROR: Unable to delete this user. Please try again.";
	}
}
?>