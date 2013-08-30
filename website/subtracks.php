<?php
require_once 'confsubs.php';	// database configuration.
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
<title>Paste new tracks</title>
<script language="JavaScript" src="my_js/user.js"></script>
<link rel="stylesheet" href="css/styles.css" />
<link href="swfupload/css/view.css" rel="stylesheet" type="text/css" />
<link href="swfupload/css/swfupload.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="swfupload/swfupload/swfupload.js"></script>
<script type="text/javascript" src="swfupload/js/swfupload.queue.js"></script>
<script type="text/javascript" src="swfupload/js/fileprogress.js"></script>
<script type="text/javascript" src="swfupload/js/handlers.js"></script>

<script type="text/javascript">
        var swfu,swfu1;
        window.onload = function() {
                var settings = {
                        flash_url : "swfupload/swfupload/swfupload.swf",
                        upload_url: "swfupload/upload.php",
                        post_params: {"PHPSESSID" : "<?php echo session_id(); ?>"},
                        file_size_limit : "1824 MB",
                        file_types : "*.*",
                        file_types_description : "All Files",
                        file_upload_limit : 100,
                        file_queue_limit : 0,
                        custom_settings : {
                                progressTarget : "fsUploadProgress",
                                cancelButtonId : "btnCancel"
                        },
                        debug: false,

                        // Button settings
                        button_image_url: "swfupload/images/XPButton61x22.png",
                        button_width: "61",
                        button_height: "22",
                        button_placeholder_id: "spanButtonPlaceHolder",

                        swfupload_loaded_handler : swfUploadLoaded,
                        // The event handler functions are defined in handlers.js
                        file_queued_handler : fileQueued,
                        file_queue_error_handler : fileQueueError,
                        file_dialog_complete_handler : fileDialogComplete,
                        upload_start_handler : uploadStart,
                        upload_progress_handler : uploadProgress,
                        upload_error_handler : uploadError,
                        upload_success_handler : uploadSuccess,
                        upload_complete_handler : uploadComplete,
                        queue_complete_handler : queueComplete  // Queue plugin event
                };
                var settings1 = {
                        flash_url : "swfupload/swfupload/swfupload.swf",
                        upload_url: "swfupload/upload.php",
                        post_params: {"PHPSESSID" : "<?php echo session_id(); ?>"},
                        file_size_limit : "1824 MB",
                        file_types : "*.*",
                        file_types_description : "All Files",
                        file_upload_limit : 100,
                        file_queue_limit : 0,
                        custom_settings : {
                                progressTarget : "fsUploadProgress",
                                cancelButtonId : "btnCancel1"
                        },
                        debug: false,

                        // Button settings
                        button_image_url: "swfupload/images/XPButton61x22.png",
                        button_width: "61",
                        button_height: "22",
                        button_placeholder_id: "spanButtonPlaceHolder1",

                        swfupload_loaded_handler : swfUploadLoaded,
                        // The event handler functions are defined in handlers.js
                        file_queued_handler : fileQueued,
                        file_queue_error_handler : fileQueueError,
                        file_dialog_complete_handler : fileDialogComplete,
                        upload_start_handler : uploadStart,
                        upload_progress_handler : uploadProgress,
                        upload_error_handler : uploadError,
                        upload_success_handler : uploadSuccess,
                        upload_complete_handler : uploadComplete,
                        queue_complete_handler : queueComplete  // Queue plugin event
                };

                swfu = new SWFUpload(settings);
                swfu1 = new SWFUpload(settings1);
     };
</script>

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
        <?php
		echo submit_track();
        ?>
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
