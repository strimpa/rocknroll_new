<?php

include('HtmlBuilder.php');
include(dirname(__FILE__) . '/../Utils.php');
include(dirname(__FILE__) . '/../Aufenthalt.php');
include('ContentFactory.php');

class ContentMgr
{
	private $htmlBuilder;
//	private $content;
	private $contentFactory;
	private static $instance;
	private $navi;
	
	private function ContentMgr()
	{
		$this->contentFactory = new ContentFactory();
		$this->Init();
	}
	
	private function Init()
	{
		$this->htmlBuilder = new HtmlBuilder();
	}
	
	public static function &GetInstance()
	{
		if(!isset(self::$instance))
		{
//			$_SESSION['ContentMgr'] = NULL;
			if(!isset($_SESSION['ContentMgr']))
			{
//				PrintHtmlComment('New ContentMgr instance!!!');
				self::$instance = new ContentMgr();
				$_SESSION['ContentMgr'] = self::$instance;
			}
			else
			{
				self::$instance = $_SESSION['ContentMgr'];
//				PrintHtmlComment("session manager.");
			}
			self::$instance->Init();
		}
		return self::$instance;
	}
	
	public function Genesis()
	{
	}
	
	public function &GetBuilder()
	{
		return $this->htmlBuilder;
	}
	public function &GetFactory()
	{
		return $this->contentFactory;
	}
	
	public function RenderContent()
	{
//		print ("current content:".FilenameFromUrl()."\n");
//		print ("num content:".count($this->content)."\n");
		$this->navi = $this->contentFactory->CreateMainNavi();
		if(NULL!=$this->navi)
		{
			PrintHtmlComment("Navigation should render here!");
			$this->navi->Render();
		}
		else
		{
			PrintHtmlComment("Error creating navigation!");
			print "Error creating navigation!";
		}

		PrintHtmlComment("WTF?!");
		
		$currId = FilenameFromUrl();
		if($currId=="")
			$currId = "index";
		$content = $this->contentFactory->CreateContentPages($currId);
		if(NULL!=$content)
			$content->Render();
		else
			print "Error creating content!";
	}

}


?>