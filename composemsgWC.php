<?php
//grab variables from link
$toNum = $_GET["toPhone"];
$fromNum = $_GET["fromPhone"];

//if comes in without an edit variable, then we assume that respondent cannot edit the message - fail safe for improper use
	// edit 1 means they have full control of all aspects of the message
	// edit 0 means they have no control of message and can only review and send
	// background class allows us to give a visual clue that the input is not editable
$edit = ($_GET["edit"] == 1 ? 1:0);

if($edit!=1) {
	$status = "readonly";
	$backgroundclass = "inactivebackground";
} 
else { 
	$status = "";
	$backgroundclass="normalbackground";
}

//fromPhone must be passed in through link. Since Twilio will only allow us to send from twilio numbers, we cannot assign random phone number to be used.
if(!isset($_GET["fromPhone"])) {
	echo "Outbound number not set. Please alert the programming team.";
	die();
}

if(isset($_GET["msg"])) {
	$smsMessage = $_GET["msg"];
}
else {
	$smsMessage = "";
}

//compose the introduction text that the sender will see depending on whether or not they have editing rights. 
if($edit==1) {
	$shownMessage = "Compose and review your message below.You are allowed to edit everything <em>except</em> for the from number. Please alert the programming team if you need to use a different from number for whatever reason.<p>Confirm that the to number is filled out correctly as this is the number that the text message will be sent to. <strong>All phone numbers must be 10 digits long with no special characters.</strong></p><p>Once you've confirmed that everything is correct, click on the <em>Send Message</em> button to send the text. The text will be sent out immediately afterwards.";
}
else {
	$shownMessage = "Below is the message that will be sent. Take a few moments to review and confirm that all details are correct. Please pay special attention to the <strong>to number</strong> as this will be the number that the text message is sent to.</p><p>If there are errors in the message below, alert your supervisor and <strong>do not click on the send message button</strong>.</p><p>Once you've confirmed that everything is correct, click on the <em>Send Message</em> button to send the text. The text will be sent out immediately afterwards.";
}
?>

<html>
<head>
	<title>Luce Research SMS System</title>
	<link href="https://fonts.googleapis.com/css?family=Montserrat|Roboto+Condensed&display=swap" rel="stylesheet">
	<style>
		body {
			background-color: #69D2E7;
			font-family: 'Roboto Condensed', sans-serif;
		}
		#contents {
			width: 60%;
			margin: 0 auto;
			background-color: #E0E4CC;
			padding: 5px;
			padding-left: 10px;
		}
		#formloc {
			margin-left: 20px;

		}
		#header {
			background-color: #A7DBD8;
			width: 60%;
			margin: 0 auto;
			padding: 5px;
			padding-left: 10px;
		}
		h1 {
			text-align: center;
			color: #FA6900;
			font-weight: bolder;
			font-family: 'Montserrat', sans-serif;
		}
		.increaseheight {
			height: 100px;
			width: 510px;
		}
		.normalbackground {
			background-color: white;
		}
		.inactivebackground {
			background-color: lightgray;
		}
	</style>
</head>
<body>
	<div id="header">
		<h1>Luce Research SMS System</h1>
	</div>
	<div id="contents">
		<div id="introduction">
			<p><?php echo $shownMessage; ?> </p>
		</div>
		<div id="formloc">
			<form action="sendmsg.php" method="post">
				<table>
					<tr>
						<td><label>From: </label></td>
						<td><input type="number" name="fromNumber" class="inactivebackground" value="<?php echo $fromNum; ?>" readonly></td>
					</tr>
					<tr>
						<td><label>To: </label></td>
						<td><input type="number" name="toNumber" class="<?php echo $backgroundclass ?>" value="<?php echo $toNum; ?>" <?php echo $status; ?> ></td>
					</tr>
					<tr>
						<td><label>Message:</label></td>
						<td><textarea  name="smsMessage"  class="increaseheight <?php echo $backgroundclass ?>" <?php echo $status; ?> ><?php echo $smsMessage; ?> </textarea></td>
					</tr>
					<tr>
						<td colspan="2"><input type="submit" value="Send Message"></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</body>
</html>