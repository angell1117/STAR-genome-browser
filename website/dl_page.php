<?php
if ($_GET['fn']) {
	$fn = $_GET['fn'];
	switch ($fn) {
	case 1:
		$myFile = 'ChromaSig.tar.gz';
		break;
	case 2:
		$myFile = 'chromatin.tar.gz';
		break;
	case 3:
		$myFile = 'GBNet.tar.gz';
		break;
	case 4:
		$myFile = 'TRANSMODIS.tar.gz';
		break;
	case 5:
		$myFile = 'Chromia2.tar.gz';
		break;
	}
	$MYFile = "/var/www/html/download/$myFile";
	$mySize = filesize($MYFile);
	header("Content-Type: application/x-gzip");
	header("Content-Length: $mySize");
	header("Content-Disposition: attachment; filename=$myFile");
	readfile("$MYFile");
	exit();
}
?>
