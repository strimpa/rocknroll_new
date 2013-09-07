<?php

/*
** Class to handle a content website
*/
require_once("MenuTuple.php");
require_once("SubMenu.php");
require_once("Article.php");

class ContentPage
{
	private $menu;
	private $article;
	private $isDirty;
	private static $doc;
	
	public function ContentPage($pageData, $articleType=NULL, $url=NULL)
	{
		global $loadingErrors;
		$this->menu = new SubMenu();
		if(NULL!=$articleType)
			$this->article = new DelegateArticle($articleType);
		else if(NULL!=$url)
			$this->article = new FrameArticle($url);
		else
			$this->article = new Article();
		$this->isDirty = true;
		
		// Menu entries
		$menuRef = Aufenthalt::GetInstance()->Controller()->GetMenu($pageData["menuRef"]);
		ContentFactory::GetInstance()->IterateOverFields($menuRef, array("entries","links"), 'AddMenuCallback', $this->menu);
		
		// Article
		$this->article->SetTitle($pageData["title"]);
		if("NULL"!=$pageData["paragraphs"] && NULL!=$pageData["paragraphs"])
		{
			$paraIndeces = explode(",",$pageData["paragraphs"]);
			foreach($paraIndeces as $index)
			{
				if($index == "")
					continue;
				PrintHtmlComment("one paragraph:".$index);
				$paragraph = Aufenthalt::GetInstance()->Controller()->GetParagraph($index);
				if(NULL==$paragraph)
					$loadingErrors .= "Paragraph $index not found in DB.";
				else
				{
					foreach($paragraph as $paramInfo)
						PrintHtmlComment("paragraph info:".$paramInfo);
					$this->article->AddParagraph(ContentFactory::GetInstance()->CreateParagraph($paragraph));
				}
			}
		}
	}
	
	public function &GetMenu()
	{
		return $this->menu;
	}
	public function &GetArticle()
	{
		return $this->article;
	}
	
	public function RenderHeader()
	{
		return $this->article->RenderHeader();
	}

	public function Render()
	{
//		print "ContentPage::RENDER\n";
		$builder = ContentMgr::GetInstance()->GetBuilder();
		if($this->isDirty)
		{
			$this->blob = $builder->Reset();
			$this->menu->Render($this->blob);
			$builder = ContentMgr::GetInstance()->GetBuilder();
			$this->article->Render($this->blob);
		}
	    $builder->Render();
	}
	
};
/*
class PloggerPage extends ContentPage
{
	private $menu;
	private $article;
	private $isDirty;
	private static $doc;
	
	public function PloggerPage()
	{
		$this->menu = new SubMenu();
		$this->article = new Article();
		$this->isDirty = true;
	}
	
	public function RenderHeader()
	{
//		the_gallery_head();
	}
	
	public function Render()
	{
		$builder = ContentMgr::GetInstance()->GetBuilder();
		$this->menu->Render($this->blob);
		$builder->Render();
		print '<div class="redbordered" id="contentBG">';
		print '<div id="contentLeftOverlap"><img alt="" src="/images/layout/BG_04.jpg" /></div>';
		print '<div id="content">';
//		the_gallery();
		print '<iframe src="/plogger/" id="ploggerFrame" />';
		print "</div>";
		print "</div>";
	}
};
*/

?>