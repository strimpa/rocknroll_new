<?php
	global $build_errors;
	$build_errors = array();
		

	include("../Utils.php");
	include("../Aufenthalt.php");

	$params = array();
	$query = FilenameFromUrl($params);

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
	
	function RecurseXml($content, $currRoot, $fieldName, $doc)
	{
		$tagName = MakeSafeTagName($fieldName);
		$col = $doc->createElement($tagName);
//		print ("content: ".$content." |"); 
		if(is_array($content))
		{
//			print "is array\n";
			for($colIndex=0;$colIndex<count($content);$colIndex++)
			{
				$keyArray = array_keys($content);
				$fieldName = $keyArray[$colIndex];
				RecurseXml($content[$fieldName], $col, $fieldName, $doc);
				$currRoot->appendChild($col);
			}
		}
		else
		{
			if(IsXmlString($content))
			{
//				print "is xml\n";
				$importdoc = new DOMDocument();
				$importdoc->encoding = 'UTF-8';
				$importdoc->loadHTML('<?xml version="1.0" encoding="UTF-8"?>\n'.$content);
				
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
//				print "is string\n";
				$col->nodeValue = htmlspecialchars($content);
			}
			$currRoot->appendChild($col);
		}
	}
	
	function ReplaceInvalidChars(&$string)	
	{
		$count = 0;		
		$string = preg_replace('/&.+;/', '<img src="invalidCharPic.gif">', $string, $count);
//		PrintHtmlComment("Count of replaced invalid chars: $count");	
	}
	
