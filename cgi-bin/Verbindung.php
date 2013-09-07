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
		/*$this->tableResult = mysqli_query("SELECT * FROM Termine");
		
		 MySQL Query Daten an ein indiziertes Array �bergeben
		$tableData = mysqli_fetch_row($this->tableResult);
		
		
		foreach ($tableData as $value) {
			print ($value."\n");
		}
		*/
	}
	
	function verbinde()
	{
		if(null!=$this->db)// && mysqli_ping($this->db))
			return $this->db;
global $db_serv;
	// MySQL Datenbank Name
global $db_name;
	// User
global $db_user;
	// Passwort
global $db_pass;
		$this->db = mysqli_connect($db_serv, $db_user, $db_pass) or die('Fehler beim Verbinden zum Datenbankserver!');
		// MySQL Datenbank w�hlen
		mysqli_select_db($this->db, $db_name) or die('Fehler beim Verbinden zur Datenbank!');
		mysqli_query($this->db, "SET NAMES utf8");
		return $this->db;
	}
	
	function schliesse()
	{
//		mysqli_close($this->db);
		$this->db = null;
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
		global $build_errors;
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
        print("<!-- sql:".$sql." //-->\n");
		$result = mysqli_query($this->db, $sql);
		$errors = mysqli_error($this->db);
		if(strlen($errors)!=0)
			array_push($build_errors,  "Errors: ".$errors);
		if($result && mysqli_num_rows($result)>0)
		{
			while($reihe = mysqli_fetch_assoc($result))
			{
				array_push($backGabe, $reihe);
			}
		}
		return $backGabe;
	}

	public function GetTableDef($settings)
	{
		global $build_errors;
		$build_errors = array();
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
		$result = mysqli_query($this->db, $sql);
		$errors = mysqli_error($this->db);
		if(strlen($errors)!=0)
			array_push($build_errors,  "Errors: ".$errors);
		if(FALSE==$result)
		{
			return NULL;
		}
		$fields = mysqli_num_fields($result);
		$rowArray = array();	
		
		$mysql_data_type_hash = array(
		    1=>'tinyint',
		    2=>'smallint',
		    3=>'int',
		    4=>'float',
		    5=>'double',
		    7=>'timestamp',
		    8=>'bigint',
		    9=>'mediumint',
		    10=>'date',
		    11=>'time',
		    12=>'datetime',
		    13=>'year',
		    16=>'bit',
		    //252 is currently mapped to all text and blob types (MySQL 5.0.51a)
		    252=>'text',
		    253=>'varchar',
		    254=>'char',
		    246=>'decimal'
		);

		for ($i=0; $i < $fields; $i++)
		{
			$finfo = mysqli_fetch_field_direct($result, $i);
			$rowArray[$finfo->name] = $mysql_data_type_hash[$finfo->type];
		}
		array_push($backGabe,$rowArray);
		return $backGabe;
	} 

	public function DropTableContent($settings)
	{
		global $build_errors;
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
		$result = mysqli_query($this->db, $sql);
		$errors = mysqli_error($this->db);
		if(strlen($errors)!=0)
			array_push($build_errors,  "Errors: ".$errors);
		return array($result);
	}

	public function SetTableContent($settings)
	{
		global $build_errors;
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
			$escapedValue = SafeDBString($values[$fieldIndex]);
        	$sql .= "`".$fields[$fieldIndex]."` = '".$escapedValue."'";
        }
        
		if($reqString != "")
		{
			$sql .= ' WHERE '.$reqString;
		}
		$sql .= ';';
        //print("sql:".$sql);
        // take out as otherwise writes into paragraph list in page table!
		$result = mysqli_query($this->db, $sql);
		$errors = mysqli_error($this->db);
		if(strlen($errors)!=0)
			array_push($build_errors,  "Errors: ".$errors);
		return $result;
	}
	
	public function InsertTableContent($settings)
	{
		global $build_errors;
		$build_errors = array();
		$backGabe = array();
		$this->verbinde();
		
		// UPDATE  `rocknroll`.`submenus` SET  `links` =  'The first entry,The second entry,The third entry' WHERE  `submenus`.`id` =1;
		$sql = "INSERT INTO `".$settings['table']."` (";
        $fields = NULL;
        $values = NULL;
		if(array_key_exists('fields', $settings))
	        $fields = $settings['fields'];
		if(array_key_exists('values', $settings))
	        $values = $settings['values'];
		if(is_array($fields) && count($fields)>0)
		{
	        for($fieldIndex = 0; $fieldIndex<count($fields);$fieldIndex++)
	        {
	        	if('null'==$values[$fieldIndex])
	        		continue;
	        	if($fieldIndex>0)
	        		$sql .= ",";
	        	$sql .= "`".$fields[$fieldIndex]."`";
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
				$escapedValue = SafeDBString($values[$fieldIndex]);
	        	$sql .= "'".$escapedValue."'";
	        }
	        
		}
		$sql .= ');';
//        print("<!-- sql:".$sql." //-->\n");
		$result = mysqli_query($this->db, $sql);
		$errors = mysqli_error($this->db);
		if(strlen($errors)!=0)
			array_push($build_errors,  "Errors: ".$errors);
		return array($result);
	}

	public function GetContent($targetArray)
	{
		$backGabe = true;
		$this->verbinde();
		$sql = 'SELECT `title` '
        . ' FROM `pages` '; 
		$result = mysqli_query($this->db, $sql);
		if(mysqli_num_rows($result)<1)
		{
			 $backGabe = false;
		}
		else
		{
			while($reihe = mysqli_fetch_row($result))
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
		$sql = "SELECT DISTINCT category, description, url, anlegeDatum FROM links WHERE category LIKE \"$rubrik\" AND approved != 0"; 
		$result = mysqli_query($this->db, $sql);
		if(!$result)
			print "Aufgetretene Fehler: ".mysqli_error($this->db);
		return $result;
	}

	function getLinkSections(){
		$this->verbinde();
		$sql = "SELECT DISTINCT category FROM links"; 
		$result = mysqli_query($this->db, $sql);
		if(!$result)
			print "Aufgetretene Fehler: ".mysqli_error($this->db);
		return $result;
	}
	
	function gibLinksAusFuerSuche($eingabe){
		$this->verbinde();
		print "Sucheingabe \"$eingabe\"";
		$sql = "SELECT DISTINCT `category`,`description`,`url`,`anlegeDatum` FROM links WHERE LOCATE(\"".$eingabe."\", description) != 0 OR LOCATE(\"".$eingabe."\", url) != 0"; 
		$result = mysqli_query($sql);
		if(mysqli_num_rows($result)<1){
			$sql = "SELECT DISTINCT `category`,`description`,`url`,`anlegeDatum` FROM links WHERE LOCATE(\"".$eingabe."\", LCASE(description)) != 0 OR LOCATE(\"".$eingabe."\", LCASE(url)) != 0"; 
			$result = mysqli_query($this->db, $sql);
		}
		if(!$result)
			print "Ausgabe: ".mysqli_error($this->db);
		return $result;
	}

	function gibLinksEin($rubrik, $description, $link, $anlegeDatum){
		$this->verbinde();
		$sql = "INSERT INTO `links`(category,description,url,angelegtVon) VALUES ('$rubrik','$description','$link','$_POST[adminName]')"; 
		$result = mysqli_query($this->db, $sql);
		if(!$result)
			print "Aufgetretene Fehler: ".mysqli_error($this->db);
	return $result;
	}

	function gibErsteSortierungAus($tabellenName){
		$this->verbinde();
		$sql = "SELECT DISTINCT erstAuswahl FROM " . $tabellenName; 
		$result = mysqli_query($this->db, $sql);
		if(!$result)
			print "Aufgetretene Fehler: ".mysqli_error($this->db);
	return $result;
	}

	function gibTabelleAus($tabellenName, $whereKlausel){
		if($tabellenName == "Termine" || $tabellenName == "tourneeDaten")
			$spalten = "datum,kuenstler,stadt,location,uhrzeit,url,telNummer";
		$this->verbinde();
		$sql = "SELECT $spalten FROM $tabellenName ".$whereKlausel; 
		$result = mysqli_query($this->db, $sql);
		if(!$result)
			print "Aufgetretene Fehler: ".mysqli_error($this->db);
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
		$result = mysqli_query($this->db, $sql);
		if(mysqli_num_rows($result)<1){
			 $backGabe = false;
		 } 
		 return $backGabe;
	}
	
	function loescheTabelle($dieTabelle){
		$rueckGabe = true;
		print $dieTabelle;
		$this->verbinde();
		$sql = 'TRUNCATE TABLE `kunden`';
		$result = mysqli_query($this->db, $sql);
		print "Aufgetretene Fehler: ".mysqli_error($this->db);
		$sql = 'TRUNCATE TABLE `bestellung`';
		$result = mysqli_query($sql);
		print "\n und".mysqli_error($this->db);
		if(!$result){
			 $rueckGabe = false;
		 } 
		 return $rueckGabe;
	}
	
	function gibUserInDB($user)
	{
		// namen der Tabellenspalten
		$kundenNamensArray = array("kundenNr", "anrede", "vorname" , "nachname" , "adresse" , "ort" , "plz" , "land" , 
							"telPrivat" , "email" , "bankName" , "blz" , "ktnr");
		// Werte f�r die DB-Tabelle
		$kundenAusGabeArray = array(	
		// Eingabewerte mit auszugebenden Werten vergleichen
			($user->kundenNummer == "" ? NULL : $user->kundenNummer),
			($user->anrede == "" ? NULL : $user->anrede),
			($user->vorName == "" ? NULL : $user->vorName),
			($user->nachName),
			($user->adresse == "" ? NULL : $user->adresse),
			($user->ort == "" ? NULL : $user->ort),
			($user->postleitzahl == "" ? NULL : $user->postleitzahl),
			($user->land == "" ? NULL : $user->land),
			($user->telHome == "" ? NULL : $user->telHome),
			($user->eMail == "" ? NULL : $user->eMail),
			($user->bankInstitut == "" ? NULL : $user->bankInstitut),
			($user->blz == "" ? NULL : $user->blz),
			($user->ktnr == "" ? NULL : $user->ktnr));
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
		$result = mysqli_query($this->db, $sql);
		//echo mysqli_affected_rows();
		if(!$result)
		{
			throw new Exception(
			"Es Konnte nicht in die Kunden-Datenbank geschrieben werden, bitte versuchen Sie es sp&auml;ter ".
			"noch einmal und/oder berichten sie bitte den Fehler:<br> <a href=\"mailto:schreib@gunnardroege.de\">Mail an Webmaster</a><br>".
			"Vielen Dank f&uuml;r Ihr Verst&auml;ndnis.<br>".mysqli_error($this->db));
		}
		// get max id
		return mysqli_insert_id($this->db);
	}


	/***********************************************************************************
	**   Bestellung
	***********************************************************************************/
	
	function gibBestellungInDB($user, $aktuelleBestellung, $kundenID)
	{
		$rueckGabe = "";
		$abbruch=false;
		
		$bestellDBString = $aktuelleBestellung->dbText;
		if(strlen($bestellDBString)<=0)
		{
			throw new Exception("<P>".
				"Es Konnte nicht in die Bestell-Datenbank geschrieben werden, bitte versuchen Sie es sp&auml;ter ".
				"noch einmal und/oder berichten sie bitte den Fehler:<br> <a href=\"mailto:schreib@gunnardroege.de\">Mail an Webmaster</a><br>".
				"Vielen Dank f�r Ihr Verst&auml;ndnis.<br>");
		}
		$bestellDBString.="\nBezahlverfahren: ".$aktuelleBestellung->bezahlVerfahren;
		
		$bestellNamensArray = array("kundenID", "bestellungen", "kommentar", "bestellDatum");
		$bestellAusGabeArray = array(	
		// Eingabewerte mit auszugebenden Werten vergleichen
			($kundenID),
			$bestellDBString,
			($aktuelleBestellung->kommentar == "" ? NULL : $aktuelleBestellung->kommentar),
			($aktuelleBestellung->bestellDatum == "" ? NULL : $aktuelleBestellung->bestellDatum));
		
		if(!$abbruch)
		{
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
			$result = mysqli_query($this->db, $sql);
			//printf ("Ver�nderte Datens�tze: %d\n", mysqli_affected_rows());
			if(!$result){
				throw new Exception("Nicht erfolgreich beim schreiben der Datens&auml;tze:".mysqli_error($this->db));
			} 
		} else {
			throw new Exception("<P>".
				"Es Konnte nicht in die Bestell-Datenbank geschrieben werden, bitte versuchen Sie es sp�ter ".
				"noch einmal und/oder berichten sie bitte den Fehler:<br> <a href=\"mailto:schreib@gunnardroege.de\">Mail an Webmaster</a><br>".
				"Vielen Dank f�r Ihr Verst�ndnis.<br>".
				($user->Abonnent == "ja" ? $user->Abonnent : "nein").
				($user->nachName == "" ? $abbruch=true : $user->nachName).
				($user->kundenNummer == "" ? NULL : $user->kundenNummer).
				($user->gibBestellung(0) == "" ? $abbruch=true : $user->gibBestellung(0)).
				($user->gibBestellung(1) == "" ? NULL : $user->gibBestellung(1)).
				($user->gibBestellung(2) == "" ? NULL : $user->gibBestellung(2))."");
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
		$result = mysqli_query($this->db, $sql);
		if(mysqli_num_rows($result)<1){
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
		$sql = "SELECT DISTINCT * FROM bestellung,kunden WHERE LOCATE(bestellung.kundenID, kunden.id) != 0"; 
		$result = mysqli_query($this->db, $sql);
		print "Aufgetretene Fehler: ".mysqli_error($this->db);
	return $result;
	}

	function gibKundenAus(){
		$sql = "SELECT * FROM kunden"; 
		$result = mysqli_query($this->db, $sql);
		print "Aufgetretene Fehler: ".mysqli_error($this->db);
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