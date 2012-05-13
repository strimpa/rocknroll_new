<?php
/**
* Diese Klasse managt die Verbindung zur Datenbank
***/

class ConnSettings
{
	public $table = "";
	public $fields = array(); 
	public $requirements = NULL; 
	public $useRegExp = FALSE;
	public $orderBy = NULL;
	public $distinct=FALSE;
}

class Verbindung
{
	private $ablauf;
	//MySQL Server oder Host
	// server
	private $con;
	// instanz der Datenbank
	private $db;
	// Erbenisse
	private $tableResult;
	
	/***********************************************************************************
	**   Verbindung
	***********************************************************************************/

	function Verbindung($derAblauf, $message)
	{
		$this->ablauf = &$derAblauf;
		$this->verbinde();
		
		// MySQL Query mit der Syntax zum auslesen der Informationen einer
		// gew�hlten MySQL Datenbank Tabelle
		/*$this->tableResult = mysql_query("SELECT * FROM Termine");
		
		 MySQL Query Daten an ein indiziertes Array �bergeben
		$tableData = mysql_fetch_row($this->tableResult);
		
		
		foreach ($tableData as $value) {
			print ($value."\n");
		}
		*/
	}
	
	function verbinde()
	{
		if(null!=$this->db)
			return;
global $db_serv;
	// MySQL Datenbank Name
global $db_name;
	// User
global $db_user;
	// Passwort
global $db_pass;
		$this->db = mysql_connect($db_serv, $db_user, $db_pass) or die('Fehler beim Verbinden zum Datenbankserver!');
		// MySQL Datenbank w�hlen
		mysql_select_db($db_name, $this->db) or die('Fehler beim Verbinden zur Datenbank!');
		mysql_query("SET NAMES utf8", $this->db);
		return true;
	}
	
	private function GetFieldString($params, $fieldParam)
	{
		$fieldString = "*";
		if(isset($params[$fieldParam]))
		{
			if(is_array($params[$fieldParam]))
			{
				if(count($params[$fieldParam])>0)
				{
					$joinString = join("`,`", $params[$fieldParam]);
					$fieldString = "`".$joinString."`";
				}
			}
			else {
				$fieldString = $params[$fieldParam];
			}
		}
		return $fieldString;
	} 

	private function GetReqString($params, $reqParam)
	{
		$reqString = "";
		if(isset($params[$reqParam]) && is_array($params[$reqParam]))
		{
			$index = 0;
			foreach($params[$reqParam] as $key => $value)
			{
				if($index++>0)
					$reqString .= " AND ";
				$value = preg_replace("/%20/", " ", $value);
				if(isset($params['useRegExp']) && $params['useRegExp'])
					$reqString .= $key." REGEXP '".$value."'";
				else
					$reqString .= $key." = '".$value."'";
//		        print("<!-- requirements:".$reqString." //-->\n");
			}
		}
		return $reqString;
	}

	private function GetOrderByString($params, $orderByParam)
	{
		$orderByString = "";
		if(isset($params[$orderByParam]))
		{
			$orderByString = $params[$orderByParam];
			if(is_array($params[$orderByParam]))
			{
				$orderByString = join(',', $params[$orderByParam]);
			}
		}
		return $orderByString;
	} 

	private function GetJoinString($params)
	{
		$joinString = "";
		if(isset($params['joinFields']) && is_array($params['joinFields']) && isset($params['table']))
		{
			$index = 0;
			$tables = explode(",", $params['table']);
			foreach($params['joinFields'] as $key => $value)
			{
				if($index++>0)
					$joinString .= " AND ";
//				$value = preg_replace("/%20/", " ", $value);
				$joinString .= $tables[0].".".$key." LIKE ".$tables[1].".".$value."";
		       // print("<!-- requirements:".$reqString." //-->\n");
			}
		}
		return $joinString;
	}

	/***********************************************************************************
	**   Content
	***********************************************************************************/

