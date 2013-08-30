<?php
require_once 'config.php';
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

session_start();

$code=$_POST["passcode"];
if ($code != $_SESSION["Checknum"]) {
	goBack("Invalid verification code.");
}
$user_name=$_POST["user_name"];
if (empty($user_name)) {
	goBack("Empty Username.");
}
if (strlen($user_name)>40) {
	goBack("Username is too long (max: 40 chars).");
}
if (!preg_match('/^[0-9a-zA-Z]+$/', $user_name)) {
	goBack("Username can only contain alphanumeric characters.");
}
if (preg_match('/group$/i', $user_name)) {
	goBack("Username can not end with \"group\" .");
}
if (preg_match('/guest/i', $user_name)) {
	goBack("Username can not contain \"guest\" .");
}
$query = "select USER_ID FROM user WHERE USER_ID = ?";
$stmt = $db->prepare($query);
$stmt->bindParam(1, $user_name);
$stmt->execute() or goBack("Error happened. Please try later.");
if ($stmt->rowCount() != 0) {
	goBack("User already exists. Please try another one.");
}
$query = "select GROUP_NAME FROM usrgroup WHERE GROUP_NAME = ?";
$stmt = $db->prepare($query);
$stmt->bindParam(1, $user_name);
$stmt->execute() or goBack("Error happened. Please try later.");
if ($stmt->rowCount() != 0) {
	goBack("You can not use this username. Please try another one.");
}
$query = "select USER_ID from usertrack where USER_ID = ?";
$stmt = $db->prepare($query);
$stmt->bindParam(1, $user_name);
$stmt->execute() or goBack("Error happened. Please try later.");
if ($stmt->rowCount() != 0) {
	$query = "delete from usertrack where USER_ID = ?";
	$stmt = $db->prepare($query);
	$stmt->bindParam(1, $user_name);
	$stmt->execute() or goBack("Error happened. Please try later.");
}
$query = "select track_id from track where access='public'";
$stmt = $db->prepare($query);
$stmt->execute() or goBack("Error happened. Please try later.");
while ($row = $stmt->fetch()) {
	$allids[$row['track_id']] = $row['track_id'];
}

$user_pwd=$_POST["user_pwd"];
$user_pwd_conf=$_POST["user_pwd_conf"];
if ((empty($user_pwd)) or (empty($user_pwd_conf))) {
	goBack("Empty password.");
}
if ($user_pwd != $user_pwd_conf) {
	goBack("Invalid password.");
}

//$pwd=md5($user_pwd);
$pwd = $user_pwd;

$user_tname=$_POST["user_tname"];
if (empty($user_tname)) {
	goBack("Empty Name.");
}
$user_institute=$_POST["user_institute"];
if (empty($user_institute)) {
	goBack("Empty Institute.");
}
$user_address=$_POST["user_address"];
if (empty($user_address)) {
	goBack("Empty Address.");
}
$user_email=$_POST["user_email"];
if (empty($user_email)) {
	goBack("Empty Email.");
}
$user_telephone=$_POST["user_telephone"];
$registtime=date("Y-m-d H:i:s");


$account_group=$_POST["account_group"];
if ($account_group == "") {
	$account_group = "PUBLIC";
} else {
	$account_group = strtoupper($account_group);
}
switch ($account_group) {
//case "SDEC":
case "REVIEWER":
	goBack("This group does not exist any longer.");
	break;
case "PUBLIC":
	$reg_type = 0;
	$group_id = 0;
	break;
default:
	$query = "select group_id from usrgroup where group_name = ?";
	$stmt = $db->prepare($query);
	$stmt->bindParam(1, $account_group);
	$stmt->execute() or goBack("Error happened. Please try later.");
	if ($stmt->rowCount() == 0) {
		goBack("This group does not exist!");
	}
	$row = $stmt->fetch();
	$reg_type = -1;
	$group_id = $row['group_id'];
	$query = "select track_id from track where access='group' and user_id in (select user_id from user where GROUP_ID = ?)";
	$stmt = $db->prepare($query);
	$stmt->bindParam(1, $group_id);
	$stmt->execute() or goBack("Error happened. Please try later.");
	while ($row = $stmt->fetch()) {
		$allids[$row['track_id']] = $row['track_id'];
	}
	$query = "select group_name from sdecgroup where group_name = ?";
	$stmt = $db->prepare($query);
	$stmt->bindParam(1, $account_group);
	$stmt->execute() or goBack("Error happened. Please try later.");
	if ($stmt->rowCount() > 0) {
		$query = "select track_id from track where access='group' and user_id in (select user_id from user where GROUP_ID = 2)";
		$stmt = $db->prepare($query);
	$stmt->execute() or goBack("Error happened. Please try later.");
		while ($row = $stmt->fetch()) {
			$allids[$row['track_id']] = $row['track_id'];
		}
	}
}

