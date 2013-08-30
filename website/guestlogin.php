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

//exist guest relogin
$ipaddr = $_SERVER["REMOTE_ADDR"];
$ipaddr = preg_replace('/\./','',$ipaddr);
$user_name = "guest$ipaddr";

$query = "select user_id, passwd, account_group FROM user WHERE USER_ID = '$user_name'";
$stmt = $db->query($query);
if(!$stmt) goBack("Error happened. Please try later.");
while($row = $stmt->fetch()) 
{
   $_SESSION['username'] = $row['user_id'];
   $_SESSION['passwd'] = $row['passwd'];
   $_SESSION['usergrp'] = $row['account_group'];
   $host  = $_SERVER['HTTP_HOST'];
   $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
   $extra = 'brwtrk.php';
   header("Location: http://$host$uri/$extra");
   exit;
}

$query = "select USER_ID from usertrack where USER_ID = '$user_name'";
$stmt = $db->query($query);
if(!$stmt) goBack("Error happened. Please try later.");
while($row = $stmt->fetch()) 
{
   $query = "delete from usertrack where USER_ID = ?";
   $stmt = $db->prepare($query);
   $stmt->bindParam(1, $user_name);
   $stmt->execute() or goBack("Error happened. Please try later.");
   break;
}

$query = "select track_id from track where access='public'";
$stmt = $db->prepare($query);
$stmt->execute() or goBack("Error happened. Please try later.");
while ($row = $stmt->fetch()) {
   $allids[$row["track_id"]] = $row["track_id"];
}

$pwd = '123456';
$user_tname='NA';
$user_telephone='NA';
$user_email='NA';
$user_address='NA';
$user_institute='NA';
$reg_type=1;
$account_group = "PUBLIC";
$group_id = 0;
$registtime=date("Y-m-d H:i:s");

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
$stmt->execute() or goBack("Error happened. Please try later.".$str);
//print_r($stmt->errorInfo());

$upuser_name = strtoupper($user_name);
$allidslist = implode(',', $allids);
$query = "insert into usertrack (user_id, track_list) values (?, ?)";
$stmt = $db->prepare($query);
$stmt->bindParam(1, $upuser_name);
$stmt->bindParam(2, $allidslist);
$stmt->execute() or goBack("Error happened. Please try later.");

$_SESSION['username'] = $user_name;
$_SESSION['passwd'] = $pwd;
$_SESSION['usergrp'] = $account_group;
$host  = $_SERVER['HTTP_HOST'];
$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
$extra = 'brwtrk.php';
header("Location: http://$host$uri/$extra");
exit;
?>
