<?php
	//users.php - list of all users
require '../auth/cookiecheck.php';
require '../auth/interviewercheck.php';
require '../auth/cred.php';

$conn = new mysqli($servername, $username, $password, $dbname);
$sql = "SELECT * from logins";
$results = $conn ->query($sql);
$output = "";
$currentuserid = $_SESSION['id'];

function usergroup($usergrp) {
	if($usergrp==1) {
		return "Manager";
	}
	else {
		return "Interviewer";
	}
}

function deleteCheck($uid, $cid) {
	if($uid != $cid) {
		return "<a href=\"deleteuser.php?userid=".$uid."\"><i class=\"fas fa-times-circle\"></a>";
	}
	else {		
		return " - ";
	}
}

if($results -> num_rows >0){
	foreach($results as $result){
		$output .= "<tr><td>" .$result['username']."</td><td>".usergroup($result['user_group'])."</td><td><a href=\"changepw.php?userid=".$result['id']."\"><i class=\"fas fa-edit\"></a></td><td>".deleteCheck($result['id'] , $currentuserid)."</td></tr>";
	}
}
else {
	$output = "<tr><td colspan=\"5\">No users found</td></tr>";
}
$output .="<tr class=\"user-table-add\"><td colspan=\"5\"><a href=\"adduser.php\"><i class=\"fas fa-plus-circle\"></i> Create new user</a></td></tr>";
$conn -> close();
?>
<!DOCTYPE html>
<html>
	<head>
	<title>Luce Research SMS System - Users</title>
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
	<link href="../css/style.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="../js/jquery.js"></script>
	<style>
		
	</style>
</head>
<body class="loggedin">
	<?php include '../layout/topnav.php';?>
	<div class="content">
		<h2>Users</h2>
		<table id="activeProjects">
			<thead>
				<tr><th style="width: 200px;">Username</th><th style="width: 200px;">User Group</th><th style="width: 100px;">Change Password</th><th style="width: 150px;">Delete User</th></tr>
			</thead>
			<tbody>
			<?php echo $output;?>
			</tbody>
		</table>
</body>
</html>