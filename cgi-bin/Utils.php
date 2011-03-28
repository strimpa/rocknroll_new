<?php


function MyHtmlSpecialVars_decode($string)
{
	$newString = preg_replace("/%27/", "'", $string);
	$newString = preg_replace("/%20/", " ", $newString);
	return $newString;
}

function FilenameFromUrl(&$params=NULL)
{
	$start = strrpos($_SERVER['REQUEST_URI'], ".")+5;
	$end = strlen($_SERVER['REQUEST_URI']);
	
	$query = substr($_SERVER['REQUEST_URI'], $start, $end);

	$query = MyHtmlSpecialVars_decode($query);
//		print "<!-- ".$_SERVER['QUERY_STRING']." //-->";
	$params = array();
	$singleParams = explode("/", $query);
	foreach($singleParams as $oneParam)
	{
//		$paramTuple = explode("=", $oneParam);
//			print ($paramTuple[0]." =>". $paramTuple[1]);
//		if(count($paramTuple)>1)
//			$params[$paramTuple[0]] = $paramTuple[1];
//		else
		$params[$oneParam] = true;
	}

	$data = array_keys($params);
	return $data[0];//substr($_SERVER['REQUEST_URI'], $start+1, $end-$start-1);
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