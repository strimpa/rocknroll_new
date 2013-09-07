<?php
$path_parts = pathinfo($_GET['url']);
header("Content-type: application/xml");
header("Content-Disposition:attachment; filename=\"".$path_parts['filename'].".".$path_parts['extension']."\"");
if(file_exists($_GET['url']))
	readfile($_GET['url']);
else
	echo "File ".$_GET['url']." does not exist";
?>