<?php

include("ContentPage.php");

class ContentFactory
{
	private function IterateOverFields($result, $fieldArray, $func, $target)
	{
		$lastFieldEntryCount = NULL;
		$newFields = array();
		foreach($fieldArray as $fields)
		{
//			$explodedArray = array();
			$field = $result[$fields];
			$explodedArray = explode(",", $field);
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
		$mt = new MenuTuple($params[0], $params[1]);
		$target->AddMenuEntry($mt);
	}

	public function CreateParagraph($paraResult, $target)
	{
		$p = NULL;
		switch($paraResult["type"])
		{
			case iParagraph::eTYPE_PIC_RIGHT:
				$p = new PicPara(iParagraph::eTYPE_PIC_RIGHT);
			break;
			case iParagraph::eTYPE_PIC_LEFT:
				$p = new PicPara(iParagraph::eTYPE_PIC_LEFT);
			break;
			default:
			break;
		}
		$p->Init($paraResult["title"], $paraResult["meta"], $paraResult["content"]);
		$target->AddParagraph($p);
	}

	public function CreateContentPages($id)
	{
		print ("content id:".$id."\n");
		$dbConn = Aufenthalt::GetInstance()->GetConn();
		$result = $dbConn->GetContent($id);
		print("Content count:".count($result));
		foreach($result as $page)
		{
			// inital create
			$newPage = new ContentPage();
			$pageSubMenu = $newPage->GetMenu();
			
			// Menu entries
			$menuRef = $dbConn->GetMenu($page["menuRef"]);
			$this->IterateOverFields($menuRef, array("entries","links"), 'AddMenuCallback', $pageSubMenu);
			
			// Article
			$article = $newPage->GetArticle();
			$article->SetTitle($page["title"]);
			$paraIndeces = explode(",",$page["paragraphs"]);
			foreach($paraIndeces as $index)
			{
				$paragraph = $dbConn->GetParagraph($index);
				$this->CreateParagraph($paragraph, $article);
			}
			
			// add
			return $newPage;
		}
	}

}

?>