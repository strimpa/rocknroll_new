<?php

	include("ContentMgr.php");

	$params = array();
	$suggestionErrors = array();
	
	$query = FilenameFromUrl($params);
	$builder = ContentMgr::GetInst()->GetBuilder();
	$doc = $builder->Reset();
	$result = array();
	if(isset($_POST["io"]) && $_POST["io"]=="write")
	{
	}
	else if(isset($_POST["suggestion"]))
	{
		require_once('cgi-bin/recaptchalib.php');
		$privatekey = "6LfSqN4SAAAAAChTATLV3zKyQGEepadN9-4DjksX";
		$resp = recaptcha_check_answer ($privatekey,$_SERVER["REMOTE_ADDR"],$_POST["recaptcha_challenge_field"],$_POST["recaptcha_response_field"]);
		
		$needed = $_POST["benoetigt"];
		$neededTokens = array("tabelle");
		$neededTokens = array_merge($neededTokens, explode(",", $needed));
		
		$goOn = true;//$resp->is_valid;
		if(!$goOn)
		{
			$suggestionErrors['Captcha problem'] = $resp->error;
		}
		foreach($neededTokens as $needToken)
		{
			$isCOmplete = isset($_POST[$needToken]) && strlen($_POST[$needToken])!=0;
			if(!$isCOmplete)
			{
				$suggestionErrors[$needToken] = "Feld nicht ausgef&uuml;llt.";
			}
			$goOn = $goOn && $isCOmplete;
		}
		
		if($goOn)
		{
			$tabelle = $_POST['tabelle'];
			$kategorie = strtolower($_POST['kategorie']); 
			// optional 
			$title =  isset($_POST['titel']) ? $_POST['titel']: NULL;
			$kuenstler = isset($_POST['kuenstler']) ? $_POST['kuenstler'] : NULL;
			$datum = isset($_POST['datum']) ? $_POST['datum'] : NULL;
			$bild = isset($_POST['bild']) ? $_POST['bild'] : NULL;
			$beschreibung = isset($_POST['beschreibung']) ? $_POST['beschreibung'] : NULL;
			$ort = isset($_POST['ort']) ? $_POST['ort'] : NULL;
			$zeit = isset($_POST['zeit']) ? $_POST['zeit'] : NULL;
			$link = isset($_POST['link']) ? $_POST['link'] : NULL;
			$email = isset($_POST['email']) ? $_POST['email'] : NULL;
			$telefon = isset($_POST['telefon']) ? $_POST['telefon'] : NULL;
			
			$table = null;
			$fields = array("category", "approved");
			$values = array($kategorie, "0");
	
			if($beschreibung!=NULL)
			{
				array_push($fields, "description");
				array_push($values, $beschreibung);
			}
			if($link!=NULL)
			{
				array_push($fields, "url");
				array_push($values, $link);
			}
	
			if($tabelle=="termine")
			{
				$table = "events";
				$datum = ParseDateFromString($datum);
				if($title!=NULL)
				{
					array_push($fields, "title");
					array_push($values, $title);
				}
				if(FALSE==$datum)
				{
					$suggestionErrors['datum'] = "Das Datum konnte nicht erfolgreich erkannt werden. Bitte benutzen Sie mm/tt/jjjj oder tt.mm.jjjj.";
				}
				else
				{
					$_POST['datum'] = $datum;
				}
				if($kuenstler!=NULL)
				{
					array_push($fields, "artists");
					array_push($values, $kuenstler);
				}
				if($datum!=NULL)
				{
					array_push($fields, "date");
					array_push($values, $datum);
				}
				if($bild!=NULL)
				{
					array_push($fields, "pic");
					array_push($values, $bild);
				}
				if($ort!=NULL)
				{
					array_push($fields, "location");
					array_push($values, $ort);
				}
				if($zeit!=NULL)
				{
					array_push($fields, "time");
					array_push($values, $zeit);
				}
				if($email!=NULL)
				{
					array_push($fields, "mail");
					array_push($values, $email);
				}
				if($telefon!=NULL)
				{
					array_push($fields, "tel");
					array_push($values, $telefon);
				}
			}
			else if($tabelle=="verlinkung")
			{
				$table = "links";
			}
			
			if($table==NULL)
			{
				$suggestionErrors['code'] = "Das Programm konnte die Zieltabelle nicht ermitteln. Falls sie den Link kopiert haben, stellen Sie sicher dass er komplett uebernommen wurde.";
			}
			
			if(count($suggestionErrors)<=0)
			{
				$result = DBCntrl::GetInst()->Conn()->InsertTableContent(
					array(
						'table'=>$table, 
						'fields'=>$fields,
						'values'=>$values
						));
			}

			$msqlerrors = mysqli_error(DBCntrl::GetInst()->Conn()->connect());
			if(strlen($msqlerrors)>0)
			{
				SendDebugMail("Suggestion failed:".$msqlerrors."<br />Eingaben:".join(",", $_POST), TRUE);
				$suggestionErrors["Datenbank"] = "Fehler beim schreiben in die Datenbank. Eine email mit Debug informationen wurde verschickt. ".$msqlerrors;
			}
			else {
				SendDebugMail("Neuer Vorschlag eingetragen:".join(",", $_POST).". <br />Gehen Sie auf die Admin Seite auf die \"approve\" Seite um ihn zu bestaetigen.");
			}
		}
		
		// if(!is_bool($result))
		// {
			// $doc =  new DOMDocument(); //$imp->createDocument("", "", $dtd);
			// // Set other properties
			// $doc->encoding = 'UTF-8';
			// $doc->formatOutput = true;
	// //		$doc->standalone = false;
// 			
			// $currRow = NULL;
			// $rootElem = $doc->createElement("ContentAccess");
			// $doc->appendChild($rootElem);
			// $print = "";
// 			
			// try{
				// RecurseXml($result, $rootElem, "row", $doc);
			// }
			// catch(Exception $e)
			// {
				// array_push($build_errors, "Fehler beim lesen von html in Tabelle $query and id ".$currRow.": ".$e->getMessage());
			// }
// 
			// $output = $doc->saveXML();
			// print $output;
		// }
		// else if($result!=TRUE)
		// {
			// print "ERROR: FALSE result given back:";
		// }
	}
	else
	{
		switch($query)
		{
			case "paragraphs":
			{
				if(!isset($_POST["identifier"]) || $_POST["identifier"]=="")
					return;
				$contentDiv = $builder->AddTag("div");
				$factory = ContentMgr::GetInst()->GetFactory();
				$p = $factory->CreateContentPages($_POST["identifier"]);
				$doc->appendChild($contentDiv);
				$p->GetArticle()->RenderParagraphs($contentDiv);
			}
		}
	}
	$builder->Render();
?>