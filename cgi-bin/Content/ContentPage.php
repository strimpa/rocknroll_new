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
	
	public function ContentPage()
	{
		$this->menu = new SubMenu();
		$this->article = new Article();
		$this->isDirty = true;
	}
	
	public function &GetMenu()
	{
		return $this->menu;
	}
	public function &GetArticle()
	{
		return $this->article;
	}

	public function Render()
	{
//		print "ContentPage::RENDER\n";
		$builder = ContentMgr::GetInstance()->GetBuilder();
		if($this->isDirty)
		{
			$this->blob = $builder->Reset();

	//		print("NAME:".$this->blob->nodeName."\n");
			$this->menu->Render($this->blob);
			$builder = ContentMgr::GetInstance()->GetBuilder();
			$this->article->Render($this->blob);
		}
	    $builder->Render();
	}
	
};

?>