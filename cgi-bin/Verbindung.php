<?php
/**
* Diese Klasse managt die Verbindung zur Datenbank
***/

class Verbindung
{
	private $ablauf;
	//MySQL Server oder Host
	private $db_serv = 'localhost';
	// MySQL Datenbank Name
	private $db_name = 'rocknroll';
	// User
	// Domaingo: db85283
	private $db_user = 'HR';
	// Passwort
	private $db_pass = 'hr';
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
		// Verbindung zum MySQL Server herstellen
		$this->db = mysql_pconnect($this->db_serv, $this->db_user, $this->db_pass) or die('ERROR!');
		mysql_select_db($this->db_name, $this->db) or die('Error connecting to database!');
		
		
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
		$this->db = mysql_pconnect($this->db_serv, $this->db_user, $this->db_pass) or die('Fehler beim Verbinden zum Datenbankserver!');
		// MySQL Datenbank w�hlen
		mysql_select_db($this->db_name, $this->db) or die('Fehler beim Verbinden zur Datenbank!');
		return true;
	}
	
	/***********************************************************************************
	**   Content
	***********************************************************************************/

	public function GetTableContent($table, $fields, $requirements = NULL, $useRegExp = FALSE)
	{
		$backGabe = array();
		$this->verbinde();
		
		$fieldString = $fields;
		if(is_array($fields))
		{
			$joinString = join("`,`", $fields);
			$fieldString = "`".$joinString."`";
		}

		$reqString = "";
		if(is_array($requirements))
		{
			$index = 0;
			foreach($requirements as $key => $value)
			{
				if($index>0)
					$reqString .= " AND ";
				$value = preg_replace("/%20/", " ", $value);
				if($useRegExp)
					$reqString .= $key." REGEXP '".$value."'";
				else
					$reqString .= $key." LIKE '".$value."'";
			}
		}
		
		$sql = 'SELECT '.$fieldString;
        $sql .= ' FROM `'.$table.'` '; 
		if($reqString != "")
		{
			$sql .= ' WHERE '.$reqString;
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

	public function DropTableContent($table, $requirements = NULL)
	{
		$backGabe = array();
		$this->verbinde();
		
		$reqString = "";
		if(is_array($requirements))
		{
			$index = 0;
			foreach($requirements as $key => $value)
			{
				if($index>0)
					$reqString .= " AND ";
				$value = preg_replace("/%20/", " ", $value);
				$reqString .= $key." LIKE '".$value."'";
			}
		}
		
		// UPDATE  `rocknroll`.`submenus` SET  `links` =  'The first entry,The second entry,The third entry' WHERE  `submenus`.`id` =1;
		$sql = 'DELETE FROM `rocknroll`.`'.$table.'`';
        
		if($reqString != "")
		{
			$sql .= ' WHERE '.$reqString;
		}
		$sql .= ';';
        print("<!-- sql:".$sql." //-->\n");
		$result = mysql_query($sql);
		print "<!-- Errors: ".mysql_error()."//-->";
		return array($result);
	}

	public function SetTableContent($table, $fields, $requirements = NULL, $values = NULL)
	{
        PrintHtmlComment("SetTableContent begin");
		$backGabe = array();
		$this->verbinde();
		
		$reqString = "";
		if(is_array($requirements))
		{
			$index = 0;
			foreach($requirements as $key => $value)
			{
				if($index>0)
					$reqString .= " AND ";
				$value = preg_replace("/%20/", " ", $value);
				$reqString .= $key." LIKE '".$value."'";
			}
		}
		
		// UPDATE  `rocknroll`.`submenus` SET  `links` =  'The first entry,The second entry,The third entry' WHERE  `submenus`.`id` =1;
		$sql = 'UPDATE `rocknroll`.`'.$table;
        $sql .= '` SET '; 
        for($fieldIndex = 0; $fieldIndex<count($fields);$fieldIndex++)
        {
        	if($fieldIndex>0)
        		$sql .= ",";
        	$sql .= "`".$fields[$fieldIndex]."` = '".$values[$fieldIndex]."'";
        }
        
		if($reqString != "")
		{
			$sql .= ' WHERE '.$reqString;
		}
		$sql .= ';';
        print("<!-- sql:".$sql." //-->\n");
		$result = mysql_query($sql);
		print "<!-- Errors: ".mysql_error()."//-->";
		return $result;
	}
	
	public function InsertTableContent($table, $fields=NULL, $requirements = NULL)
	{
		$backGabe = array();
		$this->verbinde();
		
		$reqString = "";
		if(is_array($requirements))
		{
			$index = 0;
			foreach($requirements as $key => $value)
			{
				if($index>0)
					$reqString .= " AND ";
				$value = preg_replace("/%20/", " ", $value);
				$reqString .= $key." LIKE '".$value."'";
			}
		}
		
		// UPDATE  `rocknroll`.`submenus` SET  `links` =  'The first entry,The second entry,The third entry' WHERE  `submenus`.`id` =1;
		$sql = 'INSERT INTO `rocknroll`.`'.$table.'` (';
		if(is_array($fields) && count($fields)>0)
		{
	        for($fieldIndex = 0; $fieldIndex<count($fields);$fieldIndex++)
	        {
	        	if($fieldIndex>0)
	        		$sql .= ",";
	        	$keys = array_keys($fields);
	        	$sql .= "`".$keys[$fieldIndex]."`";
	        }
		}
		$sql .= ') VALUES ('; 
		if(is_array($fields) && count($fields)>0)
		{
	        for($fieldIndex= 0; $fieldIndex<count($fields);$fieldIndex++)
	        {
	        	if($fieldIndex>0)
	        		$sql .= ",";
	        	$values = array_values($fields);
	        	$sql .= "'".$values [$fieldIndex]."'";
	        }
	        
		}
		$sql .= ');';
        print("<!-- sql:".$sql." //-->\n");
		$result = mysql_query($sql);
		print "<!-- Errors: ".mysql_error()."//-->";
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
		$sql = "SELECT DISTINCT rubrik, beschreibung, link, anlegeDatum FROM links WHERE rubrik LIKE \"$rubrik\""; 
		$result = mysql_query($sql);
		print "Aufgetretene Fehler: ".mysql_error();
	return $result;
	}

	function gibLinksAusFuerSuche($eingabe){
		$this->verbinde();
		print "Sucheingabe \"$eingabe\"";
		$sql = "SELECT DISTINCT `rubrik`,`beschreibung`,`link`,`anlegeDatum` FROM links WHERE LOCATE(\"".$eingabe."\", beschreibung) != 0 OR LOCATE(\"".$eingabe."\", link) != 0"; 
		$result = mysql_query($sql);
		if(mysql_num_rows($result)<1){
			$sql = "SELECT DISTINCT `rubrik`,`beschreibung`,`link`,`anlegeDatum` FROM links WHERE LOCATE(\"".$eingabe."\", LCASE(beschreibung)) != 0 OR LOCATE(\"".$eingabe."\", LCASE(link)) != 0"; 
			$result = mysql_query($sql);
		}
		print "Ausgabe: ".mysql_error();
	return $result;
	}

	function gibLinksEin($rubrik, $beschreibung, $link, $anlegeDatum){
		$this->verbinde();
		$sql = "INSERT INTO `links`(rubrik,beschreibung,link,angelegtVon) VALUES ('$rubrik','$beschreibung','$link','$_POST[adminName]')"; 
		$result = mysql_query($sql);
		print "Aufgetretene Fehler: ".mysql_error();
	return $result;
	}

	function gibErsteSortierungAus($tabellenName){
		$this->verbinde();
		$sql = "SELECT DISTINCT erstAuswahl FROM " . $tabellenName; 
		$result = mysql_query($sql);
		//print "Aufgetretene Fehler: ".mysql_error();
	return $result;
	}

	function gibTabelleAus($tabellenName, $whereKlausel){
		if($tabellenName == "Termine" || $tabellenName == "tourneeDaten")
			$spalten = "datum,kuenstler,stadt,location,uhrzeit,url,telNummer";
		$this->verbinde();
		$sql = "SELECT $spalten FROM $tabellenName ".$whereKlausel; 
		$result = mysql_query($sql);
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
		$rueckGabe = true;
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
		if(!$abbruch){
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
			if(!$result){
				$rueckGabe=false;
				print 
				"<P><font face=\"Arial\" size=\"2\"><font color=\"#EBD5D5\">".
				"Es Konnte nicht in die Kunden-Datenbank geschrieben werden, bitte versuchen Sie es sp�ter ".
				"noch einmal und/oder berichten sie bitte den Fehler:<br></font> <a href=\"mailto:schreib@gunnardroege.de\">Mail an Webmaster</a><br>".
				"<font color=\"#EBD5D5\">Vielen Dank f�r Ihr Verst�ndnis.<br>".
				"gefundene Eintr�ge: $num_results</font></font>";
			}
		} else{
		 $rueckGabe=false;
		}
		return $rueckGabe;
	}


	/***********************************************************************************
	**   Bestellung
	***********************************************************************************/
	
	function gibBestellungInDB($user, $aktuelleBestellung){
		$rueckGabe = true;
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
				$rueckGabe=false;
			} 
		} else {
			$rueckGabe=false;
			print 	
				"<P><font face=\"Arial\" size=\"2\"><font color=\"#EBD5D5\">".
				"Es Konnte nicht in die Bestell-Datenbank geschrieben werden, bitte versuchen Sie es sp�ter ".
				"noch einmal und/oder berichten sie bitte den Fehler:<br></font> <a href=\"mailto:schreib@gunnardroege.de\">Mail an Webmaster</a><br>".
				"<font color=\"#EBD5D5\">Vielen Dank f�r Ihr Verst�ndnis.<br>".
				"gefundene Eintr�ge: $num_results</font>".
				($user->Abonnent == "ja" ? $user->Abonnent : "nein").
				($user->nachName == "" ? $abbruch=true : $user->nachName).
				($user->kundenNummer == "" ? NULL : $user->kundenNummer).
				($user->gibBestellung(0) == "" ? $abbruch=true : $user->gibBestellung(0)).
				($user->gibBestellung(1) == "" ? NULL : $user->gibBestellung(1)).
				($user->gibBestellung(2) == "" ? NULL : $user->gibBestellung(2))."</font>";

			}
		return $rueckGabe;
	}
	
	function melden(){
		print "Hallo!";
		
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
			print "<P><font face=\"Arial\" size=\"2\"><font color=\"#EBD5D5\">".
			"<br>Nachname: ".$_POST['adminName'].
            "<br>Sie konnten nicht erfolreich authetifiziert werden".
			"<font color=\"#EBD5D5\">Vielen Dank f�r Ihr Verst�ndnis.</font></font>";
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

	
	function gibBestellungAlsXml($result){
	// Verbinden
		$this->verbinde();

	/*
		$result = $this->gibBestellungAus();
		if($result){
			// DOMXML Objekt erzeugen
			$doc = xmldoc('<?xml version="1.0"?><!DOCTYPE root [<!ENTITY ouml "oe"><!ENTITY uuml "ue">]><root/>');
			// Wurzel erzeugen (<article> ... </article>
			$root = $doc->add_root( "Bestellungen" );
			$trans = get_html_translation_table(HTML_ENTITIES);
			//Anzahl der Zeilen
			while($reihe = mysql_fetch_row($result)){
			  // <artheader> ... </artheader>
			  $row = $root->new_child( "Row", "" );
				for($g=0;$g<count($reihe);$g++){
					 $cell = $row->new_child( "Cell", "" );
					 $data = $cell->new_child( "Data", htmlentities($reihe[$g]) );
				}
			}
			
			// XML ausgeben
			$file = fopen ("Bestellungen.xml", "w+");

			fputs($file, $doc->dumpmem() );
			fclose($file);
		}
		  // XSLT Prozessor erzeugen
		  $xslt_processor = xslt_create();
		
		  // Transformierung durchf�hren
		  $result = xslt_run($xslt_processor, "Bestellungen.xsl", "Bestellungen.xml");
		
		  // �berpr�fen, ob ein Fehler aufgetreten ist
		  if(!$result) echo xslt_error($xslt_processor);
		
			$file = fopen ("Bestellungen_final.xml", "w+");

			fputs($file, xslt_fetch_result($xslt_processor) );
			fclose($file);

		  // Ergebnis der Transformation ausgeben
		 // print xslt_fetch_result($xslt_processor);
		
		  // Vom XSLT Prozessor belegten Speicher freigeben
		  xslt_free($xslt_processor);   
		  
		  // Ende XML Generierung
		*/  
		//Written by Dan Zarrella. Some additional tweaks provided by JP Honeywell
		//pear excel package has support for fonts and formulas etc.. more complicated
		//this is good for quick table dumps (deliverables)
		
		$count = mysql_num_fields($result);
		
		for ($i = 0; $i < $count; $i++){
			$header .= mysql_field_name($result, $i)."\t";
		}
		
		while($row = mysql_fetch_row($result)){
		  $line = '';
		  foreach($row as $value){
			if(!isset($value) || $value == ""){
			  $value = "\t";
			}else{
		# important to escape any quotes to preserve them in the data.
			  $value = str_replace('"', '""', $value);
		# needed to encapsulate data in quotes because some data might be multi line.
		# the good news is that numbers remain numbers in Excel even though quoted.
			  $value = '"' . $value . '"' . "\t";
			}
			$line .= $value;
		  }
		  $data .= trim($line)."\n";
		}
		# this line is needed because returns embedded in the data have "\r"
		# and this looks like a "box character" in Excel
		  $data = str_replace("\r", "", $data);
		
		
		# Nice to let someone know that the search came up empty.
		# Otherwise only the column name headers will be output to Excel.
		if ($data == "") {
		  $data = "\nno matching records found\n";
		}
		
		# This line will stream the file to the user rather than spray it across the screen
		/*(header("Content-type: application/octet-stream");
		
		# replace excelfile.xls with whatever you want the filename to default to
		header("Content-Disposition: attachment; filename=excelfile.xls");
		header("Pragma: no-cache");
		header("Expires: 0");
		
		//echo $header."\n".$data;
		*/



		// XML ausgeben
		$file = fopen ($_POST['dateiUrl'], "w+");

		fputs($file, $header."\n".$data);
		fclose($file);

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