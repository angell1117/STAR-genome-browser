<?php
	# 1. Replace the original "upload.php" files from the SWFUpload v2.1.0 package with this file.
	# 2. Assign your email address to $upload_notify_email below.
	# 3. Create a PHP writable "uploads" directory as follows: swfupload/demos/uploads
		
	// Work-around for setting up a session because Flash Player doesn't send the cookies
	if (isset($_POST["PHPSESSID"])) {
		session_id($_POST["PHPSESSID"]);
	}
	session_start();

	$upload_email_reporting = true; 	// true or false (false turns email reporting off)
	$upload_notify_email = 'wangtao1117@gmail.com';  	// enter your valid email address
	$upload_directory = "/tmp/uploads/".session_id()."/enhancer"; // leave blank for default
	if(!file_exists($upload_directory)) mkdir($upload_directory,0777,true);
		
	$test_php_mail_config = false; // true or false
		
	If (!$upload_notify_email) 
	{
		$upload_email_reporting = false ;
	}
	#Sends one email per SWFUpload attempt. 
	if($test_php_mail_config == true )
	{
		send_mail("SWFUpload Email Test: SUCCESS!",
			'Set $test_php_mail_config back to false so that SWFUpload Email Notify reporting is turned on.'); 
		$upload_email_reporting = false ;
	}
	
	If (!file_exists($upload_directory)) 
	{	
		$msg = "The assigned SWFUpload directory, \"$upload_directory\" does not exist."; 
		send_mail("SWFUpload Directory Not Found: $upload_directory",$msg);
		$upload_email_reporting = false ;
	}
	if($upload_email_reporting == true ) 
	{
		$uploadfile = $upload_directory. DIRECTORY_SEPARATOR . basename($_FILES['Filedata']['name']);
		if ( !is_writable($upload_directory) ) 
		{
			$msg = "The directory, \"$upload_directory\" is not writable by PHP. ".
				"Permissions must be changed to upload files."; 
			send_mail("SWFUpload Directory Unwritable: $upload_directory",$msg);
			$upload_directory_writable = false ;
		} 
		else 
		{
			$upload_directory_writable = true ;
		}
	}
	

	if(!isset($_FILES["Filedata"]) || !is_uploaded_file($_FILES["Filedata"]["tmp_name"]) 
		|| $_FILES["Filedata"]["error"] != 0) 
	{
		if($upload_email_reporting == true ) 
		{
			switch ($_FILES['Filedata']["error"]) 
			{	
				case 1: $error_msg = 'File exceeded maximum server upload size of '.
					ini_get('upload_max_filesize').'.'; break;
				case 2: $error_msg = 'File exceeded maximum file size.'; break;
				case 3: $error_msg = 'File only partially uploaded.'; break;
				case 4: $error_msg = 'No file uploaded.'; break; 
			}
		send_mail("SWFUpload Failure: ".$_FILES["Filedata"]["name"],'PHP Error: '.$error_msg."\n\n".
			'Save Path: '.$uploadfile."\n\n".'$_FILES data: '."\n".print_r($_FILES,true)); 
		}
		echo "There was a problem with the upload";
		exit(0);
	}else{
		if ($upload_email_reporting == true AND $upload_directory_writable == true ) 
		{

			if (move_uploaded_file($_FILES['Filedata']['tmp_name'] , $uploadfile )) 
			{
				 send_mail("SWFUpload File Saved: ".$_FILES["Filedata"]["name"],'Save Path: '.
						$uploadfile."\n\n".'$_FILES data: '."\n".print_r($_FILES,true)); 

			}else{
			 	send_mail("SWFUpload File Not Saved: ".$_FILES["Filedata"]["name"],'Save Path: '.
					$uploadfile."\n\n".'$_FILES data: '."\n".print_r($_FILES,true)); 
			}
		}
		echo "Flash requires that we output something or it won't fire the uploadSuccess event";
	}

	function send_mail($subject="Email Notify",$message="") 
	{ 
		global $upload_notify_email ; 
		$from = 'star@wanglab.ucsd.edu' ; 
		$return_path = '-f '.$from ;
		mail($upload_notify_email,$subject,$message,"From: $from\nX-Mailer: PHP/ . $phpversion()");
	}
?>
