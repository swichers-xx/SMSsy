<?php
require __DIR__ . '/twilio-php-master/src/Twilio/autoload.php';
use Twilio\Rest\Client;

if(empty($_POST["toNumber"]) || empty($_POST["fromNumber"]) || empty($_POST["smsMessage"])) {
	echo "To Number, From Number, or Message is empty. Message was not sent. Please try again.";
	die();
}


// Your Account SID and Auth Token from twilio.com/console
$sid = 'AC5d5b29b7d32d7505354f5a2a81de96f7';
$token = '09162b7fb25700371d568bc6e8dbfdae';
$client = new Client($sid, $token);

//collect information passed through the link. Format phone numbers so that it matches what Twilio expects.
$toNum = "+1" . $_POST["toNumber"];
$fromNum ="+1" . $_POST["fromNumber"];
$message =$_POST["smsMessage"];

// Use the client to do fun stuff like send text messages!
$client->messages->create(
    // the number you'd like to send the message to
    $toNum,
    array(
        // A Twilio phone number you purchased at twilio.com/console
        'from' => $fromNum,
        // the body of the text message you'd like to send
        'body' => $message
    )
);
echo "Message successfully sent. Please close this window.";

?>