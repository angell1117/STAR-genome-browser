<?php
require_once 'ext-confsubs.php';

// Start the session
session_start();

// Authenticate user login.
if(!isset($_SESSION['username']) or !isset($_SESSION['passwd'])){
	http_redirect('illegal.php');
}
if (!empty($_POST['showoptions']))
{
	echo show_track_option();
}
else if (!empty($_POST['annotation']))
{
	echo show_annotation();
}
else if (!empty($_POST['inquiry']))
{
	echo check_track();
}
else{
	modify_aj_conf();
	echo show_track();
}
?>
