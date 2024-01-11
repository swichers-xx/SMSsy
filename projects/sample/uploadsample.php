<?php 
if(!empty($_FILES)) {
	$ds = DIRECTORY_SEPARATOR;
	$storeFolder = "files";
	$tempFile = $_FILES['file']['tmp_name'];
	$targetPath = dirname( __FILE__ ) . $ds. $storeFolder . $ds;
	$newFileName = md5(date('Y-m-d H:i:s:u')) . ".csv";
	$targetFile = $targetPath . $newFileName;

	move_uploaded_file($tempFile, $targetFile);
	echo $newFileName;

}
?>