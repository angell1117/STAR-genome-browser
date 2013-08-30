<?php
require_once 'subs.php';
// Start session.
session_start();

// Authenticate user login.
if(!isset($_SESSION['username']) or !isset($_SESSION['passwd'])){
	http_redirect('illegal.php');
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Tutorial</title>
<link rel="stylesheet" href="css/styles.css" />
</head>

<body>
<table>
<tr>
<td class="noborder" width="300"> <img src="img/SDEC logo small.gif" alt="SDEC logo small" style="border: 0;" /> </td>
<td class="noborder"> Welcome,
<?php
$username = $_SESSION['username'];
if (preg_match('/guest/i', $username)) {
	echo "Guest!&nbsp;&nbsp;&nbsp;My Profile&nbsp;|&nbsp;<a href=\"mailto:star@wanglab.ucsd.edu\" target=_self>Contact Us</a>&nbsp;|&nbsp;<a href=\"logout.php\" target=_self>Login</a>";
} else {
	echo "$username!&nbsp;&nbsp;&nbsp;<a href=\"profile.php\" target=_self>My Profile</a>&nbsp;|&nbsp;<a href=\"mailto:star@wanglab.ucsd.edu\" target=_self>Contact Us</a>&nbsp;|&nbsp;<a href=\"logout.php\" target=_self>Logout</a>";
}
?>
</td>
</tr>
</table>
<table cellpadding="0" cellspacing="0">
<tr>
        <td class="noborder" valign=top style="background:url('img/panel_top.gif') no-repeat;" width=200 height="33"> </td>
        <td class="noborder"></td>
</tr>
<tr>
        <td class="noborder" valign=top style="background:url('img/panel.gif') repeat-y;" width=200 height=450>
        <div style= "font-size:16px;height:30px;width:170px" align=center> <A class="c1" href="brwconf.php" target=_self>Browse Configs</a> </div>
        <div style= "font-size:16px;height:30px;width:170px" align="center"> <a class="c1" href="brwtrk.php" target=_self>Browse Tracks</a> </div>
		<div style= "font-size:16px;height:30px;width:170px" align="center"> <a class="c1" href="brwsdec.php" target=_self>SDEC Tracks</a> </div>
        <div style= "font-size:16px;height:30px;width:170px" align="center"> <a class="c1" href="subtracks.php" target=_self>New Tracks</a> </div>
        <div style= "font-size:16px;height:30px;width:170px" align="center"> <a class="c1" href="tutorial.php" target=_self>Tutorial</a> </div>
        </td>
        <td class="noborder" valign=top>
	<td class="noborder" valign=top>
	<h3><a href="#conf">About Configuration:</a></h3>
	<h4><font color=#FF8888><a href="#create">1. Create a configuration</a></font></h4>
	<h4><font color=#FF8888><a href="#modify">2. Modify a configuration</font></a></h4>
	<h4><font color=#FF8888><a href="#view">3. View a configuration</font></a></h4>
	<h3><a href="#track">About Track:</a></h3>
	<h4><font color=#FF8888><a href="#paste">1. Paste your own track</font></a></h4>
	<h4><font color=#FF8888><a href="#update">2. Modify your own track</font></a></h4>
	<h1><a name="conf"></a>About Configuration:</h1>
	<h2><font color=#FF8888><a name="create"></a>1. Create a configuration</font></h2>
	<h3>Click the &quot;Create A New Configuration&quot; button, select the tracks from the table by using check boxes, and click the &quot;Create A New Configuration&quot; button. A new configuration, named new_configuration, will be automatically created and highlighted.</h3>
	<h3>You can click on column header table to sort the table and/or use the &quot;Search&quot; button to search.</h3>
	<img src="img/tutorial_start_new.png" width="951" height="274"/>
	<img src="img/tutorial_select_new.png" width="952" height="882"/>
	<img src="img/tutorial_new_new.png" width="952" height="513"/>
	<h2><font color=#FF8888><a name="modify"></a>2. Modify a configuration</font></h2>
	<h3>Click the configuration and you can edit the configuration. You can delete any track and/or add new tracks from the configuration. Also you can merge two or more configurations into one new configuration, named &quot;com_configuration&quot;. </h3>
	<img src="img/tutorial_modify_new.png" width="952" height="798"/>
	<img src="img/tutorial_addmore_new.png" width="952" height="798"/>
	<img src="img/tutorial_merge_new.png" width="952" height="593"/>
	<h2><font color=#FF8888><a name="view"></a>3. View a configuration</font></h2>
	<h3>Click the &quot;View&quot; button and you can visualize the parallel sequence data by using Anno-J. This is a tutorial to Anno-J.</h3>
	<h3><a href="http://neomorph.salk.edu/index.html">http://neomorph.salk.edu/index.html</a></h3>
	<img src="img/tutorial_view_new.png" width="952" height="616"/>
	<h1><a name="track"></a>About Track:</h1>
	<h2><font color=#FF8888><a name="paste"></a>1. Paste your own track</font></h2>
	<h3>Click the &quot;Paste Tracks&quot; link, you can paste your own track by following the instruction in this page. Access &quot;Public&quot; means everyone can view your track. Access &quot;Private&quot; means no one but you can view your track. Access &quot;Group&quot; means every one in the same group with you can view the track, if you belong to a group when you register. Furthermore, you can grant access to anyone and/or any group.</h3>
	<img src="img/tutorial_submit_new.png" width="950" height="887"/>
	<h2><font color=#FF8888><a name="update"></a>2. Modify your own track</font></h2>
	<h3>If you are the owner of a track, you can update the information and attribute of this track by clicking the track.</h3>
	<img src="img/tutorial_update_new.png" width="952" height="742"/>
	<h3></h3>
	<h1></h1>
	<h2><font color=#FF8888></font></h2>
</td>
</tr>
<tr>
        <td class="noborder" valign=top style="background:url('img/panel_bottom.gif') no-repeat;" width=200 height="37">
        </td>
        <td class="noborder"></td>
</tr>
</table>
</body>
</html>
