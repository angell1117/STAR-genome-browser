<?php
	Header("Content-type: image/PNG");
	$im = imagecreate(100,20);
	$black = ImageColorAllocate($im, 0,0,0);
	$red = ImageColorAllocate($im, 255,0,0);
	$white = ImageColorAllocate($im, 255,255,255);
	$gray = ImageColorAllocate($im, 200,200,200);
	imagefill($im,0,0,$white);
	
	$style = array($black, $black, $black, $black, $black, $gray, $gray, $gray, $gray, $gray);
	imagesetstyle($im, $style);
	
	$font = $_SERVER["DOCUMENT_ROOT"]."/sdec/arial.ttf";
	$counterFile="counter/counter.txt";
	$num = 2150;
	if(!file_exists($counterFile)){ 
		exec("echo $num > $counterFile");
	}else{
		$fp =fopen($counterFile,"r+");
		$num =fgets($fp,10);
		fclose($fp);
		$num += 1; 
		exec("echo $num > $counterFile");
	}
	imagettftext($im, 14, 0, 10, 15, $gray, $font, "Visitors:$num");	
	ImagePNG($im);
	ImageDestroy($im);
?>
