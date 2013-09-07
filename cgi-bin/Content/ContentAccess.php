<?php

	include("ContentMgr.php");

	$params = array();
	$errors = array();
	
	$query = FilenameFromUrl($params);
	$builder = ContentMgr::GetInstance()->GetBuilder();
	$doc = $builder->Reset();
	if(isset($_POST["io"]) && $_POST["io"]=="write")
	{
	}
	else if(isset($_POST["suggest"]))
	{
		$goOn = 
				isset($_POST['tabelle']) && strlen($_POST['tabelle'])!=0 &&
				isset($_POST['kategorie']) && strlen($_POST['kategorie'])!=0 &&
				isset($_POST['titel']) && strlen($_POST['titel'])!=0;
		if(!goOn)
		{
			array_push($errors, "Bitte f&uuml;llen Sie alle mit Sternchen versehenden Felder aus.");
			return;
		}
		$tabelle = $_POST['tabelle'];
		$kategorie = $_POST['katergorie']; 
		$title = $_POST['titel'];
		// optional 
		$kuenstler = isset($_POST['kuenstler']) ? $_POST['kuenstler'] : NULL;
		$datum = isset($_POST['datum']) ? $_POST['datum'] : NULL;
		$bild = isset($_POST['bild']) ? $_POST['bild'] : NULL;
		$beschreibung = isset($_POST['beschreibung']) ? $_POST['beschreibung'] : NULL;
		$ort = isset($_POST['ort']) ? $_POST['ort'] : NULL;
		$zeit = isset($_POST['zeit']) ? $_POST['zeit'] : NULL;
		$homepage = isset($_POST['homepage']) ? $_POST['homepage'] : NULL;
		$email = isset($_POST['email']) ? $_POST['email'] : NULL;
		$telefon = isset($_POST['telefon']) ? $_POST['telefon'] : NULL;
		
		$table = null;
		$fields = array();
		$values = array();
		if($tabelle=="termine")
		{
			$table = "events";
			$fields = array("category", "title");
			$values = array($kategorie, $title);
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
			if($beschreibung!=NULL)
			{
				array_push($fields, "description");
				array_push($values, $$beschreibung);
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
			if($homepage!=NULL)
			{
				array_push($fields, "url");
				array_push($values, $homepage);
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
			
		}
		if($table!=null)
		{
			$result = Aufenthalt::GetInstance()->DBConn()->InsertTableContent(
				array(
					'table'=>$table, 
					'fields'=>$fields,
					'values'=>$values
					));
		}

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
				$factory = ContentMgr::GetInstance()->GetFactory();
				$p = $factory->CreateContentPages($_POST["identifier"]);
				$doc->appendChild($contentDiv);
				$p->GetArticle()->RenderParagraphs($contentDiv);
			}
		}
	}
	$builder->Render();
?>