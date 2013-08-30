<?php
include("conn.php"); 
if(isset($_POST["PHPSESSID"])) 
{
       	 session_id($_POST["PHPSESSID"]);
}

session_start();
$input_dir = session_id();
?>

<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
<title></title>
<link href="css/swfupload.css" rel="stylesheet" type="text/css" />
</head>
<body>

<div id="content">
<?php

if($_SESSION["refresh"] == false)
{
	$method = $_POST["element_1"];
	$para = $_POST["element_2"];
	$email = $_POST["element_4"];
	$time=date("Y-m-d H:i:s");
	$timestamp = time();

	$exec="insert into jobs(job_type,job_status,submit_time,parameters,input_dir,email,timestamp) ".
		"values ('$method','0','$time','$para','$input_dir','$email','$timestamp')";
	$ret = mysql_query($exec,$link);

	$_SESSION["refresh"] = true;
	if($ret)
	{
               echo "<script>window.open('status.php?id=$timestamp')</script>";
                echo "<script>window.location='index.php'</script>";

		$to =  $email;
                $from = 'star@wanglab.ucsd.edu';
		$url = $_SERVER['PHP_SELF'];
		$url = "http://alya.ucsd.edu".dirname($url)."/status.php?id=$timestamp"; 
		$messages = "Congratulations!\nYou have submitted a new job to STAR web server at $time.\n\n".
			    "Please visit $url to get job status.\n\nAfter the job is finished, a notify email ".
			    "will be sent to you!\n\nThanks for using STAR!";
		mail($to,"Email notify!",$messages, "From: $from\nX-Mailer: PHP/ . $phpversion()");
	}
	else echo "Failed to submit a new job!";
}
?>
<p><a href="index.php">Submit another Job</a></p>
</div>
</body>
</html>
