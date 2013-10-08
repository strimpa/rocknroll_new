<?php

include("Paragraph.php");


class Article
{
	const DELEGATE_ARTICLE_PLOGGER = 1;
	const DELEGATE_ARTICLE_LINKS = 2;
	const DELEGATE_ARTICLE_ORDER = 3;
		
	private $title;
	private $paragraphs;
	
	public function __construct()
	{
		$this->paragraphs = array();
	}
	
	public function SetTitle($t)
	{
		$this->title = $t;
	}
	
	public function AddParagraph($para)
	{
		if(!is_array($this->paragraphs))
			$this->paragraphs = array();
		array_push($this->paragraphs, $para);
	}
	
	public function RenderHeader()
	{
		print "<title>$this->title</title>";
	}
	
	public function RenderParagraphs(&$contentDiv)
	{
		$builder = ContentMgr::GetInst()->GetBuilder();
		$currentOffset = 20;
		if(count($this->paragraphs)<=0)
		{
			$builder = ContentMgr::GetInst()->GetBuilder();
	
			$contentDiv->appendChild($builder->AddTag("div", "noContantWarning", NULL, "The Page contains no paragraphs and has no plugin defined either."));
		}
		foreach($this->paragraphs as $para)
		{
			if(is_a($para, "iParagraph"))
			    $para->Render($contentDiv, $currentOffset);
		}	
		// if($currentOffset!=20)
			// $builder->AddStyle($contentDiv, ("height:".$currentOffset."px;"));
	}
	
	private function RenderPageOverlay($contentDiv)
	{
		$builder = ContentMgr::GetInst()->GetBuilder();

		$menuPicDiv = $builder->AddTag("div", "contentLeftOverlap");
	    $pic = $builder->GetDoc()->createElement( "img" );
		$pic->setAttribute("src", "/images/layout/BG_04.jpg");
		$menuPicDiv->appendChild($pic);
		$contentDiv->appendChild($menuPicDiv);
	}
	
	public function RenderFooter($contentDiv)
	{
		$builder = ContentMgr::GetInst()->GetBuilder();

		$footerDiv = $builder->AddTag("div", "footerDiv", NULL);
		$contentDiv->appendChild($footerDiv);
		$footerDiv->appendChild($builder->CreateImage("/images/layout/FooterDeko.jpg"));
	}
	
	public function Render(&$parentNode)
	{
		$builder = ContentMgr::GetInst()->GetBuilder();

		$parentNode->appendChild($builder->AddTag("div", "articleTitle", NULL, $this->title));
		
		$contentBGBorderDiv = $builder->AddTag("div", "contentBGBorder");
		$contentBGDiv = $builder->AddTag("div", "contentBG");
		$contentBGBorderDiv->appendChild($contentBGDiv);
		$parentNode->appendChild($contentBGBorderDiv);
		
		$contentDiv = $builder->AddTag("div", "content");
		$contentBGDiv->appendChild($contentDiv);
		
		$this->RenderPageOverlay($contentBGDiv);
		
		$this->RenderParagraphs($contentDiv);
		
		$this->RenderFooter($contentBGBorderDiv);
	}
}

class DelegateArticle extends Article
{
	private $delegate = null;
	
	public function DelegateArticle($type)
	{
		$plugins = Aufenthalt::GetInst()->GetPlugins();
		if(array_key_exists($type, $plugins))
		{
			$this->delegate = $plugins[$type];
		}
		else {
			PrintHtmlComment("Couldn't find plugin ".$type);			
		}
	}
	
	public function RenderHeader()
	{
		Article::RenderHeader();
		PrintHtmlComment("start render plugin header");
		if(null!=$this->delegate)
		{
			PrintHtmlComment("Delegate:".get_class($this->delegate));
			$this->delegate->RenderHeader();
		}
		PrintHtmlComment("end render plugin header");
	}

	public function RenderParagraphs(&$contentDiv)
	{
		global $queryDir;
		$getVars = GetGETVars();
		$queryDir = "";
		if(array_key_exists("dir", $getVars))
			$queryDir = $getVars["dir"];

		if(null!=$this->delegate)
			$this->delegate->RenderContent($contentDiv);
	}
}

class FrameArticle extends Article
{
	private $url;
	
	public function FrameArticle($url)
	{
		$this->url = $url;
	}

	public function RenderParagraphs(&$contentDiv)
	{
		$builder = ContentMgr::GetInst()->GetBuilder();
		$importdoc = new DOMDocument();
		$importdoc->encoding = 'UTF-8';
$htmlStr = <<<EOD
<p style="margin-left:10px;">
	<a href="http://www.rock-around.de/system-cgi/guestbook/guestbook.php?action=sign" target="innerFrame">Ins G&auml;stebuch eintragen</a>
	 | <a href="http://www.rock-around.de/system-cgi/guestbook/guestbook.php?action=view" target="innerFrame">G&auml;stebuch anschauen</a>
</p>
EOD;
		$importdoc->loadHTML('<?xml version="1.0" encoding="UTF-8" ?>'.utf8_encode($htmlStr));
		$doc = $builder->GetDoc();
		$text = $doc->importNode($importdoc->documentElement, true);
		$contentDiv->appendChild($text);
		
		
		$builder = ContentMgr::GetInst()->GetBuilder();
//		<iframe src="/plogger/" id="ploggerFrame" />
		$iframe = $builder->AddTag("iframe", "articleframe", NULL);
		$iframe->setAttribute("name", "innerFrame");
		$iframe->setAttribute("src", $this->url);
		
		$contentDiv->appendChild($iframe);
	}
}

?>