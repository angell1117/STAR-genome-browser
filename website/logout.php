<?php
// Start session.
session_start();

// Clear all session variables.
$_SESSION = array();

session_destroy();

// Remind user about current status.
$host  = $_SERVER['HTTP_HOST'];
$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
$extra = 'login.php';
header("Location: http://$host$uri/$extra");
?>
