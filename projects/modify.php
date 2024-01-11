<?php
if(!isset($_GET['projectid'])) {
	echo "Could not find project to modify. Please return to the dashboard and try again.";
	die();
}
else{
	require '../auth/cookiecheck.php';
	require '../auth/interviewercheck.php';
	require '../auth/cred.php';
	
	$projectid = $_GET['projectid'];
	$conn = new mysqli($servername, $username, $password, $dbname);
	$sql = "SELECT * FROM projectinfo WHERE id=" .$projectid;

	$results = $conn -> query($sql);

	if($results -> num_rows >0) {
		foreach($results as $project) {
			$projectname = $project['projectname'];
			$acuityid = $project['acuityid'];
			$message = $project['message'];
			$outboundnumber = $project['outboundnumber'];
			$smsvar = $project['smsvar'];
			$smsdatevar = $project['smsdatevar'];

		}
	}
	else {
		echo "Could not find project to modify. Please return to the dashboard and try again.";
		die();
	}
}

?>
<html>
	<head>
	<title>Luce Research SMS System - Modify Project</title>
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
	<link href="../css/style.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="../js/jquery.js"></script>
	<script type="text/javascript">
		//checkPipes: checks the pipe ins in the SMS message to ensure they are part of the valid list of pipeins
		//   error returned if a pipein is found that is not on the list along with a list of the offending pipeins
		function checkPipes(pipeString) {
			var found = [];
			var errorlist = [];
			var error=0;
			pipeString.replace(/\[(.*?)\]/g, function(g0,g1){found.push(g1);});
			for(i=0; i<found.length; i++){
				if(found[i].indexOf(' ') >=0){
					//can ignore this one - likely means there's a different meaning to the square brackets
				}
				else {
					var currentString = found[i].toUpperCase();
					//checks to see if the pipein is valid
					if(currentString != "FNAME" && currentString != "LNAME" && currentString != "URL" && currentString != "EMBEDURL" && currentString != "EMAIL" && currentString != "PHONE" && currentString != "PIN" && currentString != "CUTTLY") {
						error = 1;
						errorlist.push(currentString);

					}

				}
			}
			if(error) {
				return errorlist;
			}
			else {
				return "0";
			}
		}

		$(document).ready(function (){
			//prevents submitting on enter - must hit submit manually
			$(window).keydown(function(event){
				if(event.keyCode == 13){
					event.preventDefault();
					return false;
				}
			});

			$("#fname, #lname, #url, #embedurl, #email, #phone, #pin, #cuttly").click(function(event){
				updatedContent = $("#smsMessage").val() + $(this).html();
				$("#smsMessage").focus().val("").val(updatedContent);
			});

			$("#submitButton").click(function(event){
				console.log("entering check");
				errorFlag = 0; //keeps track of if there's an error with any of the fields, prevents form from being submitted if there is


				//check that project name is populated with some value
				if($("#projectname").val().trim() == '') {
					$("#projectError").html("&#10060;");
					errorFlag=1;
				}
				else {
					$("#projectError").html("&#10003;");
				}
				//checks that acuity id is populated and corresponds to a valid acuity project
				acuity = $("#acuityID").val();
				if(acuity.trim() ==''){
					$("#acuityError").html("&#10060;");
					errorFlag=1;
				}
				else {
					a1 = $.ajax({
						url: 'checkacuity.php',
						type: 'GET',
						data: 'acuityID=' + acuity,
						success: function(data) {
							if(data=="0" || data.trim()=='') {
								$("#acuityError").html("&#10060;");
								errorFlag=1;
							}
							else {
								$("#acuityError").html("&#10003; " + data);
							}
						},
						error: function(e) {
							$("#acuityError").html("&#10060;");
							errorFlag=1;
						}

					});

					//checks that phone number is properly formatted and that it is a Twilio number that we own
					phone = $("#outboundNumber").val();
					a2 = $.ajax({
						url: 'checkoutbound.php',
						type: 'GET',
						data: 'outboundNumber=' + phone,
						success: function(data) {
							if(data=="1"){
								$("#testOutbound").html("&#10003;");
							}
							else {
								$("#testOutbound").html("&#10060; Number must be 10 digits and a valid Twilio number.");
								errorFlag = 1;
							}
						},
						error: function(e) {
							$("#testOutbound").html("&#10060;");
							errorFlag = 1;
						}

					});
			
					//checks that SMS field is populated and doesn't include invalid pipeins
					message = $("#smsMessage").val();

					if(message.trim() == ''){
						$("#smsError").html("&#10060;");
						errorFlag=1;
					}
					else {
						smsPipeins = checkPipes(message.trim());
						if(smsPipeins == "0") {
							$("#smsError").html("&#10003;");
						}
						else {
							listForError = "";
							for(i=0; i<smsPipeins.length; i++){
								if(i+1 == smsPipeins.length) {
									listForError += smsPipeins[i];
								}
								else {
									listForError += smsPipeins[i] + ", ";
								}
							}
							$("#smsError").html("&#10060; Invalid pipeins: " + listForError);
							errorFlag = 1;
						}
					}					

				}

				$.when(a1,a2).done(function(r1,r2){
					//keeps us from firing off before all ajax commands are complete
					if(errorFlag==0) {
						$("#modifyProject").submit();
					}
				});

				//default behavior - means that there's an issue with the data inputted
				event.preventDefault();
				return false;


			});


		});
</script>
</head>
<body class="loggedin">
	<?php include '../layout/topnav.php';?>
	<div class="content">
		<h2>Modify  Existing Project (<?php echo $projectid. " - " .$projectname; ?>)</h2>
		<form action="modifyproject.php" id="modifyProject" method="GET">
			<table>
				<tr>
					<td><label for="projectname">Project Name:</label></td>
					<td><input type="text" name="projectname" id="projectname" value="<?php echo $projectname;?>"></td>
					<td><span id="projectError">&nbsp;</span></td>
					<td> &nbsp;</td>
					
				</tr>
				<tr>
					<td><label for="acuityID">Acuity ID:</label></td>
					<td><input type="number" id="acuityID" name="acuityID" value="<?php echo $acuityid;?>"></td>
					<td><span id="acuityError">&nbsp;</span></td>
					<td>&nbsp;</td>
					
				</tr>
				<tr>
					<td><label for="outboundNumber">Outbound Number:</label></td>
					<td><input type="number" name="outboundNumber" id="outboundNumber" value="<?php echo $outboundnumber;?>"></td>
					<td><span id="testOutbound">&nbsp;</span></td>
				</tr>
				<tr>
					<td colspan>SMS Message:</td>
					<td>&nbsp;</td>
					<td><span id="smsError">&nbsp;</span></td>
				</tr>
				<tr>
					<td>Available Pipeins:</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td colspan="3"><span class="clickable" id="fname">[FNAME]</span> <span class="clickable" id="lname">[LNAME]</span> <span class="clickable" id="url">[URL]</span> <span class="clickable" id="embedurl">[EMBEDURL]</span> <span class="clickable" id="email">[EMAIL]</span> <span class="clickable" id="phone">[PHONE]</span> <span class="clickable" id="pin">[PIN]</span> <span class="clickable" id="cuttly">[CUTTLY]</span></td>
				</tr>
				<tr>
					<td colspan="3"><textarea id="smsMessage" name="smsMessage"><?php echo $message;?></textarea></td>
				</tr>
				<tr>
					<td colspan="2"><input type="submit" id="submitButton" value="Modify Project"></td>
					<td style="display: none;"><input type="number" name="projectid" value="<?php echo $projectid;?>"></td>
				</tr>
			</table>
		</form>
</body>
</html>