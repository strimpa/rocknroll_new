<?php

function FilenameFromUrl()
{
	$start = strrpos($_SERVER['REQUEST_URI'], "/");
	$end = strlen($_SERVER['REQUEST_URI']);//strrpos($_SERVER['REQUEST_URI'], ".");
	return substr($_SERVER['REQUEST_URI'], $start+1, $end-$start-1);
}

function MakeSafeString($string)
{
	return preg_replace("/\s+/", "_", $string);
}

function DitchQuotes($string)
{
	return preg_replace("/\"+/", "_", $string);
}

?>