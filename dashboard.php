<?php
//dashboard.php - main overview of all projects
//get a list of all active projects 
require __DIR__ .'/auth/cookiecheck.php';
require __DIR__ .'/auth/cred.php';

function getRemaining($projectid,$servername, $username, $password, $dbname) {
	$conn = new mysqli($servername, $username, $password, $dbname);
	$total = $conn->query("SELECT COUNT(id) as total FROM sample WHERE FLAG=1 AND projectid=".$projectid);
	$conn -> close();
	foreach($total as $tot) {
		$total = $tot['total'];
	}
	return $total;
}

function getSent($projectid,$servername, $username, $password, $dbname) {
	$conn = new mysqli($servername, $username, $password, $dbname);
	$total = $conn->query("SELECT COUNT(id) as total FROM sample WHERE FLAG=0 AND projectid=".$projectid);
	$conn -> close();
	foreach($total as $tot) {
		$total = $tot['total'];
	}
	return $total;
}
// ------------------
if(isset($_GET['limit'])){
	$limit = $_GET['limit'];
}
else {
	$limit=8;
}

if(isset($_GET['page'])){
	$page = $_GET['page'];
}
else {
	$page = 1;
}
$start_from = ($page-1) * $limit;
$conn = new mysqli($servername, $username, $password, $dbname);
$total = $conn->query('SELECT COUNT(id) as total FROM projectinfo WHERE ACTIVE=1');

foreach($total as $tot) {
	$total = $tot['total'];
}
$numPages = ceil($total/$limit);
$sql = "SELECT * FROM projectinfo WHERE ACTIVE=1 LIMIT ".$start_from.", ".$limit;

$results = $conn -> query($sql);
$conn -> close();
$output = "";

if($results -> num_rows > 0) {	
	$output .= "<table id=\"activeProjects\">";
	$header ="<tr><th style=\"width: 250px;\">Project Name</th><th style=\"width: 100px;\">Outbound Number</th><th style=\"width: 350px;\">Message</th><th style=\"width: 30px;\"># Sent</th><th style=\"width: 30px;\"># Left</th><th style=\"width: 150px;\">Last Modified</th><th style=\"width: 30px;\">Modify</th><th style=\"width: 30px;\">Send</th><th style=\"width: 30px;\">Delete</th></tr>";
	$output .= $header;
	$count = 0;
	foreach($results as $project) {
		if($navusergroup==1) {
			$editHTML = "<td><a href=\"./projects/modify.php?projectid=" .$project['id'] ."\"> <i class=\"fas fa-edit\"></i></a></td>";
			$deactiveHTML = "<td><a href=\"./projects/deactive.php?projectid=" . $project['id'] ."\"><i class=\"fas fa-times-circle\"></i></a></td>";
		}
		else {
			$editHTML="<td> - </td>";
			$deactiveHTML = "<td> - </td>";
		}
		// if($count % 5 == 0 && $count != 0)
		// {
		// 	$output .= $header;
		// }
		$output .="<tr><td style=\"text-align: left;\">" .$project['projectname'] . "</td><td>" . $project['outboundnumber'] ."</td><td style=\"text-align: left;\">" .$project['message'] ."</td><td>" .getSent($project['id'], $servername, $username, $password, $dbname)."</td><td>" .getRemaining($project['id'], $servername, $username, $password, $dbname) ."</td><td>".$project['mod_date'] ."</td>" .$editHTML ."<td><a href=\"./send/send.php?projectid=".$project['id']."\"><i class=\"fas fa-paper-plane\"></i></a></td>".$deactiveHTML."</tr>";
		$count++;
	}
	$output .="</tbody></table>";
		$output .= '<div id="controls"><select id="limitSelector">
				<option value="0">--</option>
				<option value="5">5</option>
				<option value="10">10</option>
				<option value="15">15</option>
				<option value="100">All</option>
				</select> <span style="padding: 0.25em;">per page</span>';
	$output .= '<ul id="pagination">';
	for($i=1; $i<=$numPages; $i++){
		if($i==1) {
			$back = $page -1;
			if($page==1) {
				$output .= '<li class="page"><a href="dashboard.php?page=' .$page .'"> << </a></li>';
			}
			else {
				$output .= '<li class="page"><a href="dashboard.php?page=' .$back .'"> << </a></li>';
			}
		}
		if($i == $page) {
			$output .= '<li class="page page-active"><a href="dashboard.php?page=' .$i .'">'.$i.'</a></li>';
		}
		else {
			$output .= '<li class="page"><a href="dashboard.php?page=' .$i .'">'.$i.'</a></li>';
		}
		
		if($i==$numPages) {
			$forward = $page +1;
			if($page == $numPages) {
				$output .= '<li class="page"><a href="dashboard.php?page=' .$page .'"> >> </a></li>';
			}
			else {
				$output .= '<li class="page"><a href="dashboard.php?page=' .$forward .'"> >> </a></li>';
			}
			
		}
	}
	$output .= '</ul></div>';

}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Luce Research SMS System - Dashboard</title>
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
		<link href="./css/style.css" rel="stylesheet" type="text/css">
		<style>
		#activeProjects td, #activeProjects th {
			font-size: 0.8rem;
			min-width: 50px;
			max-width: 200px;
			text-align: center;
			padding-left: 2px;
			padding-right: 2px;
		}
		#activeProjects i {
			font-size: 0.9rem;
		}
		</style>
	</head>
	<body class="loggedin">
		<?php include './layout/topnav.php';?>
		<div class="content">
			<h2>Active Projects</h2>
			<?php echo $output; ?>
		</div>
	</body>
	<script>
		let limitDropdown = document.querySelector('#limitSelector');
		limitDropdown.addEventListener('change', function(){
			console.log(this.value);
			if(!this.value==0) {
				window.location.href= "dashboard.php?limit=" + this.value;
			}
		});
	</script>
</html>