<?php
/**
* Diese KLasse beschreibt den Aufenthalt eines Besuchers der Site
*/
require_once("Benutzer.php");
require_once("Linkausgabe.php");
require_once("Order/Bestellablauf.php");
require_once("Controller.php");

global $loadingErrors;

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
		$this->aktuellerNutzer = new Benutzer();
		$this->meineLinks = new Linkausgabe(0);
	}
	
	public static function &GetInstance()
	{
		if(!isset(self::$instance))
		{
			if(!isset($_SESSION['Aufenthalt']))
			{
//				PrintHtmlComment('New Aufenthalt instance!!!');
				self::$instance = new Aufenthalt();
				$_SESSION['Aufenthalt'] = self::$instance;
			}
			else
			{
				self::$instance = $_SESSION['Aufenthalt'];
//				PrintHtmlComment("session Aufenthalt.");
			}
		}
		return self::$instance;
	}
	
	public function &Controller()
	{
		return $this->dbConn;
	}

	public function &DBConn()
	{
		return $this->dbConn->Conn();
	}

	function &GetAblauf()
	{
		if(NULL==$this->aktuellerBestellAblauf)
		{
			PrintHtmlComment("Creating new Ablauf");
			$this->aktuellerBestellAblauf = new BestellAblauf($this);
		}
	
		return $this->aktuellerBestellAblauf;
	}
	
	function &GetUser()
	{
		return $this->aktuellerNutzer;
	}
	
	function &Links()
	{
		return $this->meineLinks;
	}
}
?>