	public function GetTableContent($settings)
	{
		$backGabe = array();
		if(!is_array($settings))
		{
			throw new Exception("Invalid Connection Settings object!", 1);
			print "Invalid Connection Settings object!";
			return $backGabe;
		}
		
		$this->verbinde();

		$fieldString = $this->GetFieldString($settings, 'fields');

		$reqString = $this->GetReqString($settings, 'requirements');

		$joinString = $this->GetJoinString($settings);
		
		$orderByString = $this->GetOrderByString($settings, 'orderBy');
		
		$sql = 'SELECT ';
		if(isset($settings['distinct']))
			$sql .= "DISTINCT ";
		$sql .= $fieldString;
        $sql .= ' FROM '.$settings['table'].' '; 
		if($reqString != "")
		{
			$sql .= ' WHERE '.$reqString;
		}
		if($joinString != "")
		{
			$sql .= ' WHERE '.$joinString;
		}
		
		if($orderByString != "")
		{
			$sql .= ' ORDER BY '.$orderByString;
		}
		
		$sql .= ';';
//        print("<!-- sql:".$sql." //-->\n");
		$result = mysql_query($sql);
		if($result && mysql_num_rows($result)>0)
		{
			while($reihe = mysql_fetch_assoc($result))
			{
//				$keys = array_keys($reihe);
//				foreach ($keys as $key)
//	        		print("<!-- key:".$key." //-->\n");
				array_push($backGabe, $reihe);
			}
		}
		return $backGabe;
	}

	public function GetTableDef($settings)
	{
		$backGabe = array();
		if(!is_array($settings))
		{
			throw new Exception("Invalid Connection Settings object!", 1);
			return $backGabe;
		}
		$this->verbinde();
		
		$fieldString = $this->GetFieldString($settings, 'fields');

		$reqString = $this->GetReqString($settings, 'requirements');

		$joinString = $this->GetJoinString($settings);

		$sql = 'SELECT '.$fieldString;
        $sql .= ' FROM '.$settings['table'].' '; 
		if($reqString != "")
		{
			$sql .= ' WHERE '.$reqString;
		}
		if($joinString != "")
		{
			$sql .= ' WHERE '.$joinString;
		}
		
		$sql .= ';';
//        print("<!-- sql:".$sql." //-->\n");
		$result = mysql_query($sql);
		$fields = mysql_num_fields($result);
		$rowArray = array();	
		for ($i=0; $i < $fields; $i++)
		{
			$rowArray[mysql_field_name($result, $i)] = mysql_field_type($result, $i);
		}
		array_push($backGabe,$rowArray);
		return $backGabe;
	} 

	public function DropTableContent($settings)
	{
		$backGabe = array();
		$this->verbinde();
		
		$reqString = $this->GetReqString($settings, 'requirements');
		
		// UPDATE  `rocknroll`.`submenus` SET  `links` =  'The first entry,The second entry,The third entry' WHERE  `submenus`.`id` =1;
		$sql = "DELETE FROM `".$settings['table']."`";
        
		if($reqString != "")
		{
			$sql .= ' WHERE '.$reqString;
		}
		$sql .= ';';
//        print("<!-- sql:".$sql." //-->\n");
		$result = mysql_query($sql);
		print "<!-- Errors: ".mysql_error()."//-->";
		return array($result);
	}

	public function SetTableContent($settings)
	{
		$backGabe = array();
		$this->verbinde();
		
		$reqString = $this->GetReqString($settings, 'requirements');
		
		// UPDATE  `rocknroll`.`submenus` SET  `links` =  'The first entry,The second entry,The third entry' WHERE  `submenus`.`id` =1;
		$sql = "UPDATE `".$settings['table']."` SET "; 
		$fields = $settings['fields'];
		$values = $settings['values'];
        for($fieldIndex = 0; $fieldIndex<count($fields);$fieldIndex++)
        {
        	if('null'==$values[$fieldIndex])
        		continue;
        	if($fieldIndex>0)
        		$sql .= ",";
        	$sql .= "`".$fields[$fieldIndex]."` = '".$values[$fieldIndex]."'";
        }
        
		if($reqString != "")
		{
			$sql .= ' WHERE '.$reqString;
		}
		$sql .= ';';
//        print("<!-- sql:".$sql." //-->\n");
		$result = mysql_query($sql);
//		print "<!-- Errors: ".mysql_error()."//-->";
		return $result;
	}
	
