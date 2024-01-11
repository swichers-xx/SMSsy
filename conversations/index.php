<?php
	//get a list of conversations

require '../auth/cookiecheck.php';
require '../auth/cred.php';

$conn = new mysqli($servername, $username, $password, $dbname);
$sql ="SELECT * from conversations WHERE active=1";
$results = $conn -> query($sql);
$output="";

//collects information for each conversation
if($results -> num_rows > 0){
	$convo_count= 0;
	if($convo_count<=20) {
		foreach($results as $result) {
			$smsid = $result['smsid'];
			$convoid = $result['id'];
			$active = $result['active'];
			$projectid ="";
			$acuityid="";
			$tonumber="";


			//check that there are replies to the conversation - if not then we don't want to display the information
			$sql3 = "SELECT * from replies WHERE convoid=" .$convoid ." LIMIT 1";
			$replies = $conn -> query($sql3);
			if($replies -> num_rows > 0){
				foreach($replies as $reply) {
					$convodate = $reply['date'];
				}
				//gathers projectid and acuityid from smsmessage
				if($projectid=="") {
				$sql2 = "SELECT * from smsmessage WHERE id=" .$smsid;
				$projectinfo = $conn -> query($sql2);
				if($projectinfo -> num_rows > 0){
					foreach($projectinfo as $details) {
						$projectid = $details['projectid'];
						$acuityid = $details['acuityid'];
						$tonumber = $details['tonumber'];
					}
				}
			}
				//gathers projectname and outbound number from projectinfo
				$sql4 = "SELECT * FROM projectinfo WHERE id=" .$projectid;
				$moreprojectinfo = $conn -> query($sql4);
				if($moreprojectinfo -> num_rows > 0) {
					foreach($moreprojectinfo as $moreproject) {
						$projectname = $moreproject['projectname'];
						$outboundnumber = $moreproject['outboundnumber'];
					}
				} //ends projectinfo check
				//means that there are replies to the conversation, so display table row for this
				if($active==1) {
					$convo_count++;
					$output .="<tr><td>".$convoid ."</td><td style=\"text-align: left;\">" .$projectname ."</td><td>".$outboundnumber."</td><td>".$tonumber."</td><td>".$convodate."</td><td><a href=\"conversation.php?convoid=".$convoid."\"><i class=\"fas fa-comments\"></i></a></td><td><a href=\"deactivateconvo.php?convoid=".$convoid."\"><i class=\"fas fa-times-circle\"></i></a></td></tr>";
				}

			}//ends check for replies
		}
	}// end blocks that only displays 20 conversations
}//ends check for conversation
if($output==""){
	$output = "<tr><td colspan=\"7\">No conversations to display</td></tr>";
}
?>
<!DOCTYPE html>
<html>
	<head>
	<title>Luce Research SMS System - Conversation</title>
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
	<link href="../css/style.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="../js/jquery.js"></script>
	<script type="text/javascript" src="../js/sorttable.js"></script>
	<style>
		
	</style>
	<script>
		$(document).ready(function(){
			$("#activeProjects").stupidtable();
		});
	</script>
</head>
<body class="loggedin">
	<?php include '../layout/topnav.php';?>
	<div class="content">
		<h2>Conversations</h2>
		<table id="activeProjects">
			<thead>
				<tr><th>ID</th><th style="width: 200px">Project Name</th><th style="width: 150px;">Outbound<br> #</th><th style="width: 150px;">Respondent<br> #</th><th data-sort="string" data-sort-onload=yes>Last Update</th><th>View/Reply</th><th>Remove</th></tr>
			</thead>
			<tbody>
			<?php echo $output;?>
			</tbody>
		</table>
</body>
</html>
