<?php
	//get a list of conversations
require '../auth/cookiecheck.php';
require '../auth/cred.php';

if(!isset($_GET['convoid'])){
	echo "Error: Cannot retrieve conversation history. Return to the conversation page and try again.";
	die();
}
$convoid = $_GET['convoid'];

$conn = new mysqli($servername, $username, $password, $dbname);
$sql ="SELECT * from conversations WHERE id=".$convoid;
$results = $conn -> query($sql);
$output = "";
$messagedate = "";
$acuityid = "";

if($results -> num_rows > 0){
	foreach($results as $result) {
		$output .= "<div class=\"conversation\">";
		$smsid = $result['smsid'];
		$convoid = $result['id'];
		$sql2 = "SELECT * from smsmessage WHERE id=" .$smsid;
		$firstmessagedetails = $conn -> query($sql2);
		if($firstmessagedetails -> num_rows > 0){
			foreach($firstmessagedetails as $details) {
				$projectid = $details['projectid'];
				$acuityid = $details['acuityid'];
				$tonumber = $details['tonumber'];
				$respondentid=$details['respondentid'];
				$messagedate = $details['load_date'];
				$output.="<p class=\"messages usmessage\"><span class=\"datetime\">" .$messagedate . "</span><br>".$details['message'] ."</p>";
			}
		}
		$sql4 = "SELECT * FROM projectinfo WHERE id=" .$projectid;
			$moreprojectinfo = $conn -> query($sql4);
			if($moreprojectinfo -> num_rows > 0) {
				foreach($moreprojectinfo as $moreproject) {
					$projectname = $moreproject['projectname'];
					$outboundnumber = $moreproject['outboundnumber'];
					$baseurl = $moreproject['baseurl'];
				}
			} //ends projectinfo check

		$sql3 = "SELECT * from replies WHERE convoid=" .$convoid;
		$replies = $conn -> query($sql3);
		if($replies -> num_rows > 0){
			foreach($replies as $reply){
				$messagedate = $reply['date'];
				if($reply['flag']==1) {
					$output .="<p class=\"messages usmessage\"><span class=\"datetime\">".$messagedate ."</span><br>".$reply['message']."</p>";
				}
				else {
					$output .="<p class=\"messages themmessage\"><span class=\"datetime\">".$messagedate ."</span><br>".$reply['message']."</p>";
				}
			}
		}

	}

	$sql4 = "SELECT * FROM sample WHERE id=" .$respondentid;
	$respondentinfo = $conn -> query($sql4);
	if($respondentinfo -> num_rows >0) {
		foreach($respondentinfo as $info) {
			$fnameVal = $info['FNAME'];
			$lnameVal = $info['LNAME'];
			$phoneVal = $info['PHONE'];
			$emailVal = $info['EMAIL'];
			$pinVal = $info['PIN'];
		}
	}
	else {
		// cannot find respondent information - need to decide what to do here
	}

	if($pinVal=="") {
		$finalurl = $baseurl;
	}
	else {
		$finalurl = $baseurl . "&p=" .$pinVal;
	}
	$removelink = "<a href=\"deactivateconvo.php?convoid=" . $convoid ."\"> <i class=\"fas fa-user-times\"></i> Delete Message</a>";
	$sqlunsub ="SELECT * from unsubscribe WHERE projectid=".$acuityid . " AND respondentid=" .$respondentid;
	$resultsunsub = $conn -> query($sqlunsub);
	if($resultsunsub -> num_rows > 0){	
		$unsublink = "<strong><i class=\"fas fa-user-times\"></i> Respondent Unsubscribed</strong>";
	}
	else {
		$unsublink = "<a href=\"unsubscribe.php?convoid=" . $convoid . "&acuityid=" .$acuityid . "&respondentid=" .$respondentid ."\"> <i class=\"fas fa-user-times\"></i> Unsubscribe Respondent</a>";
	}
}
else {
	echo "Error: Unable to load this conversation. Please try again.";
	die();
}
?>
<!DOCTYPE html>
<html>
	<head>
	<title>Luce Research SMS System - Conversation</title>
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
	<link href="../css/style.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="../js/jquery.js"></script>
	<style>
		#newmessage {
			width: 325px;
			border-radius: 5px;
		}
		#submitButton {
			border-radius: 5px;
		}
		.sandbox {
			display: flex;
			flex-direction: row;
		}
		#respondentInfo {
			max-width: 450px;
			margin-left: 30px;
			padding-left: 10px;
			border-left: 5px solid lightgrey;
		}
		#respondentInfo a {
			text-decoration: none;
			color: #c1c4c8;
			transition: color 0.2s;
		}
		#respondentInfo a:hover {
			color: blue;
		}
		#respondenttable {
			width: 100%;
		}
		.content > div table td:first-child {
			width: 200px;
		}
		.content > div table td {
			border-bottom: 1px dashed lightgrey;
			max-width: 250px;
			word-wrap: break-word;
		}
	</style>
</head>
<body class="loggedin">
	<?php include '../layout/topnav.php';?>
	<div class="content">
		<h2>Conversation >> <?php echo $projectname;?> >> <?php echo $tonumber;?></h2>
			<div class="sandbox">
				<?php echo $output;?>
			<form action="sendconvo.php" id="modifyProject" method="GET">
				<input type="text" id="newmessage" name="newmessage" autocomplete="new-password">
				<input type="submit" id="submitButton" value="Send">
				<p style="display: none;">
					<input type="text" name="convoid" value="<?php echo $convoid;?>">
					<input type="text" name="outboundnumber" value="<?php echo $outboundnumber;?>">
					<input type="text" name="tonumber" value="<?php echo $tonumber;?>">
				</p>
			</form>
		</div>
			<div id="respondentInfo">
				<h2>Respondent Information: </h2>
				<table id="respondenttable">
					<tr>
						<td>First Name:</td>
						<td><?php echo $fnameVal;?></td>
					</tr>
					<tr>
						<td>Last Name:</td>
						<td><?php echo $lnameVal;?></td>
					</tr>
					<tr>
						<td>Phone:</td>
						<td><?php echo $phoneVal;?></td>
					</tr>
					<tr>
						<td>Email:</td>
						<td><?php echo $emailVal;?></td>
					</tr>
					<tr>
						<td>Pin:</td>
						<td><?php echo $pinVal;?></td>
					</tr>
					<tr>
						<td>Link:</td>
						<td style="font-size: 75%;"><?php echo $finalurl;?></td>
					</tr>
					<tr>
						<td colspan="2"><?php echo $unsublink;?></td>
					</tr>
					<tr>
						<td colspan="2" style="border-bottom: 0px solid black;"><?php echo $removelink;?></td>
					</tr>
				</table>
				<h2>Standard Replies:</h2>
				<ul class="standard-replies">
					<li class="standard-reply">Wrong Number <div class="mouseover">Thank you for letting me know. I'll get you removed from my list right away. Have a good day!</div></li>
					<li class="standard-reply">Please Remove Me <div class="mouseover">I apologize for any inconvenience. I'll get you removed from my list right away. Have a good day!</div></li>
					<li class="standard-reply">Who are we? <div class="mouseover">We are a market research company speaking to people in your area about important issues.</div></li>
					<li class="standard-reply standard-reply-clear">Clear <div class="mouseover"></div></li>
				</ul>

			</div>
		</div>

		<script>
			let list = document.querySelectorAll('.standard-reply');
			let replyInput = document.getElementById('newmessage');
			list.forEach(function(reply){
				reply.addEventListener('click',function() {
					let message = this.querySelector('.mouseover').textContent;
					replyInput.value = message;
				})
			});
		</script>
</body>
</html>