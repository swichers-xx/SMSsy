<?php
//send.php - gets project id from URL. Grabs first record in acuity not flagged as already being sent and displays info on the page. 
if(!isset($_GET['projectid'])) {
	echo "Error getting records. Return to dashboard and try again.";
	die();
}
else {
	$sendingerror = "";
	$errorMessage ="";
	$noIds=0;
	if(isset($_GET['error'])) {
		//error sending message
		$sendingerror = "<span class=\"errorMessage\">Unable to send the message. Double check that the phone number is correct and try again. If you keep seeing this message, click on \"Flag Record\" to skip to the next message.</span>";
	}
	require '../auth/cookiecheck.php';
	require '../auth/cred.php';
	$projectid = $_GET['projectid'];
	$_SESSION['pid'] = $projectid;
	$conn = new mysqli($servername, $username, $password, $dbname);

	date_default_timezone_set('America/Denver');
	$logintime = $_SESSION['logintime'];
	$starttime = new DateTime($_SESSION['logintime']);
	$currenttime = new DateTime("now");
	$elapsedtime = $starttime -> diff($currenttime);
	$hoursdiff = $elapsedtime -> format("%h");
	$minutesdiff = $elapsedtime -> format("%i");
	$secondsdiff = $elapsedtime -> format("%s");
	$totalelapsed = ($hoursdiff * 60 * 60) + ($minutesdiff * 60) + $secondsdiff;

	//INTERVIEWER STATS
	$allsmssql = "SELECT COUNT(*) AS 'totalSent' FROM smsmessage WHERE load_date > '".$logintime."' AND userid=".$cookieuserid;
	$totalMessageArray = $conn -> query($allsmssql) -> fetch_array();
	$totalMessagesSent = $totalMessageArray['totalSent'];

	$lasthoursql = "SELECT COUNT(*) AS 'lasthour' FROM smsmessage WHERE userid=".$cookieuserid." AND load_date > DATE_SUB(NOW(), INTERVAL 1 HOUR)";
	$lastHourArray = $conn -> query($lasthoursql) -> fetch_array();
	$lastHour = $lastHourArray['lasthour'];

	$rate = ($totalMessagesSent / $totalelapsed)* 60;

	//GET BASIC PROJECT INFORMATION
	$sql = "SELECT * FROM projectinfo WHERE id=".$projectid;
	$results = $conn ->query($sql);
	$conn -> close();

	if($results -> num_rows >0) {
		foreach($results as $project) {
			$projectname = $project['projectname'];
			$outboundnumber = $project['outboundnumber'];
			$message = $project['message'];
			$baselink = $project['baseurl'];
			$acuityid = $project['acuityid'];
		}
		
		//RANDOMLY SELECT 1 UNSENT CASE TO DELIVER
		$conn = new mysqli($servername, $username, $password, $dbname);
		//$sql = "SELECT * FROM sample WHERE  projectid=".$projectid." AND FLAG=1 ORDER BY RAND() LIMIT 1";
		$sql = "SELECT * FROM sample WHERE  projectid=".$projectid." AND FLAG=1 LIMIT 1";
		$records = $conn -> query($sql);
		$conn -> close();
		if($records -> num_rows > 0) {
			foreach($records as $record){
				$phone = $record['PHONE'];
				$firstname = trim($record['FNAME']);
				$lastname = $record['LNAME'];
				$email = $record['EMAIL'];
				$pin = $record['PIN'];
				$respondentid = $record['id'];
				$finalmessage = composeMsg($firstname, $lastname, $email, $phone, $pin, $baselink, $message);
			}

			$flaglink = "<a href=\"flagmessage.php?projectid=" . $projectid . "&respondentid=" .$respondentid ."\"> <i class=\"fas fa-flag\"></i> Flag Record</a>";
		}
		else {
			//NO UNSENT RECORDS FOUND. DISPLAY MESSAGE.
			$errorMessage = "<span class=\"errorMessage\"><i class=\"fas fa-exclamation-triangle\"></i> All messages have been sent. Return to the dashboard to work on a new project.</span>";
			$outboundnumber = "";
			$baselink = "";
			$noIds = 1;
		}

	}
	else {
		//UNABLE TO FIND THE CORRESPONDING PROJECT IN THE DATABASE.
		$errorMessage = "Project cannot be found. Please return to the dashboard to try again.";
		$noIds = 1;
	}

		
}

