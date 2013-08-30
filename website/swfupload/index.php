<?php
session_start();
	$_SESSION["refresh"] = false;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
<title>Wig2Bed application</title>
<link href="css/view.css" rel="stylesheet" type="text/css" />
<link href="css/swfupload.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="swfupload/swfupload.js"></script>
<script type="text/javascript" src="js/swfupload.queue.js"></script>
<script type="text/javascript" src="js/fileprogress.js"></script>
<script type="text/javascript" src="js/handlers.js"></script>
<script type="text/javascript" src="../js/menu.js"></script>
<script type="text/javascript">
	var swfu;

	window.onload = function() {
		var settings = {
			flash_url : "swfupload/swfupload.swf",
			upload_url: "upload.php",
			post_params: {"PHPSESSID" : "<?php echo session_id(); ?>"},
			file_size_limit : "1024 MB",
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
			button_image_url: "images/XPButton61x22.png",
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
			queue_complete_handler : queueComplete	// Queue plugin event
		};

		swfu = new SWFUpload(settings);
     };
</script>
</head>
<body>
<div id="header">
</div>

<div id="form_container">
	<form id="form1" class="appnitro" action="submit.php" method="post" enctype="multipart/form-data">
	<div class="fieldset">
		<span class="legend">Format Conversion</span>
		<div>
			<span id="spanButtonPlaceHolder"></span>
			<input id="btnCancel" type="button" value="Cancel All Uploads" onclick="swfu.cancelQueue();" 
			disabled="disabled" style="margin-left: 2px; font-size: 8pt; height: 22px;" />
			<div id="divStatus">0 Files Uploaded</div>
		</div>
		<div class="fieldset flash" id="fsUploadProgress"> </div>
		<ul>
		 <li id="li_1" >
                <label class="description" for="element_1">Format conversion:</label>
                <span>
                    <input id="element_1_1" name="element_1" class="element radio" type="radio" value="wig2bed"/>
                    <label class="choice" for="element_1_1">Wig to Bed</label>
                    <input id="element_1_2" name="element_1" class="element radio" type="radio" value="eland2bed"/>
                    <label class="choice" for="element_1_2">Eland to Bed</label>
                    <input id="element_1_3" name="element_1" class="element radio" type="radio" value="bowtie2bed"/>
                    <label class="choice" for="element_1_3">Bowtie to Bed</label>
                    <input id="element_1_4" name="element_1" class="element radio" type="radio" value="bed2bed"/>
                    <label class="choice" for="element_1_4">Bed to Bed
                    <input id="element_2" name="element_2" class="element text medium" type="text" maxlength="255"/>
                    </label>
		</span>
                </li>
 
		<li id="li_4" >
                <label class="description" for="element_4">Email address</label>
                <div>
                    <input id="element_4" name="element_4" class="element text medium" type="text" maxlength="255" value="@"/>
                </div>
                </li>
                <li class="buttons">
                        <input type="hidden" name="form_id" value="147891" />
                </li>
                </ul>
			<input type="submit" value="Submit" id="btnSubmit" />
	</div>
	</form>
</div>
</body>
</html>
