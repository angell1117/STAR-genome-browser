<?php
require_once 'config.php';
function goBack ($_str_info) {
   echo '<div style="text-align: center">';
   echo "<p>$_str_info</p>";
   echo "<form>";
   echo "Want to go back?&nbsp;";
   echo "<input type=\"button\" value=\"Yes\" onClick='window.history.back()'>";
   echo "</form>";
   echo '</div>';
   exit;
}

// Destroy old session.
session_start();
$_SESSION = array();
session_destroy();

// Re-Start the session
session_start();

$autologing=false;// auto login as a guest without password
if(!$autologing)
{
	$host  = $_SERVER['HTTP_HOST'];
	$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
	$extra = 'login.php';
	header("Location: http://$host$uri/$extra");
	exit;
}
else
{
	$host  = $_SERVER['HTTP_HOST'];
	$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
	$extra = 'guestlogin.php';
	header("Location: http://$host$uri/$extra");
	exit;
}

?>
