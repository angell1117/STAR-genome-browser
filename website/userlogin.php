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

// Verify user authentication.
$username = $_POST['user_name'];
if (empty($username)) {
	goBack("Please input the user ID.");
}
$passwd = $_POST['user_pwd'];
if (empty($passwd)) {
	goBack("Please input the password.");
}
$query = "select PASSWD, group_id from user where user_id = ? and user_status=1";
$stmt = $db->prepare($query);
$stmt->bindParam(1, $username);
$stmt->execute() or goBack("Error happened. Please try later.");
if($stmt->rowCount() == 0){     // Authentication failed.
        goBack("This user doesn't exist or isn't activated.");
} else {        // Register user name and password in session variables.
        $row = $stmt->fetch();
	$tpasswd = $row['PASSWD'];
	if ($tpasswd != $passwd) {
		goBack("The password is incorrect.");
	}
	$_SESSION['username'] = $username;
	$_SESSION['passwd'] = $passwd;
	$_SESSION['usergrp'] = $row['group_id'];
	$host  = $_SERVER['HTTP_HOST'];
	$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
	$extra = 'brwtrk.php';
	header("Location: http://$host$uri/$extra");
	exit;
}
?>
