<?php

class MainNavi
{
	private $bgPic;
	private $titlesAndUrls;
	private $urlPrefix;
	
	public function MainNavi()
	{
		$this->urlPrefix = "/index/";
	} 
	
	public function SetPic($pic)
	{
		$this->bgPic = $pic;
	}
	
	public function SetTitles($titles)
	{
		$this->titlesAndUrls = $titles;
	}
	
	public function Render()
	{
		$builder = ContentMgr::GetInst()->GetBuilder();
		
		$menuDiv = $builder->AddTag("div", "menuLeft", NULL);
		$titles = array_keys($this->titlesAndUrls);
		PrintHtmlComment("Main navi render:".count($this->titlesAndUrls));
		for($menuIndex = 0;$menuIndex<count($this->titlesAndUrls); $menuIndex++)
		{
			if(strlen($titles[$menuIndex])<=0)
				continue;
			$linkNode = $builder->AddTag("a");
			$link = $this->urlPrefix.$this->titlesAndUrls[$titles[$menuIndex]];
			PrintHtmlComment($link);
			$linkNode->setAttribute("href", $link);
			$newEntry = $builder->AddTag("div", "mainNaviEntry_".$titles[$menuIndex], "mainNaviEntry", $titles[$menuIndex]);
			$linkNode->appendChild($newEntry);
		    $menuDiv->appendChild($linkNode);
		}
		$builder->GetDoc()->appendChild($menuDiv);
		$builder->Render();
	}
}

?>