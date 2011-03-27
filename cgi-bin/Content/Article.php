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
		$builder->AddStyle($contentDiv, ("height:".$currentOffset."px;"));
	}
	
	public function Render(&$parentNode)
	{
		$builder = ContentMgr::GetInstance()->GetBuilder();
		
		$contentDiv = $builder->AddTag("div", "content", "redbordered");
		$parentNode->appendChild($contentDiv);
		
		$contentDiv->appendChild($builder->AddTag("div", "articleTitle", NULL, $this->title));
		$this->RenderParagraphs($contentDiv);
	}
}

?>