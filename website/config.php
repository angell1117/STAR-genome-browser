<?php
$hostname = 'localhost';
$dbname = 'sdec';
$username = 'sdepig';
$password = 'sandiego2009';
// Open new database connection if not exists.
if (!isset($db)) {
	try {
		$db = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
	} catch (PDOException $e) {
		echo $e->getMessage();
	}
}
?>
