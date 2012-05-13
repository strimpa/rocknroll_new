<?php

include("Paragraph.php");


class Article
{
	const DELEGATE_ARTICLE_PLOGGER = 1;
	const DELEGATE_ARTICLE_LINKS = 2;
		
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
	
	public function RenderHeader()
	{
print <<<EOD
	<link rel="stylesheet" href="/MiniGal/css/mediaboxAdvWhite.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="/MiniGal/css/Gallery.css" type="text/css" media="screen" />
	<script src="/MiniGal/js/mootools.js" type="text/javascript"></script>
	<script src="/MiniGal/js/mediaboxAdv-1.3.4b.js" type="text/javascript"></script>
EOD;
	}
	
	public function RenderParagraphs(&$contentDiv)
	{
		$builder = ContentMgr::GetInstance()->GetBuilder();
		$currentOffset = 20;
		foreach($this->paragraphs as $para)
		{
		    $para->Render($contentDiv, $currentOffset);
		}	
		if($currentOffset!=20)
			$builder->AddStyle($contentDiv, ("height:".$currentOffset."px;"));
	}
	
	public function Render(&$parentNode)
	{
		$builder = ContentMgr::GetInstance()->GetBuilder();

		$parentNode->appendChild($builder->AddTag("div", "articleTitle", NULL, $this->title));
		
		$contentBGDiv = $builder->AddTag("div", "contentBG", "redbordered");
		$menuPicDiv = $builder->AddTag("div", "contentLeftOverlap");
	    $pic = $builder->GetDoc()->createElement( "img" );
		$pic->setAttribute("src", "/images/layout/BG_04.jpg");
		$menuPicDiv->appendChild($pic);
		$contentBGDiv->appendChild($menuPicDiv);
		$parentNode->appendChild($contentBGDiv);
		
		$contentDiv = $builder->AddTag("div", "content");
		$contentBGDiv->appendChild($contentDiv);
		
		$this->RenderParagraphs($contentDiv);
		$footerDiv = $builder->AddTag("div", "footerDiv", NULL);
		$contentBGDiv->appendChild($footerDiv);
		$footerDiv->appendChild($builder->CreateImage("/images/layout/FooterDeko.jpg"));
	}
}

class DelegateArticle extends Article
{
	private $type = Article::DELEGATE_ARTICLE_PLOGGER;
	
	public function DelegateArticle($url)
	{
		$this->type = $url; 
	}

	public function RenderDelegate()
	{
		global $queryDir;
		switch($this->type)
		{
			case Article::DELEGATE_ARTICLE_PLOGGER:
//				$queryStart = strrpos($_SERVER['REQUEST_URI'], "&");
//				$wholeQueryString = substr($_SERVER['REQUEST_URI'], $queryStart);
//				$reqString ='http://'.$_SERVER['SERVER_NAME'].'/MiniGal/index.php?'.$wholeQueryString;
				$getVars = GetGETVars();
				$queryDir = "";
				if(array_key_exists("dir", $getVars))
					$queryDir = $getVars["dir"];
				include 'MiniGal/index.php';
				break;
			case Article::DELEGATE_ARTICLE_LINKS:
				include 'links.php';
				break;
		}
	}
	
	public function RenderParagraphs(&$contentDiv)
	{
		$builder = ContentMgr::GetInstance()->GetBuilder();
		//<iframe src="/plogger/" id="ploggerFrame" />
		//$iframe = $builder->AddTag("iframe", "ploggerFrame", NULL);
//		$iframe->setAttribute("src", $this->url);
		
		ob_start();
		$this->RenderDelegate();
		$str=ob_get_contents();
		ob_end_clean();	
		$importdoc = new DOMDocument();
		$importdoc->encoding = 'UTF-8';
		$importdoc->loadHTML('<?xml encoding="UTF-8">'.$str);
		$doc = $builder->GetDoc();
		$text = $doc->importNode($importdoc->documentElement, true);
		$contentDiv->appendChild($text);
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
		$builder = ContentMgr::GetInstance()->GetBuilder();
		$importdoc = new DOMDocument();
		$importdoc->encoding = 'UTF-8';
$htmlStr = <<<EOD
<p style="margin-left:10px;">
	<a href="http://www.rock-around.de/system-cgi/guestbook/guestbook.php?action=sign" target="innerFrame">Ins G�stebuch eintragen</a>
	 | <a href="http://www.rock-around.de/system-cgi/guestbook/guestbook.php?action=view" target="innerFrame">G�stebuch anschauen</a>
</p>
EOD;
		$importdoc->loadXML('<?xml version="1.0" encoding="UTF-8" ?>'.utf8_encode($htmlStr));
		$doc = $builder->GetDoc();
		$text = $doc->importNode($importdoc->documentElement, true);
		$contentDiv->appendChild($text);
		
		
		$builder = ContentMgr::GetInstance()->GetBuilder();
//		<iframe src="/plogger/" id="ploggerFrame" />
		$iframe = $builder->AddTag("iframe", "articleframe", NULL);
		$iframe->setAttribute("name", "innerFrame");
		$iframe->setAttribute("src", $this->url);
		
		$contentDiv->appendChild($iframe);
	}
}

?>