<?php

function FilenameFromUrl(&$params=NULL)
{
	$start = strrpos($_SERVER['REQUEST_URI'], "/");
	$end = strrpos($_SERVER['REQUEST_URI'], ".");
	if(-1 == $end || $start > $end)
	{
		$end = strrpos($_SERVER['REQUEST_URI'], "?");
		//
		// call with params
		//
	}
	if(-1 == $end ||$start > $end)
	{
		$end = strlen($_SERVER['REQUEST_URI']);
	}

	if(NULL!=$_SERVER['QUERY_STRING'])
	{
		$params = array();
		$singleParams = explode("&", $_SERVER['QUERY_STRING']);
		foreach($singleParams as $oneParam)
		{
			$paramTuple = explode("=", $oneParam);
//			print ($paramTuple[0]." =>". $paramTuple[1]);
			$params[$paramTuple[0]] = $paramTuple[1];
		}
	}

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