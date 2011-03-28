<?php

require_once("Verbindung.php");

class DBController
{
	private $meineVerbindung; 

	public function DBController()
	{
		$this->meineVerbindung = new Verbindung($this, "verbindung");
	}
	
	public function GetTableContent($table, $fields, $requirements = NULL)
	{
		return $this->meineVerbindung->GetTableContent($table, $fields, $requirements);
	}
	public function SetTableContent($table, $fields, $requirements = NULL, $values = NULL)
	{
		return $this->meineVerbindung->GetTableContent($table, $fields, $requirements, $values);
	}
	public function InsertTableContent($table, $fields, $requirements = NULL)
	{
		return $this->meineVerbindung->InsertTableContent($table, $fields, $requirements);
	}
	public function GetContent($id)
	{
		return $this->meineVerbindung->GetTableContent("pages", "*", array("identifier"=>$id));
	}
	
	public function GetMenu($idval)
	{
		$result = $this->meineVerbindung->GetTableContent("submenus", "*", array("id"=>$idval));
		assert(count($result)==1);
		$result= $result[0];
		return $result;
	}

	public function GetParagraph($idval)
	{
		$result = $this->meineVerbindung->GetTableContent("paragraphs", "*", array("id"=>$idval));
		assert(count($result)==1);
		$result= $result[0];
		return $result;
	}
	
	public function GetPicData($picID)
	{
		$result = $this->meineVerbindung->GetTableContent("pictures", "*", array("id"=>$picID));
		assert(count($result)==1);
		$result= $result[0];
		return $result;
	}
}

?>