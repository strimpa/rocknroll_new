<?php

	include("ContentMgr.php");

	$params = array();
	$query = FilenameFromUrl($params);
	
	$builder = ContentMgr::GetInstance()->GetBuilder();
	switch($query)
	{
		case "paragraphs":
		{
			$doc = $builder->Reset();
			$contentDiv = $builder->AddTag("div");
			$factory = ContentMgr::GetInstance()->GetFactory();
			$p = $factory->CreateContentPages($params["identifier"]);
			$doc->appendChild($contentDiv);
			$p->GetArticle()->RenderParagraphs($contentDiv);
		}
	}
	$builder->Render();
?>