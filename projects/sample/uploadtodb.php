<?php
//uploadtodb.php - takes file uploaded in upload.php and stores it in db
if(empty($_GET['projectid']) || empty($_GET['file'])) {
	echo  "Error uploading file. Please try again.";
}
else {
	require '../../auth/cookiecheck.php';
	require '../../auth/cred.php';

	$filename = $_GET['file'];
	$projectid = $_GET['projectid'];

	$containsHeader = true;
	$fullFileName = "./files/".$filename;
	$fileHandle = fopen($fullFileName, "r");
	$counter = 0;
	$query = "";
	while(($row = fgetcsv($fileHandle,0,",")) !== FALSE){
		$counter++;
		if($counter==1 && $containsHeader) {
			//if the file contains a header, skip the first row.
		}
		else {
			$subquery = $projectid . ",";
			foreach($row as $data){
				$subquery .= "'" . $data . "',";
			}
			//remove the trailing ,
			$subquery = rtrim($subquery,",");
			$query .="(" .$subquery.",1),";
			if($counter % 100 ==0) {
				$query = rtrim($query,",");
				$finalquery = "INSERT INTO sample (projectid, PHONE, FNAME, LNAME, EMAIL, PIN, FLAG) VALUES " .$query;
				$conn = new mysqli($servername, $username, $password, $dbname);
				if($conn -> query($finalquery) !== true) {
					echo "Error: Issue updating";
					echo $conn -> error;
					die();
				}
				$query = ""; //clear out query so we can start filling it up again with the next wave
			}
    	}
	}
	// makes sure the last remaining records get upload since we're only uploading in batching of 100 in main loop.
	if($query !="") {
		$query = rtrim($query,",");
			$finalquery = "INSERT INTO sample (projectid, PHONE, FNAME, LNAME, EMAIL, PIN, FLAG) VALUES " .$query;
			$conn = new mysqli($servername, $username, $password, $dbname);
			if($conn -> query($finalquery) !== true) {
				echo "Error: Issue updating";
				die();
			}
	}

	// delete the csv from the server now that we no longer need it
	unlink($fullFileName);
	header('Location: ../../dashboard.php');
}
?>