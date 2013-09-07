<?php


/*
MINIGAL NANO
- A PHP/HTML/CSS based image gallery script

This script and included files are subject to licensing from Creative Commons (http://creativecommons.org/licenses/by-sa/2.5/)
You may use, edit and redistribute this script, as long as you pay tribute to the original author by NOT removing the linkback to www.minigal.dk ("Powered by MiniGal Nano x.x.x")

MiniGal Nano is created by Thomas Rybak

Copyright 2010 by Thomas Rybak
Support: www.minigal.dk
Community: www.minigal.dk/forum

Please enjoy this free script!


USAGE EXAMPLE:
File: createthumb.php
Example: <img src="createthumb.php?filename=photo.jpg&amp;width=100&amp;height=100">
*/
//	error_reporting(E_ALL);


function CreateThumb($filename, $size)
{
	$imageDir = "MiniGal/images/";
	$outputDir = "MiniGal/images/thumbs/";
	
	$info = pathinfo($filename);
	$saveFileName = preg_replace("/[^a-zA-Z0-9]/", "", $filename);
//	$base_file_name =  basename($filename,'.'.$info['extension']);
	$returnPic = $outputDir.$saveFileName.'.'.$info['extension'];
	
	// print "filename:$filename<br />";
	// print "returnPic:$returnPic<br />";
	
	// if exists just give back =)
	if (is_file($returnPic))
	{
		return "/".$returnPic;
	}
//	print("\n<br />returnPic:$returnPic");

//if (preg_match("/.jpg$|.jpeg$/i", $filename)) header('Content-type: image/jpeg');
//if (preg_match("/.gif$/i", $filename)) header('Content-type: image/gif');
//if (preg_match("/.png$/i", $filename)) header('Content-type: image/png');

	// Display error image if file isn't found
	if (!is_file($filename))
	{
//		header('Content-type: image/jpeg');
		$errorimage = ImageCreateFromJPEG($imageDir.'questionmark.jpg');
		ImageJPEG($errorimage,$returnPic,90);
	}
	
	// Display error image if file exists, but can't be opened
	if (substr(decoct(fileperms($filename)), -1, strlen(fileperms($filename))) < 4 OR substr(decoct(fileperms($filename)), -3,1) < 4) {
//		header('Content-type: image/jpeg');
		$errorimage = ImageCreateFromJPEG($imageDir.'cannotopen.jpg');
		ImageJPEG($errorimage,$returnPic,90);
	}
	
	// Define variables
	$target = "";
	$xoord = 0;
	$yoord = 0;

    if ($size == "") $size = 120; //
    $imgsize = GetImageSize($filename);
    $width = $imgsize[0];
    $height = $imgsize[1];
    if ($width > $height) { // If the width is greater than the height it’s a horizontal picture
        $xoord = ceil(($width-$height)/2);
        $width = $height;      // Then we read a square frame that  equals the width
    } else {
        $yoord = ceil(($height-$width)/2);
        $height = $width;
    }

    // Rotate JPG pictures
    if (preg_match("/.jpg$|.jpeg$/i", $filename)) {
		if (function_exists('exif_read_data') && function_exists('imagerotate')) {
			$exif = exif_read_data($filename);
			$ort = $exif['IFD0']['Orientation'];
			$degrees = 0;
		    switch($ort)
		    {
		        case 6: // 90 rotate right
		            $degrees = 270;
		        break;
		        case 8:    // 90 rotate left
		            $degrees = 90;
		        break;
		    }
			if ($degrees != 0)	$target = imagerotate($target, $degrees, 0);
		}
	}
	
//	print("\n<br />filename:$filename");
	
    $target = ImageCreatetruecolor($size,$size);
    if (preg_match("/.jpg$/i", $filename)) $source = ImageCreateFromJPEG($filename);
    if (preg_match("/.gif$/i", $filename)) $source = ImageCreateFromGIF($filename);
    if (preg_match("/.png$/i", $filename)) $source = ImageCreateFromPNG($filename);
    imagecopyresampled($target,$source,0,0,$xoord,$yoord,$size,$size,$width,$height);
 	imagedestroy($source);

    if (preg_match("/.jpg$/i", $filename)) ImageJPEG($target,$returnPic,90);
    if (preg_match("/.gif$/i", $filename)) ImageGIF($target,$returnPic,90);
    if (preg_match("/.png$/i", $filename)) ImageJPEG($target,$returnPic,90); // Using ImageJPEG on purpose
    imagedestroy($target);
         
    return "/".$returnPic; 
}

?>