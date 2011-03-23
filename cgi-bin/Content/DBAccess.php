<?php

	include("../Utils.php");
	include("../Aufenthalt.php");

	$params = array();
	$query = FilenameFromUrl($params);
	
	$pattern = '/(pages|submenus|paragraphs|links)/';
	if(0!=preg_match($pattern, $query, $matches, PREG_OFFSET_CAPTURE))
	{
		$result = Aufenthalt::GetInstance()->GetConn()->GetTableContent($query, "*", $params);
		
		$doc = new DOMDocument();
		$currRow = NULL;
		foreach($result as $row)
		{
			$currRow = $doc->createElement("row");
			$doc->appendChild($currRow);
			for($colIndex=0;$colIndex<count($row);$colIndex++)
			{
				$keyArray = array_keys($row);
				$fieldName = $keyArray[$colIndex];
				$col = $doc->createElement($fieldName);
				$col->nodeValue = $row[$fieldName];
				$currRow->appendChild($col);
			}
		}
	
		print $doc->saveHTML();
	}
	else
	{
		print "invalid request!";
	}


?>