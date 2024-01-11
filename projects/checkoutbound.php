<?php
//confirms outbound number is connected to our Twilio account

require '../auth/twilioload.php';
if(!isset($_GET["outboundNumber"])){
	echo "0";
}
else {
	$phone = $_GET["outboundNumber"];
	$number = "+1" .$phone;

	$incomingPhoneNumbers = $client ->incomingPhoneNumbers ->read(array("phoneNumber" => $number),20);

	if(count($incomingPhoneNumbers) == 0) {
		echo "0";
	}
	else {
		echo "1";
	}
}
?>