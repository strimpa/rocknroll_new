<?php

include("ContentPage.php");
include("MainNavi.php");

class ContentFactory
{
	private static $instance;
	
	/**
	 * 
	 * Hide constructor
	 */
	private function ContentFactory()
	{}
	
	public static function &GetInst()
	{
		if(!isset(self::$instance))
		{
//			$_SESSION['ContentMgr'] = NULL;
			if(!isset($_SESSION['ContentFactory']))
			{
//				PrintHtmlComment('New ContentMgr instance!!!');
				self::$instance = new ContentFactory();
				$_SESSION['ContentFactory'] = self::$instance;
			}
			else
			{
				self::$instance = $_SESSION['ContentFactory'];
//				PrintHtmlComment("session manager.");
			}
		}
		return self::$instance;
	}
	
	public function IterateOverFields($result, $fieldArray, $func, $target)
	{
		$lastFieldEntryCount = NULL;
		$newFields = array();
		foreach($fieldArray as $fields)
		{
//			$explodedArray = array();
			$field = $result[$fields];
			$explodedArray = explode("|", $field);
			array_push($newFields, $explodedArray);
		}
		
//			assert(count($titleArray)==count($linkArray));
		if(count($newFields)<=0)
			return;
		for($k=0;$k<count($newFields[0]);$k++)
		{
			$paramArray = array();
			for($i=0;$i<count($newFields);$i++)
			{
				$theField = "";
				if(count($newFields[$i])>$k)
					$theField = $newFields[$i][$k];
//				print $theField;
				array_push($paramArray, $theField);
			}
			$this->$func($target, $paramArray);
		}
	}
	
	public function AddMenuCallback($target, $params)
	{
		if($params[0]!="")
		{
			$mt = new MenuTuple($params[0], $params[1]);
			$target->AddMenuEntry($mt);
		}
	}

	public function CreateParagraph($paraResult)
	{
		$p = NULL;
		PrintHtmlComment("type:".$paraResult["type"]);
		switch($paraResult["type"])
		{
			case iParagraph::eTYPE_PIC_RIGHT:
				$p = new PicPara(iParagraph::eTYPE_PIC_RIGHT);
				$p->Init($paraResult["title"], $paraResult["meta"], $paraResult["content"]);
				break;
			case iParagraph::eTYPE_PIC_LEFT:
				$p = new PicPara(iParagraph::eTYPE_PIC_LEFT);
				$p->Init($paraResult["title"], $paraResult["meta"], $paraResult["content"]);
				break;
			case iParagraph::eTYPE_TABLE:
				$p = new TablePara();
				$p->Init($paraResult["title"], $paraResult["meta"], NULL);
				break;
			default:
				break;
		}
		return $p;
	}
	
	public function &CreateMainNavi()
	{
		$result = DBCntrl::GetInst()->GetNavi();
		$theMenu = new MainNavi();
		$titlesAndLinks = array();
		foreach($result as $entry)
		{
			// Menu entries
			$pageIdentifier = DBCntrl::GetInst()->GetPageIdentifier($entry["pageRef"]);
			$titlesAndLinks[$entry['title']] = $pageIdentifier['identifier'];
		}
		$theMenu->SetTitles($titlesAndLinks);
		return $theMenu;
	}
	
	public function CreateContentPages($id)
	{
		$result = DBCntrl::GetInst()->GetContent($id);
		if(count($result)<=0)
			$result = DBCntrl::GetInst()->GetContent("start");
		
		foreach($result as $pageData)
		{
			$newPage = new ContentPage($pageData);
			
			// add
			return $newPage;
		}
	}

}

?>