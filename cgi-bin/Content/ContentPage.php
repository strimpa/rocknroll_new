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
	
	public function ContentPage($pageData)
	{
		global $loadingErrors;
		$this->menu = new SubMenu();
		
		$pluginType = $pageData['plugin'];

		if(NULL!=$pluginType)
			$this->article = new DelegateArticle($pluginType);
		else
			$this->article = new Article();
		//			$this->article = new FrameArticle($url);
		$this->isDirty = true;
		
		// Menu entries
		$menuRef = DBCntrl::GetInst()->GetMenu($pageData["menuRef"]);
		ContentFactory::GetInst()->IterateOverFields($menuRef, array("entries","links"), 'AddMenuCallback', $this->menu);
		
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
				$paragraph = DBCntrl::GetInst()->GetParagraph($index);
				if(NULL==$paragraph)
					$loadingErrors .= "Paragraph $index not found in DB.";
				else
				{
					foreach($paragraph as $paramInfo)
						PrintHtmlComment("paragraph info:".$paramInfo);
					$this->article->AddParagraph(ContentFactory::GetInst()->CreateParagraph($paragraph));
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
		$builder = ContentMgr::GetInst()->GetBuilder();
		if($this->isDirty)
		{
			$this->blob = $builder->Reset();
			$this->menu->Render($this->blob);
			$builder = ContentMgr::GetInst()->GetBuilder();
			$this->article->Render($this->blob);
		}
	    $builder->Render();
	}
	
};