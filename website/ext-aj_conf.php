<?php
require_once 'ext-confsubs.php';	// functions for AJ configuration.

session_start();	// Start session to read configuration.

// Authenticate user login.
if(!isset($_SESSION['username']) or !isset($_SESSION['passwd'])){
	http_redirect('illegal.php');
}
	modify_aj_conf();
?>
