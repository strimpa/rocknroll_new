<?php

	include("ContentMgr.php");

	$params = array();
	
	$query = FilenameFromUrl($params);
	$builder = ContentMgr::GetInstance()->GetBuilder();
	$doc = $builder->Reset();
	if(isset($_POST["io"]) && $_POST["io"]=="write")
	{
	}
	else
	{
		switch($query)
		{
			case "paragraphs":
			{
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