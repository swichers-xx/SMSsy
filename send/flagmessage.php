<?php
require '../auth/cookiecheck.php';
require '../auth/cred.php';

if(!isset($_GET["respondentid"]) || !isset($_GET["projectid"])) {
	echo "Unable to flag record for review. Please return to the dashboard and try again.";
	die();
}
else {
    $respondentid = $_GET['respondentid'];
    $projectid = $_GET['projectid'];

    $conn = new mysqli($servername, $username, $password, $dbname);
    $checksql = "UPDATE sample SET FLAG=2 WHERE id=" .$respondentid;
    $checkResults = $conn -> query($checksql);
    $conn ->close();
    $redirectURL = "send.php?projectid=" .$projectid;
    header("Location: " .$redirectURL);
    die();
}
?>