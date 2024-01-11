<?php
//load.php - pulls sample from Acuity and loads it into sample database. 

$acuityid = 390; //hard coded for testing
$smsvar = "SMS";

$startTime = microtime(true);

// -- PLACEHOLDER FOR CODE TO GRAB GENERAL PROJECT DETAILS (NAMELY BASE URL) FROM ACUITY. 

function checkIfKey($tocheck) {
	if($tocheck == "EMAIL") {
		return 1;
	}
	else if ($tocheck =="FNAME") {
		return 2;
	}
	else if ($tocheck == "LNAME") {
		return 3;
	}
	else if ($tocheck == "PHONE") {
		return 4;
	}
	else if ($tocheck == "PIN") {
		return 5;
	}
	else {
		return 0;
	}
}

//pull all records that are flagged with sms=0
	//first identify how many records are loaded into the project.
	$curl = curl_init();
	curl_setopt_array($curl, array(
	  CURLOPT_URL => "http://vxoadmin.luceresearch.com/api/respondents/".$acuityid."/GetCount",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => "GET",
	  CURLOPT_HTTPHEADER => array(
	    "Content-Type: application/json",
	    "Authorization: Client 16NzT8K6MSXNGPxDBusOEVs19Z9UiWMv6ShRjpW+rAjK7z2zLQhCFVPyyYuGkqwt9CZuz1LX2Ep/jaHp36RVcG9RO9dBmcuKb9UT30Qp9O2irmcGfiipNQ=="
	  ),
	));
	$numberofrecords = curl_exec($curl);
	curl_close($curl);
	echo "Number of Records: " .$numberofrecords."<br>";

	if($numberofrecords<1) {
		echo "no sample to upload";
		die();
	}

	//can only get 100 records at a time from the API - the following figures out how many times we'll have to loop through the api to get all records
	$loopiterations = ceil($numberofrecords / 100);
	echo "Number of Loop Iterations Needed: " .$loopiterations ."<br>";

	//connect to api to get list of all record numbers that are flagged with sms=0
	$all_ids = array();

	for($i=0; $i<$loopiterations; $i++) {
		$curl = curl_init();
		$url = "http://vxoadmin.luceresearch.com/api/respondents/" . $acuityid ."?pageStart=" .$i * 100; 
		$expression = "{\n\t\"Expression\" : \"".$smsvar."=0\"\n}";


		curl_setopt_array($curl, array(
		  CURLOPT_URL => $url,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => $expression,
		  CURLOPT_HTTPHEADER => array(
		    "Content-Type: application/json",
		    "Authorization: Client 16NzT8K6MSXNGPxDBusOEVs19Z9UiWMv6ShRjpW+rAjK7z2zLQhCFVPyyYuGkqwt9CZuz1LX2Ep/jaHp36RVcG9RO9dBmcuKhyTTwn025ro="
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);
		$pulled = json_decode($response, true);
		foreach ($pulled as $record) {
			array_push($all_ids,$record["Id"]);
		}
	}// end loopiterations for loop

	//Loop through all_ids and get PHONE FNAME LNAME EMAIL
	$masterlist = array(); //will house all information parsed
	$counter = 0; //provides an index to start with
	foreach ($all_ids as $id) {
		$curl = curl_init();
			$curlURL = "http://vxoadmin.luceresearch.com/api/respondent/answer/".$acuityid ."?respondentId=" .$id;

			curl_setopt_array($curl, array(
			  CURLOPT_URL => $curlURL,
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => "",
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => "GET",
			  CURLOPT_HTTPHEADER => array(
			    "Content-Type: application/json",
			    "Authorization: Client 16NzT8K6MSXNGPxDBusOEVs19Z9UiWMv6ShRjpW+rAjK7z2zLQhCFVPyyYuGkqwt9CZuz1LX2Ep/jaHp36RVcG9RO9dBmcuKhyTTwn025ro="
			  ),
			));

			$response = curl_exec($curl);
			curl_close($curl);
			$responses = json_decode($response,true);
			$masterlist[$counter]["id"] = $id;

		foreach($responses as $respondentinfo) {
			$variablename = $respondentinfo["VariableName"];
			if(($value = checkIfKey($variablename)) > 0) {
				foreach ($respondentinfo["Matrices"] as $matrices) {
					foreach($matrices["Mentions"] as $mention) {
						if($value == 1) {
							$masterlist[$counter]["EMAIL"] = $mention["Value"];
						}
						else if($value == 2) {
							$masterlist[$counter]["FNAME"] = $mention["Value"];
						}
						else if($value == 3) {
							$masterlist[$counter]["LNAME"] = $mention["Value"];
						}
						else if ($value == 4) {
							$masterlist[$counter]["PHONE"] = $mention["Value"];
						}
						else if ($value == 5) {
							$masterlist[$counter]["PIN"] = $mention["Value"];
						}
					}
				}
			}//end keyvariable check				
		}//end variable capture foreach
		$counter++;
	}//end respondent foreach
	$endTime = microtime(true);
	$overallTime = $endTime - $startTime;
	echo "Overall Time: " . $overallTime . "<br>";
	var_dump($masterlist);

?>