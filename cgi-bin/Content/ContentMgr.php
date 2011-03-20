<?php

include("HtmlBuilder.php");
include("/../Utils.php");
include("/../Aufenthalt.php");
include("ContentFactory.php");

class ContentMgr
{
	private $htmlBuilder;
//	private $content;
	private $contentFactory;
	private static $instance;
	
	private function ContentMgr()
	{
		$this->htmlBuilder = new HtmlBuilder();
		$this->contentFactory = new ContentFactory();
	}
	
	private function Init()
	{
//		$this->content = array();
//		print("count($this->content):".count($this->content));
	}
	
	public static function &GetInstance()
	{
		if(!isset(self::$instance))
		{
//			print 'New ContentMgr instance!!!\n';
			self::$instance = new ContentMgr();
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
	
	public function RenderContent()
	{
//		print ("current content:".FilenameFromUrl()."\n");
//		print ("num content:".count($this->content)."\n");
		$content = $this->contentFactory->CreateContentPages(FilenameFromUrl());
		if(NULL!=$content)
			$content->Render();
		else
			print "Error creating content!";
	}

}


?>