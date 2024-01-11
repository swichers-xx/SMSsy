<?php
require '../twilio-php-master/src/Twilio/autoload.php';
require '../auth/cookiecheck.php';
require '../auth/cred.php';
use Twilio\Rest\Client;

if(!isset($_GET["newmessage"]) || !isset($_GET["convoid"]) || !isset($_GET["outboundnumber"]) || !isset($_GET['tonumber'])) {
	echo "To Number, From Number, or Message is empty. Message was not sent. Please try again.";
	die();
}

try {
    // Your Account SID and Auth Token from twilio.com/console
    $sid = 'AC5d5b29b7d32d7505354f5a2a81de96f7';
    $token = '09162b7fb25700371d568bc6e8dbfdae';
    $client = new Client($sid, $token);

    //collect information passed through the link. Format phone numbers so that it matches what Twilio expects.
    $toNum = "+1" . $_GET["tonumber"];
    $fromNum ="+1" . $_GET["outboundnumber"];
    $message =$_GET["newmessage"];
    $convoid = $_GET['convoid'];

    $sendmessage = $client->messages->create(
        // the number you'd like to send the message to
        $toNum,
        array(
            // A Twilio phone number you purchased at twilio.com/console
            'from' => $fromNum,
            // the body of the text message you'd like to send
            'body' => $message
        )
    );


    $conn = new mysqli($servername, $username, $password, $dbname);
    $messageSent = $conn -> real_escape_string($message);

    $sql = "INSERT INTO replies (convoid, message, fromnumber,flag) VALUES (" .$convoid .", '" .$messageSent ."', '" .$_GET['tonumber'] ."', 1)"; 

    if($conn -> query($sql) === true) {  
            $redirectURL = "conversation.php?convoid=" .$convoid;
            header("Location: " .$redirectURL);   
    }
    else {
        echo "Error updating DB.";
    }
}
catch (TwilioException $e) {
    $redirectURL = "conversations.php";
    header("Location: " .$redirectURL);
}



?>