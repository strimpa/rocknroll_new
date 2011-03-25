<?php

	include("../Utils.php");
	include("../Aufenthalt.php");

	$params = array();
	$query = FilenameFromUrl($params);
	
	$pattern = '/(pages|submenus|paragraphs|links)/';
	if(0!=preg_match($pattern, $query, $matches, PREG_OFFSET_CAPTURE))
	{
		$result = Aufenthalt::GetInstance()->GetConn()->GetTableContent($query, "*", $params);
		
		// Creates an instance of the DOMImplementation class
//		$imp = new DOMImplementation();
		
		// Creates a DOMDocumentType instance
//		$dtd = $imp->createDocumentType('graph', '', 'graph.dtd');
		
		// Creates a DOMDocument instance
		$doc =  new DOMDocument(); //$imp->createDocument("", "", $dtd);
		
		// Set other properties
//		$doc->encoding = 'UTF-8';
//		$doc->standalone = false;
		
		$currRow = NULL;
		foreach($result as $row)
		{
			$currRow = $doc->createElement("row");
			for($colIndex=0;$colIndex<count($row);$colIndex++)
			{
				$keyArray = array_keys($row);
				$fieldName = $keyArray[$colIndex];
				$col = $doc->createElement($fieldName);
				$importdoc = new DOMDocument();
				$importdoc->loadXML("<balls>".$row[$fieldName]."</balls>");
				$text = $doc->importNode($importdoc->firstChild, true);
//				$text = $doc->createTextNode($row[$fieldName]);
				$col->nodeValue = $text->nodeValue;
//				if($row[$fieldName]!="")
				$currRow->appendChild($col);
			}
//			print("<test>".$currRow->nodeValue."</test>");
//			$doc->normalizeDocument();
			$doc->appendChild($currRow);
		}
	
		$output = $doc->saveXML();
		$output = preg_replace("/[\n\r]/", "", $output);
		print $output;
	}
	else
	{
		print "invalid request!";
	}


?>