<?php
require_once '../config.php';
require_once 'sendmail.php';
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

$user_type=trim($_GET["type"]);  /* parameters */
$user_name=$_GET["user"];
$user_valid=$_GET["uid"];

$query = "select reg_time,email,user_tname from user where user_id = ?";
$stmt = $db->prepare($query);
$stmt->bindParam(1, $user_name);
$stmt->execute() or goBack("Error happened. Please try later.");
$num_rows = $stmt->rowCount();
if ($num_rows == 0) {
	echo "This user does not exist. Confirm failed.";
	exit();
}

$row = $stmt->fetch();
$user_tname = $row['user_tname'];
$user_email = $row['email'];
$user_code0 = base64_encode($row['reg_time']);
$user_code1 = ereg_replace("=","X",$user_code0);
if ("$user_code1" != $user_valid) {
	echo "Confirm failed.";
	exit();
}

$query = "update user set user_status =1 where user_id = ?";
$stmt = $db->prepare($query);
$stmt->bindParam(1, $user_name);
$stmt->execute() or goBack("Error happened. Please try later.");

if ($user_type == 0) {
	$content = "Dear admin,\n\n" .
"New user $user_name just completed registration!\n\n";
	sendMail("New user registered!",$content,"wangtao1117@gmail.com");
	$ret = sendMail("New user registered!",$content,"star@wanglab.ucsd.edu");
	echo "Registration finished successfully. Thank you!<br>";
	echo "Click <a href=\"http://wanglab.ucsd.edu/star/browser/\">here</a> to start genome browser.";
} elseif ($user_type == 1) {
	$content = "Dear $user_tname,\n\n" .
"Your registration was approved.\n\n" .
"To start genome browser, please click the following URL:\n" .
"http://wanglab.ucsd.edu/star/browser\n\n" .
"Thanks for using STAR genome browser system!\n\n";
	sendMail("New registration approved!",$content,"wangtao1117@gmail.com");
	$ret = sendMail("Your registration approved!",$content,$user_email);
	if ($ret == true) {
		echo "Registration was approved, a notice email has been sent out!";
	} else {
		echo "Registration was approved, failed to send out a notice email!";
	}
} elseif ($user_type == 2) {
	$query = "delete from user where user_id = ?";
	$stmt = $db->prepare($query);
	$stmt->bindParam(1, $user_name);
	$stmt->execute() or goBack("Error happened. Please try later.");

	$content = "Dear $user_tname,\n\n" .
"We are sorry to refuse your registration.\n" .
"We suggest you to provide valid information that can identify your access.\n\n" .
"Thanks for using our genome browser service!\n";
	sendMail("Your registration is refused!",$content,"wangtao1117@gmail.com");
	$ret = sendMail("Your registration is refused!",$content,$user_email);
	if($ret == true) {
		echo "Registration is refused, a notice email has been sent out!";
	} else {
		echo "Registration is refused, failed to send a notice email!";
	}
}
?>
