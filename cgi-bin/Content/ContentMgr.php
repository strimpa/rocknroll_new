<?php

require_once('HtmlBuilder.php');
require_once(dirname(__FILE__) . '/../Utils.php');
require_once(dirname(__FILE__) . '/../Aufenthalt.php');
require_once('ContentFactory.php');

class ContentMgr
{
	private $htmlBuilder;
//	private $content;
	private static $instance;
	private $navi;
	private $content;
	
	private function ContentMgr()
	{
		$this->Init();
	}
	
	private function Init()
	{
		/* Set internal character encoding to UTF-8 */
		mb_internal_encoding("UTF-8");
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
		$this->navi = ContentFactory::GetInstance()->CreateMainNavi();
		$currId = FilenameFromUrl();
		PrintHtmlComment("currId:$currId");
		if($currId=="")
			$currId = "start";
		$this->content = ContentFactory::GetInstance()->CreateContentPages($currId);
	}
	
	public function &GetBuilder()
	{
		return $this->htmlBuilder;
	}
	
	public function RenderHeader()
	{
		if(NULL!=$this->content)
			$this->content->RenderHeader();
	}
	
	public function RenderContent()
	{
		global $loadingErrors;
//		print ("current content:".FilenameFromUrl()."\n");
//		print ("num content:".count($this->content)."\n");
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

		if(NULL!=$this->content)
			$this->content->Render();
		else
			print "Error creating content!";
		
		print "<div id='loadingErrors'>$loadingErrors</div>";
	}

}


?>