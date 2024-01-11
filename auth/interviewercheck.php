<?php
	//interviewercheck.php - if user is classified as an interviewer, prevent access to certain pages

	if($navusergroup != 1) {
		header('Location: ../dashboard.php');
		exit();
	}

?>