<?php
	$num="";
	for($i=0;$i<4;$i++)
	{
		$num .= rand(0,9);
	}
	Session_start();
	$_SESSION["Checknum"] = $num;
	Header("Content-type: image/PNG");
	srand((double)microtime()*1000000);
	$im = imagecreate(60,20);
	$black = ImageColorAllocate($im, 0,0,0);
	$red = ImageColorAllocate($im, 255,0,0);
	$gray = ImageColorAllocate($im, 200,200,200);
	imagefill($im,0,0,$gray);
	
	$style = array($black, $black, $black, $black, $black, $gray, $gray, $gray, $gray, $gray);
	imagesetstyle($im, $style);
	$y1=rand(0,20);
	$y2=rand(0,20);
	$y3=rand(0,20);
	$y4=rand(0,20);
	imageline($im, 0, $y1, 60, $y3, IMG_COLOR_STYLED);
	imageline($im, 0, $y2, 60, $y4, IMG_COLOR_STYLED);
	
	for($i=0;$i<80;$i++)
	{
		imagesetpixel($im, rand(0,60), rand(0,20), $black);
	}
	$strx=rand(6,11);
	$font = $_SERVER["DOCUMENT_ROOT"]."/sdec/arial.ttf";
	for($i=0;$i<4;$i++)
	{
		$strpos=rand(14,20);
		imagettftext($im, 14, 0, $strx, $strpos, $black, $font, substr($num,$i,1));	
		$strx+=rand(10,14);
	}
	ImagePNG($im);
	ImageDestroy($im);
?>
