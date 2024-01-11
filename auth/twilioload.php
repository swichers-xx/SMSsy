<?php
require '../twilio-php-master/src/Twilio/autoload.php';
use Twilio\Rest\Client;

$sid = 'AC5d5b29b7d32d7505354f5a2a81de96f7';
$token = '09162b7fb25700371d568bc6e8dbfdae';
$client = new Client($sid, $token);
?>