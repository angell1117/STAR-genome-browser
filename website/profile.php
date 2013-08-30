<?php
require_once 'config.php';
require_once 'confsubs.php';
require_once 'subs.php';

// Start the session
session_start();

// Authenticate user login.
if(!isset($_SESSION['username']) or !isset($_SESSION['passwd'])){
	http_redirect('illegal.php');
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>My profile</title>
<link rel="stylesheet" href="css/styles.css" />
</head>

<body class="noborder">
<?php
$username = $_SESSION['username'];
$query = "select * from user,usrgroup where user.GROUP_ID=usrgroup.GROUP_ID and USER_ID = ?";
$stmt = $db->prepare($query);
$stmt->bindParam(1, $username);
$stmt->execute() or goBack("Error happened. Please try later.");
$row = $stmt->fetch();
if(isset($_POST['save'])){
	$se_passwd = $_SESSION['passwd'];	// Read password from session.
	// Read user submitted changes.
	$new_passwd = $_POST['new_passwd'];
	$new_passwd2 = $_POST['new_passwd2'];
	if (empty($new_passwd)) {
		$new_passwd = $se_passwd;
	} elseif($new_passwd != $new_passwd2){
		goBack("The two new passwords do not match!");
	}
	$query = "update user set PASSWD = ? where USER_ID = ?";
	$stmt = $db->prepare($query);
	$stmt->bindParam(1, $new_passwd);
	$stmt->bindParam(2, $username);
	$stmt->execute() or goBack("Error happened. Please try later.");

	$EMAIL = $_POST['user_EMAIL'];
	if (!empty($EMAIL)) {
		$query = "update user set EMAIL = ? where USER_ID = ?";
		$stmt = $db->prepare($query);
		$stmt->bindParam(1, $EMAIL);
		$stmt->bindParam(2, $username);
		$stmt->execute() or goBack("Error happened. Please try later.");
	}

	$USER_TNAME = $_POST['USER_TNAME'];
	if (!empty($USER_TNAME)) {
		$query = "update user set USER_TNAME = ? where USER_ID = ?";
		$stmt = $db->prepare($query);
		$stmt->bindParam(1, $USER_TNAME);
		$stmt->bindParam(2, $username);
		$stmt->execute() or goBack("Error happened. Please try later.");
	}
	$USER_INSTITUTE = $_POST['USER_INSTITUTE'];
	if (!empty($USER_INSTITUTE)) {
		$query = "update user set USER_INSTITUTE = ? where USER_ID = ?";
		$stmt = $db->prepare($query);
		$stmt->bindParam(1, $USER_INSTITUTE);
		$stmt->bindParam(2, $username);
		$stmt->execute() or goBack("Error happened. Please try later.");
	}

	$USER_ADDRESS = $_POST['USER_ADDRESS'];
	if (!empty($USER_ADDRESS)) {
		$query = "update user set USER_ADDRESS = ? where USER_ID = ?";
		$stmt = $db->prepare($query);
		$stmt->bindParam(1, $USER_ADDRESS);
		$stmt->bindParam(2, $username);
		$stmt->execute() or goBack("Error happened. Please try later.");
	}

	$USER_TELEPHONE = $_POST['USER_TELEPHONE'];
	if (!empty($USER_TELEPHONE)) {
		$query = "update user set USER_TELEPHONE = ? where USER_ID = ?";
		$stmt = $db->prepare($query);
		$stmt->bindParam(1, $USER_TELEPHONE);
		$stmt->bindParam(2, $username);
		$stmt->execute() or goBack("Error happened. Please try later.");
	}

	$_SESSION['passwd'] = $new_passwd;
	$host  = $_SERVER['HTTP_HOST'];
	$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
	$extra = 'profile.php?open_status=1';
	header("Location: http://$host$uri/$extra");
	exit;
}
?>
<form action="profile.php" method="post">
<input type="hidden" name="save" value="1" />
<table cellpadding="0" cellspacing="0" width="450" height="450">
<tr><td class="noborder"><input type='submit' value='Update' /></td></tr>
<tr>
<td class="noborder" valign="top" nowrap>
<font face="Arial" color=red><b>
<?php
if (!empty($_GET["open_status"])) {
	echo "Update Successfully. Thank you!";
} else {
	echo "&nbsp;";
}
?>
</b>
</font></td></tr>
<tr>
<td class="noborder" valign="top">
<font face="Arial"><b>User ID: <?php echo $row['USER_ID'] ?></b>
</font></td></tr>
<tr>
<td class="noborder" valign="top">
<font face="Arial"><b>Group Name: <?php echo strtoupper($row['GROUP_NAME']); ?></b>
</font></td></tr>
<tr>
<td class="noborder" valign="top">
<font face="Arial">Password:
</font></td></tr>
<tr>
<td class="noborder" valign="top" height="40">
<font face="Arial">
<input type="password" name="new_passwd" id="textfield3" class="input_out"  size="25" onfocus="this.className='input_on';this.onmouseout=''" onblur="this.className='input_off';this.onmouseout=function(){this.className='input_out'};" onmousemove="this.className='input_move'" onmouseover="this.focus();" onmouseout="this.className='input_out'" style="float: left" />
</font></td></tr>
<tr>
<td class="noborder" valign="top">
<font face="Arial">Re-enter Password:
</font></td></tr>
<tr>
<td class="noborder" valign="top" height="40">
<font face="Arial">
<input type="password" name="new_passwd2" id="textfield4" class="input_out"  size="25" onfocus="this.className='input_on';this.onmouseout=''" onblur="this.className='input_off';this.onmouseout=function(){this.className='input_out'};" onmousemove="this.className='input_move'" onmouseover="this.focus();" onmouseout="this.className='input_out'" style="float: left" />
</font></td></tr>
<tr>
<td class="noborder" valign="top" width="450" >
<font face="Arial">Valid Email Address:
</font></td></tr>
<tr>
<td class="noborder" valign="top" align="right" width="450" height="40">
<font face="Arial">
<input name="user_EMAIL" value="<?php echo $row['EMAIL']; ?>" id="textfield1" size="25" class="input_out" onfocus="this.className='input_on';this.onmouseout=''" onblur="this.className='input_off';this.onmouseout=function(){this.className='input_out'};" onmousemove="this.className='input_move'" onmouseover="this.focus();this.select();" onmouseout="this.className='input_out'" style="float: left"  />
</font></td></tr>
<tr>
<td class="noborder" valign="top">
<font face="Arial">Your Name:
</font></td></tr>
<tr>
<td class="noborder" valign="top" align="right" height="40">
<font face="Arial">
<input name="USER_TNAME" value="<?php echo $row['USER_TNAME']; ?>" id="textfield5" size="25" class="input_out"  onfocus="this.className='input_on';this.onmouseout=''" onblur="this.className='input_off';this.onmouseout=function(){this.className='input_out'};" onmousemove="this.className='input_move'" onmouseover="this.focus();this.select();" onmouseout="this.className='input_out'" style="float: left"  />
</font></td></tr>
<tr>
<td class="noborder" valign="top">
<font face="Arial">Institute:
</font></td></tr>
<tr>
<td class="noborder" valign="top" height="60">
<textarea rows="2" cols="36" name="USER_INSTITUTE" id="textfield6" class="input_out"  onfocus="this.className='input_on';this.onmouseout=''" onblur="this.className='input_off';this.onmouseout=function(){this.className='input_out'};" onmousemove="this.className='input_move'" onmouseover="this.focus();this.select();" onmouseout="this.className='input_out'"><?php echo $row['USER_INSTITUTE']; ?></textarea>
</td></tr>
<tr>
<td class="noborder" valign="top">
<font face="Arial">Address:
</font></td></tr>
<tr>
<td class="noborder" valign="top" height="60">
<textarea rows="2" cols="36" name="USER_ADDRESS" id="textfield" class="input_out"  onfocus="this.className='input_on';this.onmouseout=''" onblur="this.className='input_off';this.onmouseout=function(){this.className='input_out'};" onmousemove="this.className='input_move'" onmouseover="this.focus();this.select();" onmouseout="this.className='input_out'" ><?php echo $row['USER_ADDRESS']; ?></textarea>
</td></tr>
<tr>
<td class="noborder" valign="top">
<font face="Arial">Additional information:
</font></td></tr>
<tr>
<td class="noborder" valign="top" height="60">
<textarea rows="2" cols="36" name="USER_TELEPHONE" id="textfield0" class="input_out" onfocus="this.className='input_on';this.onmouseout=''" onblur="this.className='input_off';this.onmouseout=function(){this.className='input_out'};" onmousemove="this.className='input_move'" onmouseover="this.focus();this.select();" onmouseout="this.className='input_out'" ><?php echo $row['USER_TELEPHONE']; ?></textarea>
</td></tr>
<tr><td class="noborder"><input type='submit' value='Update' /></td></tr>
</table>
</form>
</body>
</html>
