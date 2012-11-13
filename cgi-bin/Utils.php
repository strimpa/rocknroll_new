<?php

include 'globals.php';

function MyHtmlSpecialVars_decode($string)
{
	$newString = preg_replace("/%27/", "'", $string);
	$newString = preg_replace("/%20/", " ", $newString);
	return $newString;
}

function PrintHtmlComment($string)
{
	//print ("<!-- ".$string."//-->\n");
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

function GetGETVars()
{
	$query = MyHtmlSpecialVars_decode($_SERVER['REQUEST_URI']);
	$params = array();
	$pathAndQueries = explode("?", $query);
	if(count($pathAndQueries)<2)
	{
		return $params;
	}
		
	$singleParams = explode("&", $pathAndQueries[1]);
	foreach($singleParams as $oneParam)
	{
		$paramTuple = explode("=", $oneParam);
		if(count($paramTuple)>1)
		{
			PrintHtmlComment($paramTuple[0]."=>". $paramTuple[1]);
			$params[$paramTuple[0]] = $paramTuple[1];
		}
		else
			$params[$oneParam] = true;
	}

	return $params;
}

function MakeSafeString($string)
{
	return preg_replace("/[^a-zA-Z0-9]+/", "_", $string);
}

function MakeSafeTagName($string)
{
	$value = preg_replace("/[^a-zA-Z0-9]/", "_", $string);
	if(preg_match("/^[0-9]/", $value))
		$value = "_".$value;
	return $value;
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

function IsXmlString($subject)
{
	$isXML = preg_match('/[<>]+/i', $subject);
//	print("THE SUBJECT: $subject is xml? $isXML\n");
	return $isXML;
}

function GetFolderContent($assetFolder)
{
	global $serverRoot;
	global $httpRoot;
	$default_dir = $serverRoot;
	$suffix = "";
	$extensions = array("*");
	switch($assetFolder)
	{
		case "images":
		{
			$suffix = "images/";
			PrintHtmlComment("suffix: $suffix");
			$extensions = array("jpg", "gif", "png");
		}
	}
	$default_dir = $default_dir.$suffix;
	if(!($dp = opendir($default_dir))) 
		die("Cannot open $default_dir.");
	$fileList = array();
	while($file = readdir($dp)) 
	{
		if(is_dir($file)) 
		{
			continue;
		}
		else if($file != '.' && $file != '..') 
		{
			$ext = substr($file, strrpos($file, '.') + 1);
			if(FALSE !== array_search($ext, $extensions))
			{
				$safeFileName = MakeSafeTagName($file);
				$fileList[$safeFileName] = $httpRoot.$suffix.$file;
			}				
		}
	}
	$retArray = array();
	array_push($retArray, $fileList);
	return $retArray;
}

function IsAssoc($myarray)
{
	return array_keys($myarray) !== range(0, count($myarray) - 1);
}

function SafeDBString($res)
{
	  $res = str_replace("ü","&uuml;",$res);
	  $res = str_replace("ä","&auml;",$res);
	  $res = str_replace('ö',"&ouml;",$res);
	  $res = str_replace('Ü',"&Uuml;",$res);
	  $res = str_replace('Ä',"&Auml;",$res);
	  $res = str_replace('Ö',"&Ouml;",$res);
	  $res = str_replace("ß", "&szlig;",$res);
	  $res = mysql_escape_string($res);
	  return $res;
}

function EncodeUmlaute($string)
{
	  $res = htmlentities($string);
	  $res = str_replace("&uuml;","ü",$res);
	  $res = str_replace("&auml;","ä",$res);
	  $res = str_replace("&ouml;",'ö',$res);
	  $res = str_replace("&Uuml;",'Ü',$res);
	  $res = str_replace("&Auml;",'Ä',$res);
	  $res = str_replace("&Ouml;",'Ö',$res);
	  $res = str_replace("&szlig;",'ß',$res);
	  $res = mysql_escape_string($res);
	  return $res;
}

function SafeJSONString($out)
{
//	$out = htmlentities($out);
	$out = str_replace("\n","<br />",$out);
  	$out = mysql_escape_string($out);
	return $out;
}

function gibTabelleAlsXml($result, $name){
	global $serverRoot;
	global $httpRoot;
// Verbinden

	if($result)
	{
		// DOMXML Objekt erzeugen
		$doc = new DOMDocument();
		$doc->encoding = 'ISO-8859-1';
		$doc->formatOutput = true;
		$doc->xmlStandalone = true;
		// Wurzel erzeugen (<article> ... </article>
		$name = MakeSafeTagName($name);
		$root = $doc->createElement( $name );
		$doc->appendChild($root);
//			$trans = get_html_translation_table(HTML_ENTITIES);

		//Anzahl der Zeilen
		foreach ($result as $rowkey => $rowvalue) {
		 	 // <artheader> ... </artheader>
		 	 // $safeRowName  = MakeSafeTagName($rowkey);
		  	$row = $doc->createElement( "Row" );
		  	$root->appendChild($row);
			foreach ($rowvalue as $cellkey => $cellvalue) {
				$safeCellName = MakeSafeTagName($cellkey);
				 $cell = $doc->createElement( $safeCellName );
				 $row->appendChild($cell);
				 $actualValue = EncodeUmlaute( utf8_decode($cellvalue) );
				 $cell->nodeValue =$actualValue; 
			}
		}
					
		// XML ausgeben
		$filename = $serverRoot."cgi-bin/Admin/temp/$name.xml";
		$file = fopen ($filename, "w+");

		fputs($file, $doc->saveXML() );
		fclose($file);
		return $filename;
	}

	
/*		
		//Written by Dan Zarrella. Some additional tweaks provided by JP Honeywell
		//pear excel package has support for fonts and formulas etc.. more complicated
		//this is good for quick table dumps (deliverables)
		
		$count = mysql_num_fields($result);
		
		for ($i = 0; $i < $count; $i++){
			$header .= mysql_field_name($result, $i)."\t";
		}
		
		while($row = mysql_fetch_row($result)){
		  $line = '';
		  foreach($row as $value){
			if(!isset($value) || $value == ""){
			  $value = "\t";
			}else{
		# important to escape any quotes to preserve them in the data.
			  $value = str_replace('"', '""', $value);
		# needed to encapsulate data in quotes because some data might be multi line.
		# the good news is that numbers remain numbers in Excel even though quoted.
			  $value = '"' . $value . '"' . "\t";
			}
			$line .= $value;
		  }
		  $data .= trim($line)."\n";
		}
		# this line is needed because returns embedded in the data have "\r"
		# and this looks like a "box character" in Excel
		  $data = str_replace("\r", "", $data);
		
		
		# Nice to let someone know that the search came up empty.
		# Otherwise only the column name headers will be output to Excel.
		if ($data == "") {
		  $data = "\nno matching records found\n";
		}
		
		# This line will stream the file to the user rather than spray it across the screen
		(header("Content-type: application/octet-stream");
		
		# replace excelfile.xls with whatever you want the filename to default to
		header("Content-Disposition: attachment; filename=excelfile.xls");
		header("Pragma: no-cache");
		header("Expires: 0");
		
		//echo $header."\n".$data;
*/
}

function EnterXMLintoTable($tablename, $filename)
{
	global $serverRoot;
//	$path_parts = pathinfo($filename);
	$localPath = $serverRoot."cgi-bin/Admin/temp/$filename";//$path_parts['filename'].".".$path_parts['extension'];
	print "localpath $localPath\n";

	$doc = new DOMDocument();
	$doc->encoding = 'utf-8'; //'ISO-8859-1';
	$doc->load($localPath);
	$root = $doc->firstChild;
	print "tag:".$root->tagName."\n";
	
	$lastResult = FALSE;
	foreach ($root->childNodes as $row) 
	{
		if(!is_a($row, "DOMElement"))
		{
			print "row not an element.\n";
			continue;
		}
		$fieldArray = array();
		$valueArray = array();
		$idValue = null;
		foreach ($row->childNodes as $cell) 
		{
			if(!is_a($cell, "DOMElement"))
			{
				print "cell not an element.\n";
				continue;
			}
			if($cell->tagName=="id")
				$idValue = $cell->textContent;
			else {
				$val = $cell->textContent;
				array_push($fieldArray,$cell->tagName);
				if ($cell->tagName == 'date') 
				{
					$test = date_create_from_format("d.m.Y", $val);
					if(FALSE != $test)
						$val = date_format($test, 'Y-m-d'); // 2011-03-03 00:00:00;
				}
				array_push($valueArray,$val);
				print "there's a value:".$cell->tagName.":".$val."\n";
			}
		}
		$params = array(
				'table'=>$tablename, 
				'fields'=>$fieldArray, 
				'values'=>$valueArray
				);
		if($idValue != null)
		{
			$params['requirements'] = array("id"=>$idValue);
			$lastResult = Aufenthalt::GetInstance()->DBConn()->SetTableContent($params);
		}
		else
		{
			$lastResult = Aufenthalt::GetInstance()->DBConn()->InsertTableContent($params);
		}
		if(is_bool($lastResult) && FALSE==$lastResult)
		{
			//return FALSE;
		}
	}

	return $lastResult;
}

function handleError($errno, $errstr, $errfile, $errline, array $errcontext)
{
    // error was suppressed with the @-operator
    if (0 === error_reporting()) {
        return false;
    }

    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}

?>