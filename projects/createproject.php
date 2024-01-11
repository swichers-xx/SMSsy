<?php
	//Creates new row in projectinfo database that tracks the project information
	if(empty($_GET['projectname']) || empty($_GET['acuityID']) || empty($_GET['outboundNumber']) || empty($_GET['smsMessage'])) {
		echo "Error creating project. Please back up and try again. If the problem persists, contact programming.<br>";
		echo "Project Name: " . $_GET['projectname'];
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
		// $smsvar = $conn ->real_escape_string(strtoupper($_GET['smsvar']));
		// $smsdatevar = $conn -> real_escape_string(strtoupper($_GET['smsdatevar']));

		//Connect to acuity to get the base url for the project
		$curl = curl_init();

		$surveyInfoURL = "http://vxoadmin.luceresearch.com/api/survey/" .$acuityid;
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $surveyInfoURL,
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
		$surveyInfo = json_decode($response,true);		
		$baseurl = $conn -> real_escape_string($surveyInfo['Link']);


		//Create projet in projectinfo
		$sql = "INSERT INTO projectinfo (projectname, acuityid, message, outboundnumber, active, baseurl) VALUES ('" .$projectname ."', '" .$acuityid ."', '" .$message ."', '" .$outboundnumber ."', '1','".$baseurl."')"; 

		if($conn -> query($sql) === true) {
			$projectid= $conn -> insert_id;
			$InsertOrgNumberSql = 'INSERT INTO origination_numbers (acuityid, number) VALUES ('.$acuityid.', "'.$outboundnumber.'")';
			$conn->query($InsertOrgNumberSql);
			$conn->close();
			header('Location: ./sample/upload.php?projectid=' .$projectid);
		}
		else {
			echo "ERROR: " .$conn -> error;
		}
	}

	

?>