function cuttly($urltoShorten) {
	$link = urlencode($urltoShorten);
	$api = "5743b0e51543601dff8a7ebdc296b33a24929";
	$url = "https://cutt.ly/api/api.php?key=$api&short=$link";
	$ch = curl_init();
	curl_setopt ($ch, CURLOPT_URL, $url);
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 5);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
	$contents = curl_exec($ch);
	if (curl_errno($ch)) {
	 	$contents = '';
	} 
	else {
	  curl_close($ch);
	}

	if (!is_string($contents) || !strlen($contents)) {
	$contents = '';
	}

	$data = json_decode($contents, true);
	$contents = $data["url"]["shortLink"];
	return $contents;
}

function composeMsg($fname, $lname, $email, $phone, $pin, $url, $orgMsg) {

	$embedURL = $url ."&p=".$pin;

	$msg = str_ireplace("[FNAME]", $fname,$orgMsg);
	$msg = str_ireplace("[LNAME]", $lname, $msg);
	$msg = str_ireplace("[EMAIL]", $email, $msg);
	$msg = str_ireplace("[PHONE]", $phone, $msg);
	$msg = str_ireplace("[PIN]", $pin, $msg);
	$msg = str_ireplace("[URL]", $url, $msg);
	$msg = str_ireplace("[EMBEDURL]", $embedURL, $msg);
	//$msg = str_ireplace("[CUTTLY]", cuttly($embedURL), $msg);

	return $msg;
}
?>
<!DOCTYPE html>
<html>
	<head>
	<title>Luce Research SMS System - New Project</title>
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
	<link href="../css/style.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="../js/jquery.js"></script>
	<script type="text/javascript" src="../js/gauge.js"></script>
	<script>
		$(document).ready(function(){
			$("#toNumber").focus();

			var opts = {
			angle: 0.15, // The span of the gauge arc
			lineWidth: 0.44, // The line thickness
			radiusScale: 1, // Relative radius
			pointer: {
			length: 0.6, // // Relative to gauge radius
			strokeWidth: 0.035, // The thickness
			color: '#000000' // Fill color
			
			},
			limitMax: false,     // If false, max value increases automatically if value > maxValue
			limitMin: false,     // If true, the min value of the gauge will be fixed
			colorStart: '#6FADCF',   // Colors
			colorStop: '#8FC0DA',    // just experiment with them
			strokeColor: '#E0E0E0',  // to see which ones work best for you
			generateGradient: true,
			highDpiSupport: true,     // High resolution support
			percentColors: [[0.0, "#ff0000" ], [0.50, "#f9c802"], [1.0, "#a9d70b"]]


			};
			var target = document.getElementById('stats'); // your canvas element
			var gauge = new Gauge(target).setOptions(opts); // create sexy gauge!
			gauge.maxValue = 16; // set max gauge value
			gauge.setMinValue(0);  // Prefer setter over gauge.minValue = 0
			gauge.animationSpeed = 32; // set animation speed (32 is default value)
			gaugevalue = document.getElementById("mpm").innerHTML;
			console.log(gaugevalue);
			gauge.set(gaugevalue); // set actual value

			$("#sendmessage").submit(function() {
				$("#submitButton").val("Please Wait...").attr("disabled", "disabled");
				$(".loading").html("<i class=\"fas fa-spinner fa-pulse\"></i>");
				return true;

			});
			$("#flag").click(function(){
				exiturl = "flagmessage.php?projectid=" + $("#prjid").val() + "&respondentid="+$("#rspid").val();
				window.location = exiturl;
			});

		});
	</script>
	<style>
		.sandbox {
			display: flex;
			flex-direction: row;
		}
		#sendingpanel, #leftside {
			width: 500px;
		}
		#respondentInfo, #rightside {
			max-width: 400px;
			margin-left: 30px;
			padding-left: 10px;
			border-left: 5px solid lightgrey;
		}
		textarea {
			width: 100%;
			height: 150px;
		}
		#respondentInfo a {
			text-decoration: none;
			color: #c1c4c8;
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
		.errorMessage {
			color: red;
			font-weight: bold;
		}
		#interviewerProgress{
			display: flex;
			flex-direction: row;
		}
	</style>
