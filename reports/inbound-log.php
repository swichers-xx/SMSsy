<?php 
/**
* Download the library from: https://github.com/twilio/twilio-php 
* Copy the 'Twilio' folder into a directory containing this file. 
*/ 
require '../auth/cookiecheck.php';
require  '../twilio-php-master/src/Twilio/autoload.php';

use Twilio\Rest\Client;

$account_sid = "AC5d5b29b7d32d7505354f5a2a81de96f7"; 
$auth_token = "09162b7fb25700371d568bc6e8dbfdae"; 
$redirectURL = "../reports/index.php";

if(!isset($_GET['acuityid'])) {
    header("Location: " .$redirectURL);
    die();
}
else {
    $acuityid = $_GET['acuityid'];
    $conn = new mysqli($servername, $username, $password, $dbname);
    $getAllOrgNumSQL = "SELECT * FROM origination_numbers WHERE acuityid=" . $acuityid;
    $allOrgNums = $conn -> query($getAllOrgNumSQL);
    if($allOrgNums->num_rows>0) { 
        $count=0;
        foreach($allOrgNums as $orgnum) {
            if($count==0) {
                $filename = "inbound_" .$acuityid."_sms.csv"; 
                header("Content-Type: application/csv");
                header("Content-Disposition: attachment; filename={$filename}");
                $fields = array( 'SMS Message SID', 'From', 'To', 'Date Sent', 'Status', 'Direction', 'Price', 'Body','Error Message' );
                echo '"'.implode('","', $fields).'"'."\n";
            }
            $count++;
            $toNumber = "+1" .$orgnum['number'];
            /* Download data from Twilio API */
            $client = new Client($account_sid, $auth_token);
            $messages = $client->messages->stream(
                array( 
                    'to' => $toNumber
                )
            );
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
            }
        }
    }
    else {
        header("Location: " .$redirectURL);
        die();
    }
}

?>