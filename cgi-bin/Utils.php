<?php


function MyHtmlSpecialVars_decode($string)
{
	$newString = preg_replace("/%27/", "'", $string);
	$newString = preg_replace("/%20/", " ", $newString);
	return $newString;
}

function PrintHtmlComment($string)
{
	print ("<!-- ".$string."//-->\n");
}

function FilenameFromUrl(&$params=NULL)
{
//	$start = strrpos($_SERVER['REQUEST_URI'], "rocknroll");
//	$end = strlen($_SERVER['REQUEST_URI']);
//	$query = substr($_SERVER['REQUEST_URI'], $start+1);
	
	$query = MyHtmlSpecialVars_decode($_SERVER['QUERY_STRING']);
//	PrintHtmlComment($_SERVER['REQUEST_URI']);
	$params = array();
	$singleParams = explode("/", $query);
	foreach($singleParams as $oneParam)
	{
		$paramTuple = explode(":", $oneParam);
		if(count($paramTuple)>1)
		{
//			PrintHtmlComment($paramTuple[0]." =>". $paramTuple[1]);
			$params[$paramTuple[0]] = $paramTuple[1];
		}
		else
			$params[$oneParam] = true;
	}

	$data = array_keys($params);
	return $data[0];//substr($_SERVER['REQUEST_URI'], $start+1, $end-$start-1);
}

function MakeSafeString($string)
{
	return preg_replace("/\s+/", "_", $string);
}

function MakeSafeTagName($string)
{
	return preg_replace("/[\s+()]/", "_", $string);
}

function DitchQuotes($string)
{
	return preg_replace("/\"+/", "_", $string);
}

?>