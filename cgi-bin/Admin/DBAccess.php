<?php
	include("../Utils.php");
	include("../Aufenthalt.php");

	session_start();

	global $serverRoot;
	global $build_errors;
	global $currRow;
	$build_errors = array();
		
	$params = array();
	$query = FilenameFromUrl($params);

//	PrintHtmlComment("fuckin DBAccess!");
	
	$pattern = '/(pages|submenus|paragraphs|events|links|pictures|navigation|folder|bestellung|kunden|archive|plugins)/';
	$resultEntryID = null;
	$attributes = array();
	if(0!=preg_match($pattern, $query, $matches, PREG_OFFSET_CAPTURE))
	{
		if(isset($params["write"]) && $params["write"]==true)
		{
			if($query=="pages")
			{
				DBCntrl::GetInst()->Conn()->InsertTableContent(array('table'=>"submenus"));
				// get submenu with highest id:
				$_POST['menuRef'] =  mysqli_insert_id(DBCntrl::GetInst()->Conn()->Connect());
				$result = DBCntrl::GetInst()->Conn()->InsertTableContent(
					array(
						'table'=>$query, 
						'fields'=>array_keys($_POST),
						'values'=>array_values($_POST)
						));
			}
			else
			{
				$result = DBCntrl::GetInst()->Conn()->InsertTableContent(
					array(
						'table'=>$query, 
						'fields'=>array_keys($_POST),
						'values'=>array_values($_POST)
						));
			}
			$resultEntryID = mysqli_insert_id(DBCntrl::GetInst()->Conn()->Connect());
		}
		else if(isset($params["del"]) && $params["del"]==true)
		{
			$result = DBCntrl::GetInst()->Conn()->DropTableContent(
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
			$result = DBCntrl::GetInst()->Conn()->SetTableContent(
				array(
					'table'=>$query, 
					'fields'=>array_keys($_POST), 
					'requirements'=>$requirements, 
					'values'=>array_values($_POST)
					));
			assert(count($result)==1);
			$resultEntryID = mysqli_insert_id(DBCntrl::GetInst()->Conn()->Connect());
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
			$result = DBCntrl::GetInst()->Conn()->GetTableDef(
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
		else if(isset($params["plugins"]))
		{
			$plugins = Aufenthalt::GetInst()->GetPlugins();
			$result = array();
			foreach ($plugins as $key => $value) {
				$result[$key] = $value->GetAdminScript();
				$locaPath = $value->GetPath();
				if(!is_dir($locaPath))
					$locaPath = dirname($locaPath);
				$locaPath = str_ireplace("\\", "/", $locaPath);
				$safeServerRoot = str_ireplace("\\", "/", $serverRoot);
				$locaPath = str_replace($safeServerRoot, "", $locaPath);
				$attributes[$key] = array("localPath"=>$locaPath);
			}
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
			$result = DBCntrl::GetInst()->Conn()->GetTableContent(
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
			print gibTabelleAlsXml($result, $query, $attributes);
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
				
				try{
					RecurseXml($result, $rootElem, "row", $doc, $attributes);
				}
				catch(Exception $e)
				{
					array_push($build_errors, "Fehler beim lesen von html in Tabelle $query and id ".$currRow.": ".$e->getMessage());
				}
				foreach($build_errors as $anError)
				{
					$currError = $doc->createElement("error");
					$currError->nodeValue = $anError;
					$rootElem->appendChild($currError);
				}

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