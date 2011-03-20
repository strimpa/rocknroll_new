<?php
/**
* Diese KLasse beschreibt den Aufenthalt eines Besuchers der Site
*/
require_once("Benutzer.php");
require_once("Linkausgabe.php");
require_once("Order/Bestellablauf.php");
require_once("COntroller.php");

class Aufenthalt
{
	private static $instance;
	private $dbConn;
	private $meinName;
	private $meineId;
	private $aktuellerNutzer;
	private $aktuellerBestellAblauf;
	private $meineTermine;
	private $meineLinks;

	private function Aufenthalt()
	{
		$this->dbConn = new DBCOntroller();
		$this->aktuellerNutzer = new Benutzer($this);
		$this->meineLinks = new Linkausgabe($this, 0);
	}
	
	public static function &GetInstance()
	{
		if(!isset(self::$instance))
		{
			self::$instance = new Aufenthalt();
		}
		return self::$instance;
	}
	
	public function &GetConn()
	{
		return $this->dbConn;
	}

	function neuerBestellAblauf()
	{
		$this->aktuellerBestellAblauf = new BestellAblauf($this);
	}
}
?>