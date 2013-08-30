<html>
<head>
<script language="JavaScript" src="my_js/user.js"></script>
<script language="javascript" type="text/javascript">
function RefreshImage()
{
var el =document.getElementById("img11");
el.src=el.src+'?';
}
</script>

<?php include("my_conn/conn.php");?>
<link rel="stylesheet" href="css/styles.css" />
</head>

<body class="noborder">
<table align="left">
<form name="register" method="POST" onsubmit="return formCheck(this);" action="result.php?action=register">
<tr>
<td class="noborder" height="450" valign="top">
<table cellpadding="0" cellspacing="0" height="450">
<tr>
<td class="noborder" valign="top" align="left"  height="45">
<img border="0" src="img/SDEC_logo.gif" align="left">
</tr>
<tr>
<td class="noborder" valign="top" height="50">
<a href="login.php">STAR</a>&nbsp;-&gt;&nbsp;New user registration
</td>
</tr>
<tr> <td class="noborder" valign="top" height="40" align="left">* Username: <input name="user_name" class="input_out" id="textfield2" onfocus="this.className='input_on';this.onmouseout=''" onblur="this.className='input_off';this.onmouseout=function(){this.className='input_out'};" onmousemove="this.className='input_move'" onmouseover="this.focus();" onmouseout="this.className='input_out'"  size="20"/></td></tr>
<tr> <td class="noborder" valign="top" height="40">* Password: <input type="password" name="user_pwd" id="textfield3" class="input_out" onfocus="this.className='input_on';this.onmouseout=''" onblur="this.className='input_off';this.onmouseout=function(){this.className='input_out'};" onmousemove="this.className='input_move'" onmouseover="this.focus();" onmouseout="this.className='input_out'" size="20"/></td></tr>
<tr><td class="noborder" valign="top" height="40">* Re-enter Password: <input type="password" name="user_pwd_conf" id="textfield4" class="input_out"  size="20" onfocus="this.className='input_on';this.onmouseout=''" onblur="this.className='input_off';this.onmouseout=function(){this.className='input_out'};" onmousemove="this.className='input_move'" onmouseover="this.focus();" onmouseout="this.className='input_out'" /></td></tr>
<tr> <td class="noborder" valign="top" height="40">* Email Address: <input name="user_email" id="textfield1" size="25" class="input_out" onfocus="this.className='input_on';this.onmouseout=''" onblur="this.className='input_off';this.onmouseout=function(){this.className='input_out'};" onmousemove="this.className='input_move'" onmouseover="this.focus();" onmouseout="this.className='input_out'" /></td> </tr>
<tr> <td class="noborder" valign="top" height="40">* Your Name: <input name="user_tname" id="textfield5" size="25" class="input_out"  onfocus="this.className='input_on';this.onmouseout=''" onblur="this.className='input_off';this.onmouseout=function(){this.className='input_out'};" onmousemove="this.className='input_move'" onmouseover="this.focus();" onmouseout="this.className='input_out'" /></td></tr>
<tr> <td class="noborder" valign="top" height="40">* Institute: <input name="user_institute" id="textfield6" class="input_out"  onfocus="this.className='input_on';this.onmouseout=''" onblur="this.className='input_off';this.onmouseout=function(){this.className='input_out'};" onmousemove="this.className='input_move'" onmouseover="this.focus();" onmouseout="this.className='input_out'" size="35" /></td></tr>
<tr> <td class="noborder" valign="top" height="40">* Address: <input name="user_address" id="textfield" class="input_out"  onfocus="this.className='input_on';this.onmouseout=''" onblur="this.className='input_off';this.onmouseout=function(){this.className='input_out'};" onmousemove="this.className='input_move'" onmouseover="this.focus();" onmouseout="this.className='input_out'" size="35" /></td> </tr>
<tr> <td class="noborder" valign="top" align="left">If you want to join a group, provide group name: </td> </tr>
<tr>
<td class="noborder" valign="top" height="40"> <input name="account_group" id="textfield5" value="PUBLIC" size="25" class="input_out"  onfocus="this.className='input_on';this.onmouseout=''" onblur="this.className='input_off';this.onmouseout=function(){this.className='input_out'};" onmousemove="this.className='input_move'" onmouseover="this.focus();this.select();" onmouseout="this.className='input_out'" /></td> </tr>
<tr> <td class="noborder" valign="top">Additional Information:</td> </tr>
<tr> <td class="noborder" valign="top" height="70"> <textarea rows="3" cols="36" name="user_telephone" id="textfield0" class="input_out" onfocus="this.className='input_on';this.onmouseout=''" onblur="this.className='input_off';this.onmouseout=function(){this.className='input_out'};" onmousemove="this.className='input_move'" onmouseover="this.focus();" onmouseout="this.className='input_out'" ></textarea></td> </tr>
<tr> <td class="noborder" valign="top" height="60">* Verification code <input name="passcode" id="textfield1" size="8" class="input_out" onfocus="this.className='input_on';this.onmouseout=''" onblur="this.className='input_off';this.onmouseout=function(){this.className='input_out'};" onmousemove="this.className='input_move'" onmouseover="this.focus();" onmouseout="this.className='input_out'" style="float: left"  /><img id="img11" src=checkimg.php onmouseup="RefreshImage()" alt="Refresh" title="Refresh" align="left"></td> </tr>
<tr>
<td class="noborder" height="45" valign="top">
<input type="submit" name="button" id="button" value="Submit" class="input_out"  onfocus="this.className='input_on';this.onmouseout=''" onblur="this.className='input_off';this.onmouseout=function(){this.className='input_out'};" onmousemove="this.className='input_move'" onmouseover="this.focus();" onmouseout="this.className='input_out'"  />
<input type="reset"  name="button2" id="button2" value="Reset" class="input_out"  onfocus="this.className='input_on';this.onmouseout=''" onblur="this.className='input_off';this.onmouseout=function(){this.className='input_out'};" onmousemove="this.className='input_move'" onmouseover="this.focus();" onmouseout="this.className='input_out'"  />
</td>
</tr>
</table>
</td>
</tr>
</form>
</table>
</body>
</html>
