<?php
require_once 'ext-confsubs.php';	// database configuration.

session_start();	// Start the session.

// Authenticate user login.
if(!isset($_SESSION['username']) or !isset($_SESSION['passwd'])){
	http_redirect('illegal.php');
}
	echo show_single_track();
?>
