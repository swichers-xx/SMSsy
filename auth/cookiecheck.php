<?php
	//cookiecheck.php - verfies that proper cookies are in place
session_start();
if(!isset($_SESSION['loggedin'])) {
	header('Location: ../index.php');
	exit();
}
$navusergroup = $_SESSION['usergroup'];
$cookieuserid = $_SESSION['id'];
require __DIR__ .'/cred.php';

$conn = new mysqli($servername, $username, $password, $dbname);

$server_location = $_SERVER['REQUEST_URI'];
if(strpos($server_location, 'send.php')) {
	if(strpos($server_location, '=')) {
		$server_pid = substr($server_location,strpos($server_location,'=')+1);
		$check_project = "SELECT pid FROM logs WHERE id=".$_SESSION['logid'];
		$currentlog = $conn -> query($check_project);
		if($currentlog->num_rows>0) {
			foreach($currentlog as $cl) {
				$db_pid = $cl['pid'];
				if(intval($db_pid) == intval($server_pid)) {
					$update_log_sql = "UPDATE logs SET lastactivity=NOW() WHERE id=". $_SESSION['logid'];
					$conn -> query($update_log_sql);

				}
				else {
					$new_log_sql = "INSERT INTO logs (userid, login, lastactivity, pid) VALUES (${cookieuserid}, NOW(), NOW(), ${server_pid})";
					$new_log = $conn -> query($new_log_sql);
					$_SESSION['logid'] = $conn->insert_id;

				}
			}
		}
		else {
			//
		}
	}
}
$conn -> close();
?>