	public function InsertTableContent($settings)
	{
		$backGabe = array();
		$this->verbinde();
		
		// UPDATE  `rocknroll`.`submenus` SET  `links` =  'The first entry,The second entry,The third entry' WHERE  `submenus`.`id` =1;
		$sql = "INSERT INTO `".$settings['table']."` (";
        $keys = $settings['fields'];
        $values = $settings['values'];
		if(is_array($fields) && count($fields)>0)
		{
	        for($fieldIndex = 0; $fieldIndex<count($fields);$fieldIndex++)
	        {
	        	if('null'==$values[$fieldIndex])
	        		continue;
	        	if($fieldIndex>0)
	        		$sql .= ",";
	        	$sql .= "`".$keys[$fieldIndex]."`";
	        }
		}
		$sql .= ') VALUES ('; 
		if(is_array($fields) && count($fields)>0)
		{
			for($fieldIndex= 0; $fieldIndex<count($fields);$fieldIndex++)
	        {
	        	if('null'==$values[$fieldIndex])
	        		continue;
	        	if($fieldIndex>0)
	        		$sql .= ",";
	        	$sql .= "'".$values [$fieldIndex]."'";
	        }
	        
		}
		$sql .= ');';
        print("<!-- sql:".$sql." //-->\n");
		$result = mysql_query($sql);
//		print "<!-- Errors: ".mysql_error()."//-->";
		return array($result);
	}

	public function GetContent($targetArray)
	{
		$backGabe = true;
		$this->verbinde();
		$sql = 'SELECT `title` '
        . ' FROM `pages` '; 
		$result = mysql_query($sql);
		if(mysql_num_rows($result)<1)
		{
			 $backGabe = false;
		}
		else
		{
			while($reihe = mysql_fetch_row($result))
			{
				for($g=0;$g<count($reihe);$g++)
				{
					$title = $reihe[$g];
					print("Title:".$title);
					array_push($targetArray, $title);
				}
			}
		}
		return $backGabe;
	}
	
	/***********************************************************************************
	**   Verbindung
	***********************************************************************************/

	function gibLinksAusFuerRubrik($rubrik){
		$this->verbinde();
		$sql = "SELECT DISTINCT rubrik, beschreibung, url, anlegeDatum FROM links WHERE rubrik LIKE \"$rubrik\""; 
		$result = mysql_query($sql);
		if(!$result)
			print "Aufgetretene Fehler: ".mysql_error();
		return $result;
	}

	function getLinkSections(){
		$this->verbinde();
		$sql = "SELECT DISTINCT rubrik FROM links"; 
		$result = mysql_query($sql);
		if(!$result)
			print "Aufgetretene Fehler: ".mysql_error();
		return $result;
	}
	
	function gibLinksAusFuerSuche($eingabe){
		$this->verbinde();
		print "Sucheingabe \"$eingabe\"";
		$sql = "SELECT DISTINCT `rubrik`,`beschreibung`,`url`,`anlegeDatum` FROM links WHERE LOCATE(\"".$eingabe."\", beschreibung) != 0 OR LOCATE(\"".$eingabe."\", url) != 0"; 
		$result = mysql_query($sql);
		if(mysql_num_rows($result)<1){
			$sql = "SELECT DISTINCT `rubrik`,`beschreibung`,`url`,`anlegeDatum` FROM links WHERE LOCATE(\"".$eingabe."\", LCASE(beschreibung)) != 0 OR LOCATE(\"".$eingabe."\", LCASE(url)) != 0"; 
			$result = mysql_query($sql);
		}
		if(!$result)
			print "Ausgabe: ".mysql_error();
		return $result;
	}

	function gibLinksEin($rubrik, $beschreibung, $link, $anlegeDatum){
		$this->verbinde();
		$sql = "INSERT INTO `links`(rubrik,beschreibung,url,angelegtVon) VALUES ('$rubrik','$beschreibung','$link','$_POST[adminName]')"; 
		$result = mysql_query($sql);
		if(!$result)
			print "Aufgetretene Fehler: ".mysql_error();
	return $result;
	}

	function gibErsteSortierungAus($tabellenName){
		$this->verbinde();
		$sql = "SELECT DISTINCT erstAuswahl FROM " . $tabellenName; 
		$result = mysql_query($sql);
		if(!$result)
			print "Aufgetretene Fehler: ".mysql_error();
	return $result;
	}

	function gibTabelleAus($tabellenName, $whereKlausel){
		if($tabellenName == "Termine" || $tabellenName == "tourneeDaten")
			$spalten = "datum,kuenstler,stadt,location,uhrzeit,url,telNummer";
		$this->verbinde();
		$sql = "SELECT $spalten FROM $tabellenName ".$whereKlausel; 
		$result = mysql_query($sql);
		if(!$result)
			print "Aufgetretene Fehler: ".mysql_error();
	return $result;
	}

