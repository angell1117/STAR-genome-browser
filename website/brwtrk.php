<?php
// Start session.
session_start();

// Authenticate user login.
if(!isset($_SESSION['username']) or !isset($_SESSION['passwd'])){
        http_redirect('illegal.php');
}
?>
<html>
<head>
	<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
	<title>Genome browser</title>

	<!-- ExtJS Dependencies -->
	<link type='text/css' rel='stylesheet' href='http://tabit.ucsd.edu/annoj-js/resources/css/ext-all.css' />
	<script type='text/javascript' src='http://tabit.ucsd.edu/annoj-js/ext-base-3.2-comp.js'></script>
	<script type='text/javascript' src='http://tabit.ucsd.edu/annoj-js/ext-all-3.2-comp.js'></script>

	<!-- Anno-J -->
	<link type='text/css' rel='stylesheet' href='http://www.annoj.org/css/viewport.css' />
	<link type='text/css' rel='stylesheet' href='http://www.annoj.org/css/plugins.css' />
	
	<!-- UPload plugin -->
	<link href="swfupload/css/swfupload.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="swfupload/swfupload/swfupload.js"></script>
	<script type="text/javascript" src="swfupload/js/swfupload.queue.js"></script>
	<script type="text/javascript" src="swfupload/js/fileprogress.js"></script>
	<script type="text/javascript" src="swfupload/js/handlers.js"></script>

	<!-- MainUI -->
	<script type='text/javascript' src='ext-sdec-src.js'></script>
	
	<script type="text/javascript">
	var swfu = null;
	var swfu1 = null;
	global_username = "<?php echo $_SESSION['username']; ?>";
	window.onload = function(){
	x_settings = {
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
	x_settings1 = {
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
	};
	</script>
</head>

<body>

	<!-- Message for people who do not have JS enabled -->
	<noscript>
		<table id='noscript'><tr>
			<td>
				<p>Cannot run because your browser is currently configured to block JavaScript.</p>
				<p>To use the application please access your browser settings or preferences, turn JavaScript support back on, and then refresh this page.</p>
				<p>Thankyou, and enjoy the application!<br />- Julian</p>
			</td>
		</tr></table>
	</noscript>

	<!-- You can insert Google Analytics here if you wish -->

</body>

</html>
