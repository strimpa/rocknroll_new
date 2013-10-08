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

function SendDebugMail($msg, $isProblem=FALSE)
{
	$to = 'mail@rocknroll-magazin.de';

	$subject = 'Automatisch verschickte mail.';
	if($isProblem)
		$subject .= " Fuer Gunnar!";
	$message = "Nachricht:<br />".$msg;
	
	// To send HTML mail, the Content-type header must be set
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	
	// Additional headers
	$headers .= 'From: R&R website mail script <mail@rocknroll-magazin.de>' . "\r\n";
	
	mail($to, $subject, $message, $headers);
}

function ParseDateFromString($str)
{
	$datum = "";
	$datumInfo = date_parse($str);
	if(FALSE==$datumInfo)
	{
		return FALSE;
	}
	else
	{
		if(NULL==$datumInfo['month'] || NULL==$datumInfo['day'])
			return false;
		if(NULL!=$datumInfo['year'])
			$datum .= $datumInfo['year']."-";
		$datum .= $datumInfo['month']."-".$datumInfo['day'];
	}
	return $datum; 
}

function RecurseXml($content, $currRoot, $fieldName, $doc, $attrs)
{
	$tagName = MakeSafeTagName($fieldName);
	$col = $doc->createElement($tagName);
//		print ("content: ".$content." |"); 
	if(is_array($content))
	{
		for($colIndex=0;$colIndex<count($content);$colIndex++)
		{
			$currRow = $colIndex;
			$keyArray = array_keys($content);
			$fieldName = $keyArray[$colIndex];
			RecurseXml($content[$fieldName], $col, $fieldName, $doc, $attrs);
			$currRoot->appendChild($col);
		}
	}
	else
	{
		set_error_handler('handleError', E_WARNING|E_ERROR);
		$potentialContent =  EncodeUmlaute($content); 
		if(IsXmlString($potentialContent))
		{
			$importdoc = new DOMDocument();
			$importdoc->encoding = 'UTF-8';
			$importdoc->loadHTML('<?xml version="1.0" encoding="UTF-8"?>\n'.$potentialContent);
			
			$node = $importdoc->getElementsByTagName("div")->item(0);
			$text = FALSE;
			if(null!=$node)
				$text = $doc->importNode($node, true);
			if(FALSE!=$text)
				$col->appendChild($text);
			else
			{
				$text = $doc->createTextNode("Fehler beim Text laden!");
				$col->appendChild($text);
			}
		} 
		else
		{
			$col->nodeValue = htmlspecialchars($content);
		}
		$currRoot->appendChild($col);
		restore_error_handler();
	}

	if(array_key_exists($fieldName, $attrs))
	{
		foreach ($attrs[$fieldName] as $key => $value) {
			$col->setAttribute($key, $value);
		}
	}
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
		$value = "A_".$value;
	return $value;
}

function DitchQuotes($string)
{
	return preg_replace("/\"+/", "_", $string);
}

