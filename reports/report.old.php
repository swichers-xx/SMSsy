<?php 
// report.php
// created by: Kristen
// created on: june 15 2020

require __DIR__ .'/cookiecheck.php';
require __DIR__ .'/interviewercheck.php';
require __DIR__ .'/cred.php';


function getUserName($userid,$servername, $username, $password, $dbname) {
	$conn = new mysqli($servername, $username, $password, $dbname);
	$sql = "SELECT username FROM logins WHERE id=".$userid;
	$usernameresult = $conn -> query($sql);
	$conn -> close();
	foreach($usernameresult as $user) {
		$username = $user['username'];
	}
	return $username;
	
}

//get all active projects
$conn = new mysqli($servername, $username, $password, $dbname);
$sql = "SELECT * FROM projectinfo WHERE active=1";
$allactive = $conn -> query($sql);
$output = "";
if($allactive -> num_rows>0) {
	foreach($allactive as $activeproject) {
		//$output .= "<table><tr><th>".$activeproject['id'] . "</th><th> " .$activeproject['projectname'] . "</th></tr>";
		$output .= "<table><tr><th colspan=\"2\"> " .$activeproject['projectname'] . "</th></tr>";
		$sql2 = "SELECT * from smsmessage WHERE projectid=" .$activeproject['id'];
		$allmessagesent = $conn -> query($sql2);
		$counter = 0;
		$interviewers = array();
		if($allmessagesent -> num_rows>0){
			foreach($allmessagesent as $sentmessage) {
				//$output .= " -- " .$sentmessage['id'] . "<br> --- Message Sent " . $sentmessage['load_date'] . " by userid=" .$sentmessage['userid']."<br>";
				$counter++;
				array_push($interviewers,$sentmessage['userid']);
			}
			$output .= "<tr><td>&nbsp;</td><td class=\"total\">" .$counter ." messages sent overall</td></tr>";
			$interviewersent = array_count_values($interviewers);
			while (list ($key, $val) = each ($interviewersent)) {
				$output .= "<tr><td>&nbsp;</td><td>" .getUserName($key,$servername, $username, $password, $dbname) . " sent " .$val . " messages</td></tr>";
			}
			$output .= "</table>";
		}
		else {
			$output .= "-- No messages sent <br>";
		}
	}
}
else {
	$output = "No active projects found";
}

?>

<!DOCTYPE html>
<html>
	<head>
	<title>Luce Research SMS System - Report</title>
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
	<link href="https://www.luceresearch.com/twilio/style.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="jquery.js"></script>
	<script type="text/javascript">
		
		$(document).ready(function (){
			//blank for time being

		});
</script>
<style>
.sandbox {
	display: flex;
	flex-direction: row;
}
#formhold {
	width: 500px;
}

#pipeinInfo {
	max-width: 400px;
	margin-left: 30px;
	padding-left: 10px;
	border-left: 5px solid lightgrey;
}
th {
	border-bottom: 1px dashed lightslategrey;
}
.total {
	color: darkslateblue;
	font-style: italic;
}
</style>
</head>
<body class="loggedin">
	<?php include 'topnav.php';?>
	<div class="content">
		<h2>Report</h2>
		<?php echo $output;?>		
</div>
</body>
</html>