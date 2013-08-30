<?php
require_once 'config.php';
require_once 'subs.php';
require_once 'my_inc/sendmail.php';
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
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>My profile</title>
<link rel="stylesheet" href="css/styles.css" />
<script language="JavaScript" src="my_js/user.js"></script>
</head>
<body class="noborder">
<?php
if(isset($_POST['save'])) {
	$user_name = $_POST['user_name'];
	if (empty($user_name)) {
		goBack("Please input the user ID.");
	}
	$user_email = $_POST['user_email'];
	if (empty($user_email)) {
		goBack("Please input the email address.");
	}
	$query = "select user_tname, EMAIL from user where USER_ID = ?";
	$stmt = $db->prepare($query);
	$stmt->bindParam(1, $user_name);
	$stmt->execute() or goBack("Error happened. Please try later.");
	$row = $stmt->fetch();
	$temail = $row['EMAIL'];
	$user_tname = $row['user_tname'];
	if (empty($temail)) {
		echo "This user id doesn't exist. <a href=register.php>Register here?</a>";
	} elseif (strtoupper($user_email) != strtoupper($temail)) {
		goBack("The user id and email address do not match.");
	} else {
		$new_passwd = genPassword();
		$query = "update user set PASSWD='$new_passwd' where USER_ID = ?";
		$stmt = $db->prepare($query);
		$stmt->bindParam(1, $user_name);
		$stmt->execute() or goBack("Error happened. Please try later.");
		$content = "Dear $user_tname:\n\nYour new password is reset to $new_passwd now. Please change it after you login.\nTo start genome browser, please click the following URL:\nhttp://wanglab.ucsd.edu/star/browser\n\nThank you!";
		$ret = sendMail("Your password is reset!",$content,$temail);
		if($ret == true) {
			print "Your information has been submitted successfully!<BR>";
			print "Please check your email to find your new password.<BR>Thank you!";
		} else {
			print "Failed to send email to you!<BR>";
		}
	}
	exit;
}
?>
<table align="center">
<tr><td class="noborder" align=center height=300>
<img alt="SDEC logo" src="img/SDEC_logo.gif" />
</td><tr>
<tr>
<td class="noborder" valign="top" height="75" align="center">
<a href="login.php">STAR</a>&nbsp;-&gt;Forget the password
</tr>
<tr><td class="noborder" align=center>
<form method="post" onsubmit="return formCheck(this);" action="forgetpasswd.php">
<input type="hidden" name="save" value="1" />
<table align="center">
<tr><td class="noborder" align="right">User ID:&nbsp;</td><td class="noborder"><input type="text" name="user_name" size="20" onmouseover="this.focus();"></td></tr>
<tr><td class="noborder" align="right">Email:&nbsp;</td><td class="noborder"><input type="text" name="user_email" size="20" onmouseover="this.focus();"></td></tr>
<tr><td class="noborder"></td>
<td class="noborder" height="45" valign="top">
<input type="submit" name="button" id="button" value="Reset" class="input_out"  onfocus="this.className='input_on';this.onmouseout=''" onblur="this.className='input_off';this.onmouseout=function(){this.className='input_out'};" onmousemove="this.className='input_move'" onmouseout="this.className='input_out'"  />
&nbsp;&nbsp;&nbsp;
<input type="reset"  name="button2" id="button2" value="Reset" class="input_out"  onfocus="this.className='input_on';this.onmouseout=''" onblur="this.className='input_off';this.onmouseout=function(){this.className='input_out'};" onmousemove="this.className='input_move'" onmouseout="this.className='input_out'"  />
</td>
</tr>
</table>
</form>
</table>
</body>
</html>
