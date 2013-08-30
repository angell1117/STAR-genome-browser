<html>
<head>
<link rel=stylesheet type=text/css href="../css/style_formats.css" />
<meta http-equiv="refresh" content="30" >
</head>

<?php 
	include("conn.php"); 

	if(!isset($_GET["id"]))
	{
		echo "Can't stat your job status!";
		exit;	
	}
	$id = $_GET["id"];
	$q = "SELECT job_type,parameters,job_status,submit_time,finished_time,email,ret_code FROM jobs where timestamp=$id"; 
	$rs = mysql_query($q, $link);
	$row = mysql_fetch_object($rs);
	if(!$row) exit;

	if($row->job_status == '0') $status = "SUBMITTED";
	if($row->job_status == '1') $status = "RUNNING";
	if($row->job_status == '2') $status = "COMPLETED";
	if($row->job_status == '3') $status = "FAILED";
	if($row->job_status == '4') $status = "EXCEPTION";

	$end_time = trim($row->finished_time);
	if($end_time == null || $end_time == "") $end_time = "N/A";
?> 
<p><font face="Times New Roman" style="font-size: 26pt">About the service</font></p>
<table border="3" width="35%" id="table1" height="287">
	<tr>
		<td><font face="Times New Roman" style="font-size: 16pt">Service ID</font></td>
		<td width="60%"><font face="Times New Roman" style="font-size: 16pt">
		<?php echo $id; ?></font></td>
	</tr>
	<tr>
		<td><font face="Times New Roman" style="font-size: 16pt">Service type</font></td>
		<td width="60%"><font face="Times New Roman" style="font-size: 16pt">
		<?php echo $row->job_type; ?></font></td>
	</tr>
	<tr>
		<td><font face="Times New Roman" style="font-size: 16pt">Service status</font></td>
		<td width="60%"><font face="Times New Roman" style="font-size: 16pt" color=#ff0000>
		<?php echo $status; ?></font></td>
	</tr>
	<tr>
		<td><font face="Times New Roman" style="font-size: 16pt">Time submitted</font></td>
		<td width="60%"><font face="Times New Roman" style="font-size: 16pt">
		<?php echo $row->submit_time;?></font></td>
	</tr>
	<tr>
		<td><font face="Times New Roman" style="font-size: 16pt">Email address</font></td>
		<td width="60%"><font face="Times New Roman" style="font-size: 16pt">
		<?php echo $row->email;?></font></td>
	</tr>
	<tr>
		<td><font face="Times New Roman" style="font-size: 16pt">Time finished</font></td>
		<td width="60%"><font face="Times New Roman" style="font-size: 16pt">
		<?php echo $end_time;?></font></td>
	</tr>
</table>
<p>.</p>
<?php
	if($row->job_status == 2 && trim($row->job_type) == "wig2bed")
	{
?>
<p><font face="Times New Roman" style="font-size: 26pt">Result</font></p>
<p><a href=<?php echo "../job_results/$id/wg.txt.out";?>><font face="Times New Roman" style="font-size: 16pt">Converted BED format file</font></a></p>
<?php
	echo "<pre>";
	system("head -n 200 ../job_results/$id/wg.txt.out");
	echo "</pre>";
	}
?>

<?php
        if($row->job_status == 2 && trim($row->job_type) == "bed2bed")
        {
?>
<p><font face="Times New Roman" style="font-size: 26pt">Result</font></p>
<p><a href=<?php echo "../job_results/$id/bed.txt.out";?>><font face="Times New Roman" style="font-size: 16pt">Converted BED format file</font></a></p>
<?php
        echo "<pre>";
        system("head -n 40 ../job_results/$id/bed.txt.out");
        echo "</pre>";
?>
<?php
        echo "------------------------------END----------------------------------";
}
?>

<?php
        if($row->job_status == 2 && trim($row->job_type) == "eland2bed")
        {
?>
<p><font face="Times New Roman" style="font-size: 26pt">Result</font></p>
<p><a href=<?php echo "../job_results/$id/eland.bed.tar.gz";?>><font face="Times New Roman" style="font-size: 16pt">Download the converted file</font></a></p>
<?php
        echo "<pre>";
        echo "</pre>";
        }
?>

<?php
        if($row->job_status == 2 && trim($row->job_type) == "bowtie2bed")
        {
?>
<p><font face="Times New Roman" style="font-size: 26pt">Result</font></p>
<p><a href=<?php echo "../job_results/$id/bowtie.bed.tar.gz";?>><font face="Times New Roman" style="font-size: 16pt">Download the converted file</font></a></p>
<?php
        echo "<pre>";
        echo "</pre>";
        }
?>

</html>
