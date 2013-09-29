<?php
/**
* Diese Klasse managt die DBConn zur Datenbank
 * 
 * PERSISTENCE: instanced by Controller
 * ***/

class DBConnMysqli implements DBConn
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
	**   Initialisation
	***********************************************************************************/

	function DBConn()
	{
		$this->db = null;
		$this->Connect();
	}
	
	function Connect()
	{
		if(null!=$this->db && mysqli_ping($this->db))
		{
			return $this->db;
		}
global $db_serv;
	// MySQL Datenbank Name
global $db_name;
	// User
global $db_user;
	// Passwort
global $db_pass;
		$this->db = new mysqli($db_serv, $db_user, $db_pass, $db_name) or die('Fehler beim Connectn zum Datenbankserver!');
		// MySQL Datenbank w�hlen
//		mysqli_select_db($this->db, $db_name) or die('Fehler beim Connectn zur Datenbank!');
		mysqli_query($this->db, "SET NAMES utf8");
		return $this->db;
	}
	
	function schliesse()
	{
//		mysqli_close($this->db);
		$this->db = null;
	}

	/***********************************************************************************
	**   Local helpers
	***********************************************************************************/
	
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
		if(isset($params[$reqParam]))
		{
			$reqObj = $params[$reqParam];
			if(is_array($reqObj))
				$reqObj = new DBReq($reqObj);
			$reqString .= $reqObj->CreateReqString();
		}
//        print("<!-- requirements:".$reqString." //-->\n");
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
	**   DBConn interface contract
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
		
		$this->Connect();

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
//		PrintHtmlComment($sql);
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
		$this->Connect();
		
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
		$this->Connect();
		
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
		$this->Connect();
		
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
		$this->Connect();
		
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

	/***********************************************************************************
	**   Bollocks to refactor into Controller
	***********************************************************************************/

	/***********************************************************************************
	**   Benutzer verwaltung
	***********************************************************************************/

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


	
}
?>