	/***********************************************************************************
	**   Benutzer verwaltung
	***********************************************************************************/

		
	function istBenutzerInDb($user){
		$backGabe = true;
		$this->verbinde();
		$sql = 'SELECT * '
        . ' FROM `kunden` '
        . ' WHERE `kundenNr` '
        . ' LIKE \'' . $user->kundenNummer . '\' AND `nachname` '
        . ' LIKE \'' . $user->nachName . '\' LIMIT 0, 30'; 
		$result = mysql_query($sql);
		if(mysql_num_rows($result)<1){
			 $backGabe = false;
		 } 
		 return $backGabe;
	}
	
	function loescheTabelle($dieTabelle){
		$rueckGabe = true;
		print $dieTabelle;
		$this->verbinde();
		$sql = 'TRUNCATE TABLE `kunden`';
		$result = mysql_query($sql);
		print "Aufgetretene Fehler: ".mysql_error();
		$sql = 'TRUNCATE TABLE `bestellung`';
		$result = mysql_query($sql);
		print "\n und".mysql_error();
		if(!$result){
			 $rueckGabe = false;
		 } 
		 return $rueckGabe;
	}
	
	function gibUserInDB($user){
		$rueckGabe = "";
		$abbruch=false;
		// namen der Tabellenspalten
		$kundenNamensArray = array("kundenNr", "anrede", "vorname" , "nachname" , "adresse" , "ort" , "plz" , "land" , 
							"telPrivat" , "email" , "bankName" , "blz" , "ktnr");
		// Werte f�r die DB-Tabelle
		$kundenAusGabeArray = array(	
		// Eingabewerte mit auszugebenden Werten vergleichen
			($user->kundenNummer == "" ? NULL : $user->kundenNummer),
			($user->anrede == "" ? NULL : $user->anrede),
			($user->vorName == "" ? NULL : $user->vorName),
			($user->nachName == "" ? $abbruch=true : $user->nachName),
			($user->adresse == "" ? NULL : $user->adresse),
			($user->ort == "" ? NULL : $user->ort),
			($user->postleitzahl == "" ? NULL : $user->postleitzahl),
			($user->land == "" ? NULL : $user->land),
			($user->telHome == "" ? NULL : $user->telHome),
			($user->eMail == "" ? NULL : $user->eMail),
			($user->bankInstitut == "" ? NULL : $user->bankInstitut),
			($user->blz == "" ? NULL : $user->blz),
			($user->ktnr == "" ? NULL : $user->ktnr));
		if(!$abbruch)
		{
		/**************************************************************
		*          Userdaten
		**************************************************************/
			// Datenbankstring schreiben
			$namensString = "";
			$ausgabeString = "";
			$beginnFlag=false;
			for($k=0;$k<count($kundenNamensArray);$k++){
				if(($kundenAusGabeArray[$k]!=NULL) || 
				   ($kundenAusGabeArray[$k]!=0))
				{
					if($beginnFlag){
						$namensString.=" , ";
						$ausgabeString.=", ";
					}
					$beginnFlag=true;
					$namensString.="`$kundenNamensArray[$k]`";
					$ausgabeString.="'$kundenAusGabeArray[$k]'";
				}
			}
			// Query
			$sql = "INSERT INTO `kunden` ( " . $namensString . " ) VALUES ( " . $ausgabeString . " )";
			
			//Ergebnis		
			$result = mysql_query($sql);
			//echo mysql_affected_rows();
			if(!$result)
			{
				$rueckGabe="".
				"Es Konnte nicht in die Kunden-Datenbank geschrieben werden, bitte versuchen Sie es sp&auml;ter ".
				"noch einmal und/oder berichten sie bitte den Fehler:<br> <a href=\"mailto:schreib@gunnardroege.de\">Mail an Webmaster</a><br>".
				"Vielen Dank f&uuml;r Ihr Verst&auml;ndnis.<br>".mysql_error();
			}
		} 
		else
		{
			$rueckGabe="Keine Verbindung.";
		}
		return $rueckGabe;
	}


	/***********************************************************************************
	**   Bestellung
	***********************************************************************************/
	