//	PrintHtmlComment("fuckin DBAccess!");
	
	$pattern = '/(pages|submenus|paragraphs|events|links|pictures|navigation|folder|bestellung|kunden|archive)/';
	$resultEntryID = null;
	if(0!=preg_match($pattern, $query, $matches, PREG_OFFSET_CAPTURE))
	{
		if(isset($params["write"]) && $params["write"]==true)
		{
			if($query=="pages")
			{
				Aufenthalt::GetInstance()->DBConn()->InsertTableContent(array('table'=>"submenus"));
				// get submenu with highest id:
				$_POST['menuRef'] =  mysql_insert_id();
				$result = Aufenthalt::GetInstance()->DBConn()->InsertTableContent(
					array(
						'table'=>$query, 
						'fields'=>array_keys($_POST),
						'values'=>array_values($_POST)
						));
			}
			else
			{
				$result = Aufenthalt::GetInstance()->DBConn()->InsertTableContent(
					array(
						'table'=>$query, 
						'fields'=>array_keys($_POST),
						'values'=>array_values($_POST)
						));
			}
			$resultEntryID = mysql_insert_id();
		}
		else if(isset($params["del"]) && $params["del"]==true)
		{
			$result = Aufenthalt::GetInstance()->DBConn()->DropTableContent(
				array(
					'table'=>$query, 
					'requirements'=>$_POST)
					);
		}
		else if(isset($params["edit"]) && $params["edit"]==true)
		{
			$requirements = NULL;
			if(isset($params["req"]))
			{
				$reqTuple = explode("=", $params["req"]);
				$requirements = array($reqTuple[0]=>$reqTuple[1]);
//				PrintHtmlComment("edit:".$reqTuple[0].",".$reqTuple[1]);
			}
			// foreach ($_POST as $key => $value) {
				// Print ('$_POST['.$key.']:'.$value);
			// }
			$result = Aufenthalt::GetInstance()->DBConn()->SetTableContent(
				array(
					'table'=>$query, 
					'fields'=>array_keys($_POST), 
					'requirements'=>$requirements, 
					'values'=>array_values($_POST)
					));
			assert(count($result)==1);
			$resultEntryID = mysql_insert_id();
		}
		else if(isset($params["def"]))
		{
			$requirements = NULL;
			if(isset($params["req"]))
			{
				$reqTuple = explode("=", $params["req"]);
				$requirements = array($reqTuple[0]=>$reqTuple[1]);
			}
			$joinFields = NULL;
			if(isset($params["joinFields"]))
			{
				$unexploded = explode(",", $params["joinFields"]);
				$joinFields = array();
				foreach ($unexploded as $value) {
					$exploded = explode("=", $value);
					$joinFields[$exploded[0]] = $exploded[1];
				}
			}
			$result = Aufenthalt::GetInstance()->DBConn()->GetTableDef(
				array(
					'table'=>$query, 
					'fields'=>array_keys($_POST), 
					'requirements'=>$requirements,
					'joinFields'=>$joinFields
					));
		}
		else if(isset($params["folder"]))
		{
			$result = GetFolderContent($_POST['assetFolder'], isset($params["recursive"]));
		}
		else if(isset($params["xmlinput"]))
		{
			print "path param:".$params["xmlinput"];
			$result = EnterXMLintoTable($query, $params["xmlinput"]);
		}
		else
		{
//			PrintHtmlComment("read!!");
			$selector = "*";
			if(isset($params['selector']))
				$selector = $params['selector'];
			$orderBy = "";
			if(isset($params['orderBy']))
				$orderBy = $params['orderBy'];
			$joinFields = NULL;
			if(isset($params["joinFields"]))
			{
				$unexploded = explode(",", $params["joinFields"]);
				$joinFields = array();
				foreach ($unexploded as $value) {
					$exploded = explode("=", $value);
					$joinFields[$exploded[0]] = $exploded[1];
				}
			}
			// foreach ($_POST as $key => $value) {
				// PrintHtmlComment('$_POST['.$key.']:'.$value);
			// }
			$result = Aufenthalt::GetInstance()->DBConn()->GetTableContent(
				array(
					'table'=>$query, 
					'fields'=>$selector, 
					'requirements'=>$_POST, 
					'useRegExp'=>isset($params['regexp']), 
					'orderBy'=>$orderBy, 
					'distinct'=>isset($params['distinct']),
					'joinFields'=>$joinFields
					));
		}
		
		if(isset($params["xmloutput"]) && $params["xmloutput"])
		{
			print gibTabelleAlsXml($result, $query);
		}
		else if(null!=$resultEntryID)
		{
			echo $resultEntryID;
		}
		else if(!is_bool($result))
		{
			if(isset($params["json"]))
			{
				$keyArray = array();
				$retString = "";
				RecurseJson($result, $params["json"], $retString, 0, IsAssoc($result));
				print $retString;
			}
			else 
			{
				$doc =  new DOMDocument(); //$imp->createDocument("", "", $dtd);
				// Set other properties
				$doc->encoding = 'UTF-8';
				$doc->formatOutput = true;
		//		$doc->standalone = false;
				
				$currRow = NULL;
				$rootElem = $doc->createElement(MakeSafeTagName($query));
				$doc->appendChild($rootElem);
				$print = "";
				
				set_error_handler('handleError', E_WARNING|E_ERROR);
				
				try{
					RecurseXml($result, $rootElem, "row", $doc);
					/*
					foreach($result as $row)
					{
							if(is_bool($row))
							{
								$currRow = $doc->createElement("row");
								$currRow->nodeValue = $row;
								$rootElem->appendChild($currRow);
							}
							else 
							{
								
							}			
						}
					}
					*/
				}
				catch(Exception $e)
				{
					array_push($build_errors, "Fehler beim lesen von html in Tabelle $query and id ".$row["id"].": ".$e->getMessage());
				}
				foreach($build_errors as $anError)
				{
					$currError = $doc->createElement("error");
					$currError->nodeValue = $anError;
					$rootElem->appendChild($currError);
				}

				restore_error_handler();
			
				$output = $doc->saveXML();
		//		$output = preg_replace("/[\n\r]/", "", $output);
				print $output;
		//			PrintHtmlComment($print);
			}
		}
		else if($result!=TRUE)
		{
			print "ERROR: FALSE result given back:";
		}
	
	}
	else
	{
		print "invalid table request!";
	}


?>