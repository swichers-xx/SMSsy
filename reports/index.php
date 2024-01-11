<?php 
// report.php
// created by: Kristen
// created on: june 15 2020

require '../auth/cookiecheck.php';
require '../auth/interviewercheck.php';
require '../auth/cred.php';


function getAcuityName($acuityId) {
	$curl = curl_init();
	$url = "http://vxoadmin.luceresearch.com/api/survey/" . $acuityId;
	curl_setopt_array($curl, array(
		CURLOPT_URL => $url,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "GET",
		CURLOPT_HTTPHEADER => array(
		"Authorization: Client 16NzT8K6MSXNGPxDBusOEVs19Z9UiWMv6ShRjpW+rAjK7z2zLQhCFVPyyYuGkqwt9CZuz1LX2Ep/jaHp36RVcG9RO9dBmcuKhyTTwn025ro="
		),
	));

	$response = curl_exec($curl);

	curl_close($curl);
	if(empty(($response))){
		$name = "Unknown Project";
	}
	else {
		$surveyInfo = json_decode($response,true);
		$name = $surveyInfo['Name'];
	}
	return $name;
}

//get all active projects
$conn = new mysqli($servername, $username, $password, $dbname);
$sql = "SELECT acuityid FROM projectinfo WHERE active=1";
$allactive = $conn -> query($sql);
$output = '';
$idlist = array();
if($allactive -> num_rows>0) {
	foreach($allactive as $activeproject) {
        $found = array_search($activeproject['acuityid'],$idlist);
        if($found === false) {
            array_push($idlist,$activeproject['acuityid']);
        }
	}
}
else {
	$output = '<strong>No active projects found</strong>';
}

if(count($idlist) > 0) {
    foreach ($idlist as $aid) {
		$acuityProjectName = getAcuityName($aid);
		$output .= '<div class="report-project"><h2>' .$acuityProjectName.'</h2><div class="report-project-content">';
        $getProjectsSql = "SELECT * from projectinfo WHERE acuityid=${aid} AND active=1";
        $matchingProjects = $conn->query($getProjectsSql);
        if($matchingProjects->num_rows>0) {
			$overallHours = 0;
			$output.= '<div class="report-table"><table class="reports"><tr><th>Project Id</th><th>Project Name</th><th>Total Interviewer Hours</th></tr>';
            foreach($matchingProjects as $matchingProject) {
				$output.='<tr>';
				$output.= "<td>${matchingProject['id']}</td>";
				$output .= "<td>${matchingProject['projectname']}</td>";
				$interviewerHoursSql = "SELECT * FROM logs WHERE pid=${matchingProject['id']}";
				$interviewerLogs = $conn->query($interviewerHoursSql);
				if($interviewerLogs->num_rows>0) {
					$totalsecs = 0;
					foreach($interviewerLogs as $interviewerLog) {
						$totalsecs += strtotime($interviewerLog['lastactivity']) - strtotime($interviewerLog['login']);
					}
					$overallHours += $totalsecs;
					if($totalsecs >0) {
						$hours = $totalsecs/60/60;
					}
					else {
						$hours = '0.00';
					}
					$output .= '<td>'.number_format(round($hours,2),2).'</td>';
				}
				$output .='</tr>';
			}
			if($overallHours>0) {
				$output .= '<tr class="report-total-hours">';
				$output .= '<td>Total:</td><td>&nbsp;</td>';
				$output .= '<td>' . number_format(round($overallHours/60/60,2),2) . '</td>';
				$output .= '</tr>';
			}
			$output .='</table></div>';
			$output .='<div class="report-links"><h3>Downloads:</h3><ul class="report-download-list">
			<li class="report-download-item"><a href="unsubscribe-report.php?acuityid='.$aid.'"><i class="fas fa-user-times"></i> Unsubscribes</a></li>
			<li class="report-download-item"><a href="origination-numbers.php?acuityid='.$aid.'"><i class="fas fa-mobile-alt"></i> Origination Numbers</a></li>
			<li class="report-download-item"><a href="inbound-log.php?acuityid='.$aid.'"><i class="fas fa-comments"></i> Inbound Messages</a></li>
			<li class="report-download-item"><a href="outbound-log.php?acuityid='.$aid.'"><i class="fas fa-sms"></i> Outbound Messages</a></li>
			</ul></div>';
		}
		$output .='</div></div>';
	}
}
?>

<!DOCTYPE html>
<html>
	<head>
	<title>Luce Research SMS System - Report</title>
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
	<link href="../css/style.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="../js/jquery.js"></script>
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
	<?php include '../layout/topnav.php';?>
	<div class="content">
		<h2>Reports: Current Active Projects</h2>
		<?php echo $output;?>		
</div>


</body>
</html>