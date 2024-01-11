<?php
	//flags database as active=0 so that project no longer appears on the dashboard. All data is retained.

	if(!isset($_GET['projectid'])) {
		echo "ERROR: Could not find project to deactivate";
	}
	else {
		require '../auth/cookiecheck.php';
		require '../auth/interviewercheck.php';
		require '../auth/cred.php';
		
		$pid = $_GET['projectid'];
		$conn = new mysqli($servername, $username, $password, $dbname);
		$sql = "UPDATE projectinfo SET active=0 WHERE id=" .$pid;

		if($conn -> query($sql) === true) {
			header('Location: ../dashboard.php');
		}
		else {
			//echo "ERROR: " .$conn -> error;
		}

	}

?>