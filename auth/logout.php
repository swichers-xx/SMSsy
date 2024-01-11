<?php
	//logout.php - destroys cookies
	session_start();
	session_destroy();
	header('Location: ../index.php');
?>