function GetPrice($matches)
{
	$price = BestellAblauf::GetInst()->preise[$matches[1]];
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
	
	$order = BestellAblauf::GetInst()->aktuelleBestellung;
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
	
	$user = Aufenthalt::GetInst()->GetUser();
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

function GetFolderContentRec($relPath, $extensions, $resursive)
{
	global $serverRoot;
	global $httpRoot;
	
	$default_dir = $serverRoot.$relPath;
	$fileList = array();
	PrintHtmlComment($default_dir);
	if(!($dp = opendir($default_dir))) 
	{
		print("Cannot open $default_dir.");
		return $fileList;
	}
	while($file = readdir($dp)) 
	{
		$urlSafeFile = urlencode($file);
		if($file[0]==".")
			continue;
		$temppath = $default_dir.$file; 
		$safeFileName = MakeSafeTagName($file);
		if(is_dir($temppath))
		{
			if($resursive)
				$fileList[$safeFileName] = GetFolderContentRec($relPath.$file."/", $extensions, true);
		}
		else
		{
			$ext = substr($file, strrpos($file, '.') + 1);
			if("*"==$extensions[0] || FALSE !== array_search($ext, $extensions))
			{
				$fileList[$safeFileName] = $httpRoot.$relPath.$file;
				PrintHtmlComment("adding:".$fileList[$safeFileName]);
			}
		}
	}
	return $fileList;
}
function GetFolderContent($assetFolder, $resursive)
{
	global $serverRoot;
	global $httpRoot;
	$default_dir = $serverRoot;
	$suffix = $assetFolder."/";
	$extensions = array("*");
	switch($assetFolder)
	{
		case "images":
		{
			$extensions = array("jpg", "gif", "png");
			$default_dir = $default_dir.$suffix;
		}
	}
	$default_dir = $suffix;
	
	$fileList = GetFolderContentRec($suffix, $extensions, $resursive);
	
	$retArray = array();
	array_push($retArray, $fileList);
	return $retArray;
}

// function WriteExifCOmment($fileUrl, $comment)
// {
	// exif_ UserComment
// }

function IsAssoc($myarray)
{
	return array_keys($myarray) !== range(0, count($myarray) - 1);
}

function DecodeUmlaute($res)
{
	  $res = str_replace("ü","&uuml;",$res);
	  $res = str_replace("ä","&auml;",$res);
	  $res = str_replace('ö',"&ouml;",$res);
	  $res = str_replace('Ü',"&Uuml;",$res);
	  $res = str_replace('Ä',"&Auml;",$res);
	  $res = str_replace('Ö',"&Ouml;",$res);
	  $res = str_replace("ß", "&szlig;",$res);
	  return $res;
}

function EncodeUmlaute($res)
{
	  $res = str_replace("&uuml;","ü",$res);
	  $res = str_replace("&auml;","ä",$res);
	  $res = str_replace("&ouml;",'ö',$res);
	  $res = str_replace("&Uuml;",'Ü',$res);
	  $res = str_replace("&Auml;",'Ä',$res);
	  $res = str_replace("&Ouml;",'Ö',$res);
	  $res = str_replace("&szlig;",'ß',$res);
	  return $res;
}

function SafeDBString($res)
{
	if(is_int($res))
		return strval($res);
	  //$res = htmlspecialchars_decode($res);
	  $res = EncodeUmlaute($res);
	  //$res = htmlspecialchars($res);
	  $res = DecodeUmlaute($res);
	  $res = mysqli_escape_string(DBCntrl::GetInst()->Conn()->Connect(), $res);
	  $res = str_replace("& ", "&amp; ",$res);
	  if(!mb_detect_encoding($res, 'UTF-8', true))
	  	$res = utf8_encode($res);
	  return $res;
}

function SafeJSONString($res)
{
//	$out = htmlentities($out);
	$res = str_replace("\n","<br />",$res);
//  	$res = DecodeUmlaute($res);
	$res = mysqli_escape_string(DBCntrl::GetInst()->Conn()->Connect(), $res);
  	return $res;
}

function gibTabelleAlsXml($result, $name, $attrs){
	global $serverRoot;
	global $httpRoot;
// Connectn

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
			foreach ($rowvalue as $cellkey => $cellvalue) 
			{
				$safeCellName = MakeSafeTagName($cellkey);
				 $cell = $doc->createElement( $safeCellName );
				 $row->appendChild($cell);
				 $asciiCellValue = preg_replace('/[^(\x20-\x7F)]+/', '?', $cellvalue);
				 $actualValue =  EncodeUmlaute( utf8_decode($asciiCellValue) );
				 $cell->nodeValue =$actualValue;

				if(array_key_exists($cellkey, $attrs))
				{
					foreach ($variable as $key => $value) {
						$cell->setAttribute($key, $value);
					}
				}
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
			else if($cell->tagName=="approved")
			{
				/* Don't do anythoing as we set that manually below! */
			}
			else {
				$val = $cell->textContent;
				array_push($fieldArray,$cell->tagName);
				if ($cell->tagName == 'date') 
				{
					$test = DateTime::createFromFormat("d.m.Y", $val);
					if(FALSE != $test)
						$val = date_format($test, 'Y-m-d'); // 2011-03-03 00:00:00;
				}
				array_push($valueArray,$val);
				print "there's a value:".$cell->tagName.":".$val."\n";
			}
		}
		array_push($fieldArray,"approved");
		array_push($valueArray,1);
		$params = array(
				'table'=>$tablename, 
				'fields'=>$fieldArray, 
				'values'=>$valueArray
				);
		if($idValue != null)
		{
			$params['requirements'] = array("id"=>$idValue);
			$lastResult = DBCntrl::GetInst()->Conn()->SetTableContent($params);
		}
		else
		{
			$lastResult = DBCntrl::GetInst()->Conn()->InsertTableContent($params);
		}
		if(is_bool($lastResult) && FALSE==$lastResult)
		{
			//return FALSE;
		}
	}

	return $lastResult;
}

function RecurseJson($currRoot, $currName, &$returnString, $depth, $printName)
{
	$tabs = "";
	for ($i=0; $i < $depth; $i++) { 
		$tabs .= "\t";
	}
	
	$currCount = count($currRoot);
	
	if(!is_array($currRoot))
	{
		$returnString .= $tabs;
		if($printName)
			$returnString .= $currName.": ";
		$returnString .= "\"".SafeJSONString($currRoot)."\"";
		$returnString = str_replace("\r\n", "<br />", $returnString);
	}
	else
	{
		$returnString .= $tabs;
		if($printName)
			$returnString .= $currName.": ";
		
		$isAssoc = IsAssoc($currRoot);
		if($isAssoc)
			$returnString .= "{\n";
		else
			$returnString .= "[\n";
		$keys = array_keys($currRoot);
		$values = array_values($currRoot);
		for($k=0;$k<$currCount;$k++)
		{
			$row = $values[$k];
			$name = $keys[$k];
			RecurseJson($row, $name, $returnString, $depth+1, $isAssoc);
			if($k<($currCount-1))
				$returnString .= ",";
			$returnString .= "\n";
		}
		if($isAssoc)
			$returnString .= $tabs."}";
		else
			$returnString .= $tabs."]";
	}
}		

function ReplaceInvalidChars(&$string)	
{
	$count = 0;		
	$string = preg_replace('/&.+;/', '<img src="invalidCharPic.gif">', $string, $count);
//		PrintHtmlComment("Count of replaced invalid chars: $count");	
}


function handleError($errno, $errstr, $errfile, $errline, array $errcontext)
{
    // error was suppressed with the @-operator
    if (0 === error_reporting()) {
        return false;
    }

    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}

function lang($key,$markers = NULL)
{
	global $lang;
	
	if($markers == NULL)
	{
		$str = $lang[$key];
	}
	else
	{
		//Replace any dyamic markers
		$str = $lang[$key];

		$iteration = 1;
		
		foreach($markers as $marker)
		{
			$str = str_replace("%m".$iteration."%",$marker,$str);
			
			$iteration++;
		}
	}
	
	//Ensure we have something to return
	if($str == "")
	{
		return ("No language key found");
	}
	else
	{
		return $str;
	}
}


?>