<?php 
// unsubscribe-report.php
// created by: Kristen
// created on: december 10, 2020


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
    $getProjectIdSql = "SELECT * FROM projectinfo WHERE acuityid=" . $acuityid;
    $allProjects = $conn -> query($getProjectIdSql);
    if($allProjects->num_rows>0) { 
        $count =0;
        foreach($allProjects as $project) {
            $projectid = $project['id'];
            $sql = 'SELECT unsubscribe.date, sample.PHONE, sample.FNAME, sample.LNAME FROM unsubscribe INNER JOIN sample ON unsubscribe.respondentid = sample.id WHERE unsubscribe.projectid='.$projectid;
            $unsubscribes = $conn -> query($sql);
            if($unsubscribes -> num_rows >0){
                if($count==0) {
                    $filename = "Project_" . $acuityid . "_unsubscribes.csv";
                    header("Content-Type: application/csv");
                    header("Content-Disposition: attachment; filename={$filename}");
                    $fields = array('Phone Number', 'Unsubscribed Date', 'First Name', 'Last Name');
                    echo '"'.implode('","', $fields).'"'."\n";
                }
                $count++;
                foreach($unsubscribes as $unsubscribe) {
                    $row = array(
                        $unsubscribe['PHONE'],
                        $unsubscribe['date'],
                        $unsubscribe['FNAME'],
                        $unsubscribe['LNAME']
                    );
                    echo '"'.implode('","', $row).'"'."\n"; 
                }
            }
            else {
                header("Location: " .$redirectURL);
                die();
            }
        }
    }
}
?>