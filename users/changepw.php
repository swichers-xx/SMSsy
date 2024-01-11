<?php
	//changepw.php - allows us to change the user's password
require '../auth/cookiecheck.php';
require '../auth/interviewercheck.php';
require '../auth/cred.php';

if(!isset($_GET['userid'])) {
	echo "Error: Unable to find this user. Please return to the user page and try again.";
	die();
}
else {
	$conn = new mysqli($servername, $username, $password, $dbname);
	$userid=$_GET['userid'];
	$sql ="SELECT * FROM logins WHERE id=".$userid;
	$results = $conn -> query($sql);

	if($results -> num_rows > 0) {
		foreach($results as $result){
			$username = $result['username'];
		}
	}
	else {
		echo "Error: Unableto find this user. Please return to the user page and try again.";
		die();
	}
}
?>

<!DOCTYPE html>
<html>
	<head>
	<title>Luce Research SMS System - Change Password</title>
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
	<link href="../css/style.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="../js/jquery.js"></script>
	<script type="text/javascript">
		$(document).ready(function(){
			passwordFlag = 0;

			function validation(password) {
				if(password==1) {
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

				validation(passwordFlag);
			});
		});
	</script>
</head>
<body class="loggedin">
	<?php include '../layout/topnav.php';?>
	<div class="content">
		<h2>Users >> <?php echo $username;?> >> Change Password</h2>
				<form action="changepass.php" id="changePassword" method="GET">
			<table>
				<tr>
					<td>New Password:</td>
					<td><input type="password" id="password" name="password" autocomplete="new-password"></td>
					<td><span id="passwordError">&nbsp;</span></td>
					<td>&nbsp;</td>					
				</tr>
				<tr>
					<td>Reenter New Password:</td>
					<td><input type="password" id="password2" name="password2" autocomplete="new-password"></td>
					<td><span id="passwordError2">&nbsp;</span></td>
					<td>&nbsp;</td>
				</tr>
				<tr style="display: none;">
					<td><input type="number" name="userid" id="userid" value="<?php echo $userid;?>"></td>
				</tr>
				<tr>
					<td colspan="3"><input type="submit" id="submitButton" value="Change Password" disabled></td>
				</tr>
			</table>
		</form>
	</div>
</body>
</html>