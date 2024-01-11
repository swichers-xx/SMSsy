<?php
	//adduser.php - creates a new user

require  '../auth/cookiecheck.php';
require  '../auth/interviewercheck.php';
require  '../auth/cred.php';

$conn = new mysqli($servername, $username, $password, $dbname);
$sql = "SELECT username FROM logins";
$results = $conn -> query($sql);
$allusers ="";

if($results -> num_rows >0) {
	foreach($results as $result){
		$allusers .= $result['username'] ."~~";
	}
}
else {
	$allusers="";
}
$conn -> close();
?>
<!DOCTYPE html>
<html>
	<head>
	<title>Luce Research SMS System - Add User</title>
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
	<link href="../css/style.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="../js/jquery.js"></script>
	<script type="text/javascript">
		$(document).ready(function(){
			usernameFlag = 0;
			passwordFlag = 0;
			usergroupFlag = 0;

			function validation(username, password, usergroup) {
				if(username==1 && password==1 && usergroup==1) {
					$("#submitButton").prop('disabled', false);
				}
				else {
					$("#submitButton").prop('disabled', true);
				}
			}
			//prevents submitting on enter
			$(window).keydown(function(event){
				if(event.keyCode == 13){
					event.preventDefault();
					return false;
				}
			});

			$("#password, #password2").keyup(function(){
				firstpassword = $("#password").val().trim();
				secondpassword = $("#password2").val().trim();

				if(firstpassword == "" || secondpassword==""){
					//one of the password fields is empty and hasn't been filled out yet - do a check for the number of characters
					passwordFlag = 0;
					$("#passwordError").html("&nbsp;");
					$("#passwordError2").html("&nbsp;");
					if(firstpassword.length <7) {
						$("#passwordError").html("&#10060; Passwords much be at least 7 characters long");
					}
				}
				else if(firstpassword != secondpassword) {
					passwordFlag = 0;
					$("#passwordError").html("&#10060; Passwords do not match");
					$("#passwordError2").html("&#10060;");
				}
				else {
					//passes the checks
					$("#passwordError").html("&#10003;");
					$("#passwordError2").html("&#10003;");
					passwordFlag = 1;
				}
				$("#password").val(firstpassword);
				$("#password2").val(secondpassword);

				validation(usernameFlag, passwordFlag, usergroupFlag);
			});

			$("#username").keyup(function(){
				usernameFlag = 0;
				username = $("#username").val().trim().toLowerCase();
				if(username.length<3 && username.length>0) {
					usernameFlag = 0;
					$("#usernameError").html("&#10060; Username must be at least 3 characters long");
				}
				else {
					currentusernames = $("#hold").text().trim();
					usernames = currentusernames.split("~~");
					for(i=0; i<usernames.length-1; i++) {
						if(usernames[i] == username) {
							usernameFlag = 0;
							$("#usernameError").html("&#10060; Username is already in use");
						}
						else {
							usernameFlag = 1;
							$("#usernameError").html("&#10003;");
						}
					}					
				}
				$("#username").val(username);
				validation(usernameFlag, passwordFlag, usergroupFlag);
			});

			$("#usergroup1, #usergroup2").click(function(){
				usergroupFlag = 1;
				validation(usernameFlag, passwordFlag, usergroupFlag);
			});
		});
	</script>
</head>
<body class="loggedin">
	<?php include '../layout/topnav.php';?>
	<div class="content">
		<h2>Create New User</h2>
				<form action="createuser.php" id="newProject" method="GET">
			<table>
				<tr style="border-bottom: 1px dotted lightslategray;">
					<td><label for="username">Username:</label></td>
					<td><input type="text" name="username" id="username" autocomplete="new-password"></td>
					<td><span id="usernameError">&nbsp;</span></td>
					<td> &nbsp;</td>
					
				</tr>
				<tr>
					<td><label for="password">Password:</labe></td>
					<td><input type="password" id="password" name="password" autocomplete="new-password"></td>
					<td><span id="passwordError">&nbsp;</span></td>
					<td>&nbsp;</td>					
				</tr>
				<tr>
					<td><label for="password2">Reenter Password:</label></td>
					<td><input type="password" id="password2" name="password2" autocomplete="new-password"></td>
					<td><span id="passwordError2">&nbsp;</span></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>User Group: </td>
					<td><input type="radio" id="usergroup1" name="usergroup" value="manager"> <label for="usergroup1"> Manager</label></td>
					<td><span id="infoError">&nbsp;</span></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td><input type="radio" id="usergroup2" name="usergroup" value="interviewer"> <label for="usergroup2">Interviewer</label></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td colspan="3"><input type="submit" id="submitButton" value="Add User" disabled></td>
				</tr>
			</table>
		</form>
		<div id="hold" style="display: none;">
			<?php echo $allusers;?>
		</div>
	</div>
</body>
</html>
