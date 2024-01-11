<?php 
	//deactivateconvo.php - removes conversation from screen
require '../auth/cookiecheck.php';
require '../auth/cred.php';

if(!isset($_GET['convoid'])) {
	echo "Error: Unable to find conversation. Return to the dashboard and try again.";
	die();
}
else {
	$convoid=$_GET['convoid'];
	$conn = new mysqli($servername, $username, $password, $dbname);
	$sql = "UPDATE conversations SET active=0 WHERE id=" .$convoid;

	$conn -> query($sql);

	header("Location: conversations.php");
}
?>