</head>
<body class="loggedin">
	<?php include '../layout/topnav.php';?>
	<div class="content">
		<h2>Send Message >> <?php echo $projectname; ?> >> Respondent: <?php echo $respondentid;?> <span class="loading">&nbsp;</span></h2>
		<div class="sandbox">
			<div id="sendingpanel">
				<?php echo $errorMessage;?>
				<?php echo $sendingerror;?>
				<form action="sendmessage.php" id="sendmessage" method="GET">
					<table>
						<tr>
							<td>To:</td>
							<td><input type="number" name="toNumber" id="toNumber" value="<?php echo $phone;?>"></td>
						</tr>
						<tr>
							<td>From:</td>
							<td><input type="number" name="fromNumber" id="fromNumber" value="<?php echo $outboundnumber;?>"></td>
						</tr>
						<tr>
							<td>SMS Message:</td>
							<td><textarea name="message" id="message"><?php echo $finalmessage;?></textarea></td>
						</tr>
						<tr style="display: none;">
							<td><input type="number" id="prjid" name="projectid" value="<?php echo $projectid;?>"></td>
							<td><input type="number" id="rspid" name="respondentid" value="<?php echo $respondentid;?>"></td>
							<td><input type="number" name="acuityid" value="<?php echo $acuityid;?>"></td>
						</tr>
						<tr>
							<td colspan="2"><input type="submit" id="submitButton" value="Send" <?php echo ($noIds==1)?'disabled':'';?>></td>
						</tr>
					</table>
				</form>
			</div>
			<div id="respondentInfo">
				<table id="respondenttable">
					<tr>
						<td>First Name:</td>
						<td><?php echo $firstname;?></td>
					</tr>
					<tr>
						<td>Last Name:</td>
						<td><?php echo $lastname;?></td>
					</tr>
					<tr>
						<td>Phone:</td>
						<td><?php echo $phone;?></td>
					</tr>
					<tr>
						<td>Email:</td>
						<td><?php echo $email;?></td>
					</tr>
					<tr>
						<td>Pin:</td>
						<td><?php echo $pin;?></td>
					</tr>
					<tr>
						<td>Link:</td>
						<td style="font-size: 75%;"><?php echo $baselink;?></td>
					</tr>
					<tr>
						<td colspan="2"><?php echo $flaglink;?></td>
					</tr>
				</table>
			</div>
		</div>
		<h2>Your Statistics</h2>
			<div id="interviewerProgress">
				
				<div id="leftside">
				<table id="interviewerstats">
					<tr>
						<td>Total Messages Sent:</td>
						<td><span id="totalmessages"><?php echo $totalMessagesSent;?></span></td>
					</tr>
					<tr>
						<td>Total Messages Sent in Last Hour:</td>
						<td><span id="sentlasthour"><?php echo $lastHour;?></span></td>
					</tr>
					<tr>
						<td>Total Time:</td>
						<td><span id="totaltime"><?php echo number_format($totalelapsed / 60,2);?></span> minutes</td>
					</tr>
					<tr>
						<td>Messages Per Minute:</td>
						<td><span id="mpm"><?php echo number_format($rate,2);?></span></td>
					</tr>
				</table>
			</div>
			<div id="rightside">
				<canvas id="stats"> </canvas>
			</div>
	</div>
	</div>

</body>
</html>
