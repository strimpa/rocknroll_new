<?php

require_once("MenuTuple.php");

class SubMenu
{
	private $entries;

	public function SubMenu()
	{
		$this->entries = array();
	}
	
	public function AddMenuEntry($menuTuple)
	{
		array_push($this->entries, $menuTuple);
	}

	public function Render(&$parentNode)
	{
		$builder = ContentMgr::GetInst()->GetBuilder();
		
		for($menuIndex = 0;$menuIndex<count($this->entries); $menuIndex++)
		{
			$newEntry = &$builder->AddMenuEntry($this->entries[$menuIndex], $menuIndex);
		    $parentNode->appendChild($newEntry);
		}	
	}
	
}

?>