	function gibBestellungInDB($user, $aktuelleBestellung)
	{
		$rueckGabe = "";
		$abbruch=false;
		$bestellNamensArray = array("schonKunde", "nachNameBesteller", "kundenNrBesteller", "bestellungen", "kommentar", "bestellDatum");
		$bestellAusGabeArray = array(	
		// Eingabewerte mit auszugebenden Werten vergleichen
			($user->Abonnent == "ja" ? $user->Abonnent : "nein"),
			($user->nachName == "" ? $abbruch=true : $user->nachName),
			($user->kundenNummer == "" ? NULL : $user->kundenNummer),
			(($bestellSumme=$aktuelleBestellung->dbText) == "" ? $abbruch=true : $bestellSumme),
			($aktuelleBestellung->kommentar == "" ? NULL : $aktuelleBestellung->kommentar),
			($aktuelleBestellung->bestellDatum == "" ? NULL : $aktuelleBestellung->bestellDatum));
		
		if(!$abbruch){
		/**************************************************************
		*          Bestelldaten
		**************************************************************/
			// Datenbankstring schreiben
			$namensString = "";
			$ausgabeString = "";
			$beginnFlag=false;
			for($k=0;$k<count($bestellNamensArray);$k++){
				if($bestellAusGabeArray[$k]!=NULL)
				{
					if($beginnFlag){
						$namensString.=" , ";
						$ausgabeString.=", ";
					}
					$beginnFlag=true;
					$namensString.=$bestellNamensArray[$k];
					$ausgabeString.="'$bestellAusGabeArray[$k]'";
				}
			}
			// Query
			$sql = "INSERT INTO `bestellung` ( " . $namensString . " ) VALUES ( " . $ausgabeString . " )";
			
			//Ergebnis		
			$result = mysql_query($sql);
			//printf ("Ver�nderte Datens�tze: %d\n", mysql_affected_rows());
			if(!$result){
				$rueckGabe="Nicht erfolgreich beim schreiben der Datensaetze.";
			} 
		} else {
			$rueckGabe="<P>".
				"Es Konnte nicht in die Bestell-Datenbank geschrieben werden, bitte versuchen Sie es sp�ter ".
				"noch einmal und/oder berichten sie bitte den Fehler:<br> <a href=\"mailto:schreib@gunnardroege.de\">Mail an Webmaster</a><br>".
				"Vielen Dank f�r Ihr Verst�ndnis.<br>".
				($user->Abonnent == "ja" ? $user->Abonnent : "nein").
				($user->nachName == "" ? $abbruch=true : $user->nachName).
				($user->kundenNummer == "" ? NULL : $user->kundenNummer).
				($user->gibBestellung(0) == "" ? $abbruch=true : $user->gibBestellung(0)).
				($user->gibBestellung(1) == "" ? NULL : $user->gibBestellung(1)).
				($user->gibBestellung(2) == "" ? NULL : $user->gibBestellung(2))."";
			}
		return $rueckGabe;
	}
	
	function registriereAdmin(){
		$rueckGabe = true;
		$this->verbinde();
		$sql = 'SELECT * '
        . ' FROM `administratoren` '
        . ' WHERE 1 AND `name` '
        . ' LIKE \'' . $_POST['adminName'] . '\' AND `passwort` '
        . ' LIKE \'' . $_POST['adminPasswort'] . '\' LIMIT 0, 30'; 
		$result = mysql_query($sql);
		if(mysql_num_rows($result)<1){
			print "".
			"<br>Nachname: ".$_POST['adminName'].
            "<br>Sie konnten nicht erfolreich authetifiziert werden".
			"Vielen Dank f�r Ihr Verst�ndnis.";
			 $rueckGabe = false;
		 }
		return $rueckGabe;
	}
	
	function gibBestellungAus(){
		$this->verbinde();
		$sql = "SELECT DISTINCT * FROM bestellung,kunden WHERE LOCATE(bestellung.nachNameBesteller, kunden.nachname) != 0"; 
		$result = mysql_query($sql);
		print "Aufgetretene Fehler: ".mysql_error();
	return $result;
	}

	function gibKundenAus(){
		$sql = "SELECT * FROM kunden"; 
		$result = mysql_query($sql);
		print "Aufgetretene Fehler: ".mysql_error();
	return $result;
	}

	
	function verabeiteBenutzerdatei($url){
		$datei = fopen($url, "r") or die("Konnte die Datei nicht lesen.");
			while($zeile = fgets($datei)){
				$datenArray = preg_split("/;/", $zeile);
				echo join($datenArray, " ");
				if(	$datenArray[0]!=NULL){
					$nutzi = new Benutzer( array($datenArray[0], $datenArray[1]) );
					$inDb = $this->gibUserInDB($nutzi);
					if(!$inDb) echo "Konnte nicht in Datenbank schreiben. Bitte Fileformatierung &uml;berpr&uml;fen.";
				}	
			}
	}
}
?>