<?php

require_once '../globals.php';

$filetypes = array("image/gif", "image/jpeg", "image/pjpeg", "text/xml");


if (	array_search($_FILES["uploadedfile"]["type"], $filetypes) != -1 &&
		$_FILES["uploadedfile"]["size"] < 1000000	)
{
	if ($_FILES["uploadedfile"]["error"] > 0)
	{
		echo "Return Code: " . $_FILES["uploadedfile"]["error"] . "<br />";
		echo "Upload: " . $_FILES["uploadedfile"]["name"] . "<br />";
		echo "Type: " . $_FILES["uploadedfile"]["type"] . "<br />";
		echo "Size: " . ($_FILES["uploadedfile"]["size"] / 1024) . " Kb<br />";
		echo "Temp file: " . $_FILES["uploadedfile"]["tmp_name"] . "<br />";
	}
	else
	{
		$targetLocation = $serverRoot."images/";
		if($_FILES["uploadedfile"]["type"] == "text/xml")
		{
			$targetLocation = $serverRoot."cgi-bin/Admin/temp/";
		}
		$completeFilepath = $targetLocation . $_FILES["uploadedfile"]["name"];
		
		// if (file_exists($serverRoot."images/" . $_FILES["uploadedfile"]["name"]))
		// {
			// echo $_FILES["uploadedfile"]["name"] . " already exists. ";
		// }
		// else
		// {
			move_uploaded_file($_FILES["uploadedfile"]["tmp_name"], $completeFilepath);
			echo $completeFilepath;
		//}
	}
}
else
{
	echo "Invalid file: type:".$_FILES["uploadedfile"]["type"].", size:".$_FILES["uploadedfile"]["size"]." bytes.";
}
?>