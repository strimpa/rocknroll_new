<?php

	include("../Utils.php");
	include("../Aufenthalt.php");

	$params = array();
	$query = FilenameFromUrl($params);
	
//	PrintHtmlComment("fuckin DBAccess!");
	
	$pattern = '/(pages|submenus|paragraphs|events|links|pictures|navigation)/';
	if(0!=preg_match($pattern, $query, $matches, PREG_OFFSET_CAPTURE))
	{
		if(isset($params["write"]) && $params["write"]==true)
		{
			PrintHtmlComment("write:");
			if($query=="pages")
			{
				Aufenthalt::GetInstance()->GetConn()->InsertTableContent("submenus");
				// get submenu with highest id:
				$result = Aufenthalt::GetInstance()->GetConn()->GetTableContent("submenus", "max(id)");
				$_POST['menuRef'] =  $result[0]["max(id)"];
				$result = Aufenthalt::GetInstance()->GetConn()->InsertTableContent($query, $_POST);
			}
			else
			{
				$result = Aufenthalt::GetInstance()->GetConn()->InsertTableContent($query, $_POST);
			}
			$result = Aufenthalt::GetInstance()->GetConn()->GetTableContent($query, "max(id)");
		}
		else if(isset($params["delete"]) && $params["delete"]==true)
		{
			PrintHtmlComment("delete:".$_POST['identifier']);
			if($query=="pages")
			{
				$result = Aufenthalt::GetInstance()->GetConn()->DropTableContent($query, $_POST);
			}
		}
		else if(isset($params["edit"]) && $params["edit"]==true)
		{
			$requirements = NULL;
			if(isset($params["req"]))
			{
				$reqTuple = explode("=", $params["req"]);
				$requirements = array($reqTuple[0]=>$reqTuple[1]);
				PrintHtmlComment("edit:".$reqTuple[0].",".$reqTuple[1]);
			}
			$result = Aufenthalt::GetInstance()->GetConn()->SetTableContent($query, array_keys($_POST), $requirements, array_values($_POST));
		}
		else
		{
//			PrintHtmlComment("read!!");
			$selector = "*";
			if(isset($params['selector']))
				$selector = $params['selector'];
//			PrintHtmlComment('$_POST[id]:'.$_POST['id']);
			$result = Aufenthalt::GetInstance()->GetConn()->GetTableContent($query, $selector, $_POST, isset($params['regexp']));
		}
		
		if(!is_bool($result))
		{
			$doc =  new DOMDocument(); //$imp->createDocument("", "", $dtd);
			// Set other properties
	//		$doc->encoding = 'UTF-8';
	//		$doc->standalone = false;
			
			$currRow = NULL;
			$rootElem = $doc->createElement($query);
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
						$importdoc = new DOMDocument();
						$importdoc->loadXML("<balls>".$row[$fieldName]."</balls>");
						$print = $row[$fieldName];
						$text = $doc->importNode($importdoc->firstChild, true);
		//				$text = $doc->createTextNode($row[$fieldName]);
						$col->nodeValue = $text->nodeValue;
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
	else
	{
		print "invalid request!";
	}


?>