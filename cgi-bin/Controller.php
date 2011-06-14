<?php

require_once("Verbindung.php");

class DBController
{
	private $meineVerbindung; 

	public function DBController()
	{
		$this->meineVerbindung = new Verbindung($this, "verbindung");
	}
	
	public function GetTableContent($table, $fields, $requirements = NULL, $useRegExp = FALSE)
	{
		return $this->meineVerbindung->GetTableContent($table, $fields, $requirements, $useRegExp);
	}
	public function SetTableContent($table, $fields, $requirements = NULL, $values = NULL)
	{
		return $this->meineVerbindung->SetTableContent($table, $fields, $requirements, $values);
	}
	public function InsertTableContent($table, $fields=NULL, $requirements = NULL)
	{
		return $this->meineVerbindung->InsertTableContent($table, $fields, $requirements);
	}
	public function DropTableContent($table, $requirements = NULL)
	{
		return $this->meineVerbindung->DropTableContent($table, $requirements);
	}
	public function GetContent($id)
	{
		return $this->meineVerbindung->GetTableContent("pages", "*", array("identifier"=>$id));
	}
	public function GetNavi()
	{
		return $this->meineVerbindung->GetTableContent("navigation", "*");
	}
	public function GetPageIdentifier($id)
	{
		$result = $this->meineVerbindung->GetTableContent("pages", array("identifier"), array("id"=>$id));
		assert(count($result)==1);
		$result= $result[0];
		return $result;
	}
	
	public function GetMenu($idval)
	{
		$result = $this->meineVerbindung->GetTableContent("submenus", "*", array("id"=>$idval), FALSE);
		assert(count($result)==1);
		$result= $result[0];
		return $result;
	}

	public function GetParagraph($idval)
	{
		$result = $this->meineVerbindung->GetTableContent("paragraphs", "*", array("id"=>$idval), FALSE);
		assert(count($result)==1);
		$result= $result[0];
		return $result;
	}
	
	public function GetPicData($picID)
	{
		$result = $this->meineVerbindung->GetTableContent("pictures", "*", array("id"=>$picID), FALSE);
		assert(count($result)==1);
		$result= $result[0];
		return $result;
	}
	
	public function gibUserInDB($user)
	{
		return $this->meineVerbindung->gibUserInDB($user);
	}
	public function gibBestellungInDB(&$user, &$aktuelleBestellung)
	{
		return $this->meineVerbindung->gibBestellungInDB($user, $aktuelleBestellung);
	}
}

?>