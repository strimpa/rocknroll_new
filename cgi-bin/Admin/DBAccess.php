<?php

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
			$returnString .= "\"".htmlentities(utf8_decode($currRoot))."\"";
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
	
//	PrintHtmlComment("fuckin DBAccess!");
	
	$pattern = '/(pages|submenus|paragraphs|events|links|pictures|navigation|folder|bestellung)/';
	if(0!=preg_match($pattern, $query, $matches, PREG_OFFSET_CAPTURE))
	{
		if(isset($params["write"]) && $params["write"]==true)
		{
			if($query=="pages")
			{
				Aufenthalt::GetInstance()->DBConn()->InsertTableContent(array('table'=>"submenus"));
				// get submenu with highest id:
				$result = Aufenthalt::GetInstance()->DBConn()->GetTableContent(array('table'=>"submenus", 'fields'=>"max(id)"));
				$_POST['menuRef'] =  $result[0]["max(id)"];
				$result = Aufenthalt::GetInstance()->DBConn()->InsertTableContent(array('table'=>$query, 'fields'=>$_POST));
			}
			else
			{
				$result = Aufenthalt::GetInstance()->DBConn()->InsertTableContent(array('table'=>$query, 'fields'=>$_POST));
			}
			$result = Aufenthalt::GetInstance()->DBConn()->GetTableContent(array('table'=>$query, 'fields'=>"max(id)"));
		}
		else if(isset($params["del"]) && $params["delete"]==true)
		{
			$result = Aufenthalt::GetInstance()->DBConn()->DropTableContent(array('table'=>$query, 'fields'=>$_POST));
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
				// PrintHtmlComment('$_POST['.$key.']:'.$value);
			// }
			if($params["xmlinput"])
			{
				EnterXMLintoTable($params["xmlinput"]);
			}
			else {
				$result = Aufenthalt::GetInstance()->DBConn()->SetTableContent(
					array(
						'table'=>$query, 
						'fields'=>array_keys($_POST), 
						'requirements'=>$requirements, 
						'values'=>array_values($_POST)
						));
			}
			$result = Aufenthalt::GetInstance()->DBConn()->GetTableContent(
				array(
					'table'=>$query, 
					'fields'=>"id", 
					'requirements'=>$requirements
					));
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
			$result = GetFolderContent($_POST['assetFolder']);
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
		//		$doc->standalone = false;
				
				$currRow = NULL;
				$rootElem = $doc->createElement(MakeSafeTagName($query));
				$doc->appendChild($rootElem);
				$print = "";
				foreach($result as $row)
				{
					$currRow = $doc->createElement("row");
					if(is_bool($row))
					{
						$currRow->nodeValue = $row;
					}
					else 
					{
						for($colIndex=0;$colIndex<count($row);$colIndex++)
						{
							$keyArray = array_keys($row);
							$fieldName = $keyArray[$colIndex];
							$tagName = MakeSafeTagName($fieldName);
							$col = $doc->createElement($tagName);
	
							if(IsXmlString($row[$fieldName]))
							{
								$importdoc = new DOMDocument();
								$importdoc->encoding = 'UTF-8';
//								ReplaceInvalidChars($row[$fieldName]);
								$importdoc->loadHTML('<?xml version="1.0" encoding="UTF-8"?>\n'.$row[$fieldName]);
	//							PrintHtmlComment("Xml string after import:".$importdoc->C14N());
								
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
								$col->nodeValue = $row[$fieldName];
							}
							$currRow->appendChild($col);
						}
			//			print("<test>".$currRow->nodeValue."</test>");
			//			$doc->normalizeDocument();
					}			
					$rootElem->appendChild($currRow);
				}
			
				$output = $doc->saveXML();
		//		$output = preg_replace("/[\n\r]/", "", $output);
				print $output;
		//			PrintHtmlComment($print);
			}
		}
	}
	else
	{
		print "invalid request!";
	}


?>