$query = "insert into user (user_id,passwd,user_tname,memo,email,user_address,user_institute,user_status,account_group,group_id,reg_time,modify_time) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $db->prepare($query);
$stmt->bindParam(1, $user_name);
$stmt->bindParam(2, $pwd);
$stmt->bindParam(3, $user_tname);
$stmt->bindParam(4, $user_telephone);
$stmt->bindParam(5, $user_email);
$stmt->bindParam(6, $user_address);
$stmt->bindParam(7, $user_institute);
$stmt->bindParam(8, $reg_type);
$stmt->bindParam(9, $account_group);
$stmt->bindParam(10, $group_id);
$stmt->bindParam(11, $registtime);
$stmt->bindParam(12, $registtime);
$stmt->execute() or goBack("Error happened. Please try later.");

$upuser_name = strtoupper($user_name);
$allidslist = implode(',', $allids);
$query = "insert into usertrack (user_id, track_list) values (?, ?)";
$stmt = $db->prepare($query);
$stmt->bindParam(1, $upuser_name);
$stmt->bindParam(2, $allidslist);
$stmt->execute() or goBack("Error happened. Please try later.");

$code0 = base64_encode($registtime);
$code1 = ereg_replace("=","X",$code0);

if ($reg_type == 0) {
	$content = "Dear $user_tname:\n\n" .
"Thanks for your registration to STAR genome browser!\n\n" .
"Your account is:$user_name\n" .
"To complete your registration, just click the following URL:\n" .
"http://tabit.ucsd.edu/sdec/my_inc/register_confirm.php?type=0&user=$user_name&uid=$code1\n\n" .
"If you have any questions, please reply this email.\n";
	$ret = sendMail("Genome browser registration!",$content,$user_email);
} elseif($reg_type == -1) {
	$content = "Dear $user_tname:\n\n" .
"Thanks for your registration to STAR genome browser!\n\n" .
"Your account is:$user_name\n" .
"You need administrator's approval to activate your account and to access group's data.\n" .
"After your registration is approved, an email will be sent to you.\n" .
"If you have any questions, please reply this email.\n";
	$ret = sendMail("Genome browser registration!",$content,$user_email);

//send email to administrator
	$content1 = "Dear admin:\n\n" .
"A new registration to STAR genome browser is comming!\n\n" .
"Users information:\n" .
"Account:\t$user_name\n" .
"Name:\t$user_tname\n" .
"Address:\t$user_address\n" .
"Institute:\t$user_institute\n" .
"E-mail:\t $user_email\n" .
"Memo:\t$user_telephone\n" .
"Group:\t$account_group\n\n" .
"To approve the registration, just click the following URL:\n" .
"http://tabit.ucsd.edu/sdec/my_inc/register_confirm.php?type=1&user=$user_name&uid=$code1\n\n".
"To refuse the registration, just click the following URL:\n" .
"http://tabit.ucsd.edu/sdec/my_inc/register_confirm.php?type=2&user=$user_name&uid=$code1\n\n";
	sendMail("New registration is comming!",$content1,"star@wanglab.ucsd.edu") or goBack("Send email to manager failed!");
}
if ($ret == true) {
	print "Your information has been submitted successfully.<BR>";
	print "A confirm email has been sent to you.<BR>";
	print "Please follow the instructions in your email to complete registration.<BR>Thank you!";
} else {
	print "Failed to send email! Registration failed!<BR>";
}
?>
