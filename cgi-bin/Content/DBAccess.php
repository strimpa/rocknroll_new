<?php

	include("../Utils.php");
	include("../Aufenthalt.php");

	$query = FilenameFromUrl();
	$result = Aufenthalt::GetInstance()->GetConn()->GetTableContent($query, "*");
	
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


?>