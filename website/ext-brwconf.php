<?php
require_once 'ext-confsubs.php';

// Start the session
session_start();

// Authenticate user login.
if(!isset($_SESSION['username']) or !isset($_SESSION['passwd'])){
	http_redirect('illegal.php');
}
modify_aj_conf();
echo show_conf();
?>
