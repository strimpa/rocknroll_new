<?php

require_once("Verbindung.php");

class DBController
{
	private $meineVerbindung; 

	public function DBController()
	{
		$this->meineVerbindung = new Verbindung($this, "verbindung");
	}
	public function &Conn()
	{
		return $this->meineVerbindung;
	}
	
	public function GetContent($id)
	{
		return $this->meineVerbindung->GetTableContent(
			array(
				'table'=>"pages", 
				'requirements'=>array("identifier"=>$id)
				));
	}
	public function GetNavi()
	{
		return $this->meineVerbindung->GetTableContent(
			array(
				'table'=>"navigation", 
				'orderBy'=>"priority"
				));
	}
	public function GetPageIdentifier($id)
	{
		$result = $this->meineVerbindung->GetTableContent(
			array(
				'table'=>"pages", 
				'fields'=>"identifier", 
				'requirements'=>array("id"=>$id)
				));
		assert(count($result)==1);
		$result= $result[0];
		return $result;
	}
	
	public function GetMenu($idval)
	{
		$result = $this->meineVerbindung->GetTableContent(
			array(
				'table'=>"submenus", 
				'requirements'=>array("id"=>$idval)
				));
		if(count($result)!=1)
			return NULL;
		else
			return $result[0];
	}

	public function GetParagraph($idval)
	{
		$result = $this->meineVerbindung->GetTableContent(
			array(
				'table'=>"paragraphs", 
				'requirements'=>array("id"=>$idval)
				));
		if(count($result)!=1)
			return NULL;
		else
			return $result[0];
	}
	
	public function GetPicData($picID)
	{
		$result = $this->meineVerbindung->GetTableContent(
			array(
				'table'=>"pictures", 
				'requirements'=>array("id"=>$picID)
				));
		assert(count($result)==1);
		$result= $result[0];
		return $result;
	}
	
}

?>