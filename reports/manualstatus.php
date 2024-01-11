<?php 
/**
* Download the library from: https://github.com/twilio/twilio-php 
* Copy the 'Twilio' folder into a directory containing this file. 
*/ 

require '../twilio-php-master/src/Twilio/autoload.php';

use Twilio\Rest\Client;

/* Your Twilio account sid and auth token */
$account_sid = "AC5d5b29b7d32d7505354f5a2a81de96f7"; 
$auth_token = "09162b7fb25700371d568bc6e8dbfdae"; 

/* Download data from Twilio API */
$client = new Client($account_sid, $auth_token);
$messages = $client->messages->stream(
  array( 
  'dateSentAfter' => '2020-12-12', 
  'dateSentBefore' => '2020-12-14'
  )
);

/* Browser magic */
$filename = $account_sid."_sms.csv"; 
header("Content-Type: application/csv");
header("Content-Disposition: attachment; filename={$filename}");

/* Write headers */
$fields = array( 'SMS Message SID', 'From', 'To', 'Date Sent', 'Status', 'Direction', 'Price', 'Body','Error Message' );
echo '"'.implode('","', $fields).'"'."\n";

/* Write rows */
foreach ($messages as $sms) { 
  $row = array(
    $sms->sid,
    $sms->from,
    $sms->to,
    $sms->dateSent->format('Y-m-d H:i:s'),
    $sms->status,
    $sms->direction,
    $sms->price,
    $sms->body,
    $sms ->errorMessage
  );

  echo '"'.implode('","', $row).'"'."\n"; 
}?>