<?php
//unsubscribe.php
require '../auth/cookiecheck.php';
require './auth/cred.php';

if(!isset($_GET['convoid']) || !isset($_GET['respondentid']) || !isset($_GET['acuityid'])){
	echo "Error: Unable to unsubscribe respondent. Return to the conversation page and try again.";
	die();
}

$convoid = $_GET['convoid'];
$respondentid = $_GET['respondentid'];
$projectid = $_GET['acuityid'];

$conn = new mysqli($servername, $username, $password, $dbname);

$sql = "INSERT INTO unsubscribe (projectid, respondentid) VALUES ('" .$projectid ."', '" .$respondentid ."')"; 

if($conn -> query($sql) === true) {


    $url = "deactivateconvo.php?convoid=" .$convoid;
    header('Location: ' .$url);
}
else {
    echo "Error updating DB.";
}
?>