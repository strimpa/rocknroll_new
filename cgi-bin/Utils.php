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

function GetPrice($matches)
{
	$price = Aufenthalt::GetInstance()->GetAblauf()->preise[$matches[1]];
	$formattedPrice = sprintf(" %4.2f &euro; ", $price);
	return $formattedPrice;
}

function GetServerVars($matches)
{
	if(isset($_SERVER["$matches[1]"]))
	{
		return $_SERVER["$matches[1]"];
	}
	return $matches[1];
}

function ReplaceTagValues($tagname, $type, $wholetag, $postVar)
{
	PrintHtmlComment("ReplaceTagValues:$tagname, $type, $wholetag, $postVar");
	$newString = $wholetag;
	if(strtolower($tagname)=="input")
	{
		if($type=="text")
		{
			$valpattern = "/value=\"[^\"]*\"/";
			$count = 0;
			$newString = preg_replace($valpattern, "value=\"$postVar\"", $wholetag, -1, $count);
			if($count==0)
			{
				$endPattern = "/\/>/";
				$newString = preg_replace($endPattern, "value=\"$postVar\"/>", $wholetag, -1, $count);
			}
		}
		else if($type == "checkbox")
		{
			$endPattern = "/\/>/";
			$newString = preg_replace($endPattern, " checked/>", $wholetag);
		}
	}
	else if($tagname=="textarea")
	{
		$endPattern = "/>/";
		$newString = preg_replace($endPattern, ">$postVar", $wholetag);
	}
	return $newString;
}

function GetPostVarValue($matches)
{
	$wholetag = $matches[0];
	$newString = $wholetag;
	$tagname = $matches[1];
	$id = $matches[2];
	$postVar = "";
	$typeMatches;
	$typePattern = "/type=\"([^\"]+)\"/i";
	$type = "text";
	$numFound = preg_match($typePattern, $wholetag, $typeMatches);
	if($numFound>0)
	{
//		PrintHtmlComment("found type! $typeMatches[0] $typeMatches[1]");
		$type = $typeMatches[1];
	}
	PrintHtmlComment("id to look for:$id in $wholetag with tag \"$tagname\" and type \"$type\"");
//	for($postIndex = 0;$postIndex<count($_POST);$postIndex++)
//	{
//		$theKeys = array_keys($_POST);
//		PrintHtmlComment($theKeys[$postIndex].":".$_POST[$theKeys[$postIndex]]);
//	}
	if(isset($_POST[$id]))
	{
		$postVar = $_POST[$id];
		$newString = ReplaceTagValues($tagname, $type, $wholetag, $postVar);
	}
	
	$order = Aufenthalt::GetInstance()->GetAblauf()->aktuelleBestellung;
	if(NULL!=$order)
	{
		$orderIndex = array_search($id, Bestellung::$ids);
		if(FALSE!=$orderIndex && $order->bestellt[$orderIndex])
		{
			$newString = ReplaceTagValues($tagname, $type, $wholetag, true);
		}
		$orderIndex = array_search($id, Bestellung::$auswahlIds);
		if(FALSE!=$orderIndex)
		{
			$postVar = $order->ausgabeNr[$orderIndex];
			$newString = ReplaceTagValues($tagname, $type, $wholetag, $postVar);
		}
	}
	
	$user = Aufenthalt::GetInstance()->GetUser();
	if(NULL!=$user)
	{
		$postVar = $user->GetMember($id);
		if(''!=$postVar)
		{
			PrintHtmlComment("User wert: $postVar");
			$newString = ReplaceTagValues($tagname, $type, $wholetag, $postVar);
		}
	}
	PrintHtmlComment($newString);
	return $newString;
}

?>