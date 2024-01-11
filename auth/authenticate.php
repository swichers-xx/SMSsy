<?php
	//authenticate.php - verifies username/password
if(!isset($_POST['username']) || !isset($_POST['password'])) {
	header('Location: ../index.php');
}
else {
	require __DIR__ .'/cred.php';
	session_start();
	$conn = new mysqli($servername, $username, $password, $dbname);
	if($sql = $conn -> prepare('SELECT id,password,user_group from logins WHERE username = ?')) {
		$sql -> bind_param('s', $_POST['username']);
		$sql -> execute();
		$sql -> store_result();

		if($sql -> num_rows > 0){
			$sql -> bind_result($id, $password, $user_group);
			$sql -> fetch();

			if(password_verify($_POST['password'], $password)) {
				session_regenerate_id();
				$_SESSION['loggedin'] = true;
				$_SESSION['name'] = $_POST['username'];
				$_SESSION['id'] = $id;
				$_SESSION['usergroup'] = $user_group;
				$sql2 = "INSERT INTO logs (userid, login, lastactivity) VALUES (".$id.",NOW(), NOW())";
				$conn -> query($sql2);
				$logid = $conn -> insert_id;
				$_SESSION['logid'] = $logid;
				$sql3 = "SELECT login from logs WHERE id=" .$logid;
				$results = $conn -> query($sql3) -> fetch_array();
				$_SESSION['logintime'] = $results['login'];
				header('Location: ../dashboard.php');
			}
			else {
				header('Location: ../index.php?error=1');
			}
		}
		else {
			header('Location: ../index.php?error=1');
		}

		$sql -> close();
	}
}
?>