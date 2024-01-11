<?php
	//status.php - captures message status and places into smsmessage db
require '../auth/cred.php';
$sid = $_REQUEST['MessageSid'];
$status = $_REQUEST['MessageStatus'];

if(isset($sid) && isset($status)){

$conn = new mysqli($servername, $username, $password, $dbname);
$sql = "UPDATE smsmessage SET status='" .$status."' WHERE messageid='" .$sid."'";

$conn -> query($sql);
}

return true;
?>