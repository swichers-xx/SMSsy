<?php 
// origination-numbers.php
// created by: Kristen
// created on: december 24 2020


require '../auth/cookiecheck.php';
require '../auth/interviewercheck.php';
require '../auth/cred.php';

$redirectURL = "../reports/index.php";

if(!isset($_GET['acuityid'])) {
    //route back to the dashboard -- ideally display error message
    header("Location: " .$redirectURL);
    die();
} 
else {
    //id is captured, pull the list
    $acuityid = $_GET['acuityid'];

    $conn = new mysqli($servername, $username, $password, $dbname);
    $getAllOrgNumSQL = "SELECT * FROM origination_numbers WHERE acuityid=" . $acuityid;
    $allOrgNums = $conn -> query($getAllOrgNumSQL);
    if($allOrgNums->num_rows>0) { 
        $count =0;
        foreach($allOrgNums as $orgnum) {
            if($count==0) {
                $filename = "Project_" . $acuityid . "_origination_numbers.csv";
                header("Content-Type: application/csv");
                header("Content-Disposition: attachment; filename={$filename}");
                $fields = array('Origination Numbers');
                echo '"'.implode('","', $fields).'"'."\n";
            }
            $count++;

            $row = array($orgnum['number']);
            echo '"'.implode('","', $row).'"'."\n"; 
                
        }
    }
    else {
        header("Location: " .$redirectURL);
        die();
    }
}
?>