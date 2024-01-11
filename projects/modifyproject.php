<?php
	//Updates info in projectinfo database that tracks the project information
	if(empty($_GET['projectname']) || empty($_GET['acuityID']) || empty($_GET['outboundNumber']) || empty($_GET['smsMessage'])) {
		echo "Error updating project. Please return to the dashboard and try again. If the problem persists, contact programming.<br>";
		die();
	}
	else {
		require '../auth/cookiecheck.php';
		require '../auth/interviewercheck.php';
		require '../auth/cred.php';


		$conn = new mysqli($servername, $username, $password, $dbname);

		$projectname = $conn -> real_escape_string($_GET['projectname']);
		$acuityid = $_GET['acuityID'];
		$outboundnumber = $_GET['outboundNumber'];
		$message = $conn -> real_escape_string($_GET['smsMessage']);
		$projectid = $_GET['projectid'];


		$getCurrentOrgNumSql = 'SELECT outboundnumber FROM projectinfo WHERE id=' .$projectid;
		$getCurrentOrgNum = $conn -> query($getCurrentOrgNumSql);
		if($getCurrentOrgNum->num_rows>0) {
			foreach($getCurrentOrgNum as $currentOrgNum) {
				if($currentOrgNum['outboundnumber'] != $outboundnumber) {
					$addOrgNumSql = 'INSERT INTO origination_numbers (acuityid, number) VALUES ('.$acuityid.', "'.$outboundnumber.'")';
					$conn->query($addOrgNumSql);
					echo "New origination number here";
				}
			}
		}

		$sql = "UPDATE projectinfo SET projectname='".$projectname."', acuityid='".$acuityid."', outboundnumber='".$outboundnumber."', message='".$message."' WHERE id=".$projectid; 

		if($conn -> query($sql) === true) {
			$projectid= $conn -> insert_id;
			header('Location: ../dashboard.php');
		}
		else {
			echo "ERROR: " .$conn -> error;
		}
	}

	

?>