<?php
require '../twilio-php-master/src/Twilio/autoload.php';
require '../auth/cookiecheck.php';
require '../auth/cred.php';
use Twilio\Rest\Client;
use Twilio\Exceptions\TwilioException;


if(!isset($_GET["toNumber"]) || !isset($_GET["fromNumber"]) || !isset($_GET["message"]) || !isset($_GET['projectid']) || !isset($_GET['respondentid'])) {
	echo "To Number, From Number, or Message is empty. Message was not sent. Please try again.";
	die();
}

if(strlen($_GET['toNumber']) != 10) {
  $errorRedirect = "send.php?projectid=" .$_GET['projectid'] ."&error=1"; 
  header("Location: " .$errorRedirect);
  die();
}
else {

  // Your Account SID and Auth Token from twilio.com/console
  $sid = 'AC5d5b29b7d32d7505354f5a2a81de96f7';
  $token = '09162b7fb25700371d568bc6e8dbfdae';
  $client = new Client($sid, $token);

  //collect information passed through the link. Format phone numbers so that it matches what Twilio expects.
  $toNum = "+1" . $_GET["toNumber"];
  $fromNum ="+1" . $_GET["fromNumber"];
  $message =$_GET["message"];
  $projectid = $_GET['projectid'];
  $respondentid = $_GET['respondentid'];
  $acuityid = $_GET['acuityid'];

  // do a check to see if a message has already been sent for this campaign. If so, prevent the message from being sent again

  $conn = new mysqli($servername, $username, $password, $dbname);
  $checksql = "SELECT * FROM sample WHERE id=" .$respondentid ." AND NOT FLAG=1";
  $checkResults = $conn -> query($checksql);
  $conn ->close();

  if($checkResults -> num_rows > 0) {
    //means the message has already been sent. Jump out before sending the message again.
    $redirectURL = "send.php?projectid=" .$projectid;
    header("Location: " .$redirectURL);
    die();

  }

  try {
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

    $messageid=$sendmessage -> sid;
    $status = $sendmessage -> status;

    $conn = new mysqli($servername, $username, $password, $dbname);
    $messageSent = $conn -> real_escape_string($message);

    $sql = "INSERT INTO smsmessage (projectid, acuityid, messageid, message, respondentid, tonumber, status, userid) VALUES ('" .$projectid ."', '" .$acuityid ."', '" .$messageid ."', '" .$messageSent ."', '" .$respondentid."' , '".$_GET['toNumber']."', '".$status."',".$cookieuserid.")"; 

    if($conn -> query($sql) === true) {
        $smsid = $conn -> insert_id;
        $sql2 = "INSERT INTO conversations (smsid, active) VALUES(" .$smsid .", 2)";
        if($conn -> query($sql2) === true) {
            $conn3 = new mysqli($servername, $username, $password, $dbname);
            $sql3 = "UPDATE sample SET FLAG=0 WHERE  id=" .$respondentid;
            $conn3 -> query($sql3);
            $redirectURL = "send.php?projectid=" .$projectid;
            header("Location: " .$redirectURL);
        }
        else {
            echo "Error updating conversations db";
        }
    }
    else {
        echo "Error updating DB.";
    }
  } catch(TwilioException $e) {
    echo $e->getCode() . " : " . $e->getMessage();
  }
}


?>