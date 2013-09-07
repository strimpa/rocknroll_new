<?php
/**
* Diese KLasse behandelt Seiten die eine Übersicht über vorhandene Tabellen und deren Inahlte anzeigen
**/
class DynPage
{
var $myName;
var $hintergrundbild;
var $tabellenUeberschriften;
var $ablauf;

	function DynPage($derAblauf,$gewuenschteInhalte){
		$this->ablauf = $derAblauf;
		$this->myName = $gewuenschteInhalte;
		$this->hintergrundbild = "/_pics/BG_".$this->myName;
	}
	function melden(){
		print "Hallo!";
	}
	
	function printHeader(){
		
	}

}
?>