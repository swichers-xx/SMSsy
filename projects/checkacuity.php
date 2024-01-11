<?php
//checks that Acuity id is valid
if(!isset($_GET["acuityID"])) {
	echo "0";
}
else {
	$acuityID = $_GET["acuityID"];

	$curl = curl_init();
	$url = "http://vxoadmin.luceresearch.com/api/survey/" . $acuityID;
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
		echo "0";
	}
	else {
		$surveyInfo = json_decode($response,true);
		echo $surveyInfo['Name'];
	}
}

?>