<?php 
// newproject.php
// created by: Kristen
// created on: Feb 8 2020

require '../auth/cookiecheck.php';
require '../auth/interviewercheck.php';
require '../auth/cred.php';
?>

<!DOCTYPE html>
<html>
	<head>
	<title>Luce Research SMS System - New Project</title>
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

			$("#outboundNumber").keyup(function(){
				if($("#outboundNumber").val().length == 10) {
					$("#submitButton").prop('disabled', false);
				}
				else {
					$("#submitButton").prop('disabled', true);
				}
			});

			$("#submitButton").click(function(event){
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
						$("#newProject").submit();
					}
				});

				//default behavior - means that there's an issue with the data inputted
				event.preventDefault();
				return false;


			});


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
</style>
</head>
<body class="loggedin">
	<?php include '../layout/topnav.php';?>
	<div class="content">
		<h2>Create A New Project</h2>
		<div class="sandbox">
			<div id="formhold">
		<form action="createproject.php" id="newProject" method="GET">
			<table>
				<tr>
					<td><label for="projectname">Project Name:</label></td>
					<td><input type="text" name="projectname" id="projectname" autocomplete="new-password"></td>
					<td><span id="projectError">&nbsp;</span></td>
					<td> &nbsp;</td>
					
				</tr>
				<tr>
					<td><label for="acuityID">Acuity ID:</label></td>
					<td><input type="number" id="acuityID" name="acuityID"></td>
					<td><span id="acuityError">&nbsp;</span></td>
					<td>&nbsp;</td>					
				</tr>
				<tr>
					<td><label for="outboundNumber">Outbound Number:</label></td>
					<td><input type="number" name="outboundNumber" id="outboundNumber"></td>
					<td><span id="testOutbound">&nbsp;</span></td>
				</tr>
				<tr>
					<td colspan>SMS Message:</td>
					<td>&nbsp;</td>
					<td><span id="smsError">&nbsp;</span></td>
				</tr>
				<tr>
					<td colspan="3"></td>
				</tr>
				<tr>
					<td colspan="3"><textarea id="smsMessage" name="smsMessage"></textarea></td>
				</tr>
				<tr>
					<td colspan="3"><input type="submit" id="submitButton" value="Next >>" disabled></td>
				</tr>
			</table>
		</form>
	</div>
	<div id="pipeinInfo">
		<h2>Pipe In from Sample</h2>
	<span class="clickable" id="fname">[FNAME]</span> <span class="clickable" id="lname">[LNAME]</span> <span class="clickable" id="url">[URL]</span> <span class="clickable" id="embedurl">[EMBEDURL]</span> <span class="clickable" id="email">[EMAIL]</span> <span class="clickable" id="phone">[PHONE]</span> <span class="clickable" id="pin">[PIN]</span> <span class="clickable" id="cuttly">[CUTTLY]</span>
</div>
</div>
</body>
</html>