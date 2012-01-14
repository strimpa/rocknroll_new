<?php

include("Paragraph.php");

class Article
{

	private $title;
	private $paragraphs;
	
	public function Article()
	{
		$this->paragraphs = array();
	}
	
	public function SetTitle($t)
	{
		$this->title = $t;
	}
	
	public function AddParagraph($para)
	{
		array_push($this->paragraphs, $para);
	}
	
	public function RenderParagraphs(&$contentDiv)
	{
		$builder = ContentMgr::GetInstance()->GetBuilder();
		$currentOffset = 20;
		foreach($this->paragraphs as $para)
		{
		    $para->Render($contentDiv, $currentOffset);
		}	
//		$builder->AddStyle($contentDiv, ("height:".$currentOffset."px;"));
	}
	
	public function Render(&$parentNode)
	{
		$builder = ContentMgr::GetInstance()->GetBuilder();

		$parentNode->appendChild($builder->AddTag("div", "articleTitle", NULL, $this->title));
		
		$contentBGDiv = $builder->AddTag("div", "contentBG", "redbordered");
		//\n<div id="contentLeftOverlap"><img alt="" src="/images/layout/BG_04.jpg" /></div>
		$menuPicDiv = $builder->AddTag("div", "contentLeftOverlap");
	    $pic = $builder->GetDoc()->createElement( "img" );
		$pic->setAttribute("src", "/images/layout/BG_04.jpg");
		$menuPicDiv->appendChild($pic);
		$contentBGDiv->appendChild($menuPicDiv);
		$parentNode->appendChild($contentBGDiv);
		
		$contentDiv = $builder->AddTag("div", "content");
		$contentBGDiv->appendChild($contentDiv);
		
		$this->RenderParagraphs($contentDiv);
	}
}

?>