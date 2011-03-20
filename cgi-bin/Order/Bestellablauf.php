<?php
/**
* Klasse zum Managen der Bestellungen.
**/
class BestellAblauf{
	
	var $state;
	var $stateName = array("Aufgeben der Bestellung", "Benutzeregistrierung", "Best&auml;tigen der Bestellung", "Best&auml;tigung");
	var $preise = array();
	var $aufenthalt;	
	var $aktuelleBestellung;
	var $bestellungAufgegeben;

	function BestellAblauf($aufenthalt_p){
		$this->aufenthalt = $aufenthalt_p;
		$this->state=0;
		$this->bestellungAufgegeben = false;
		$back = $this->holepreise();
/*		if($back!=1)print("hat geklappt ");
		else print("hat nicht geklappt");
*/	}
	
	function holepreise(){
		if(($fp = fopen("http://www.rocknroll-magazin.de/cgi-bin/preis-definition.txt", 'r'))!=null){
//		print "<font color=\"#FFFFFF\">AAAARH!</font>";
			$index=0;
			while (!feof($fp)) {
				$buffer = fgets($fp);
  				if($buffer!="")$this->preise[$index++]=substr($buffer, 0, strpos($buffer, " "));
			}
			return 0;
		}else return 1;
	}
	
	function aktuellerBestellSchritt(){
	/*******************************************************************/
	// nach dritter Bestätigung ist die Flagvariable vorhanden und auf s
	/*******************************************************************/
		if(!$this->bestellungAufgegeben && isset($_POST['formFilled']) && $_POST['formFilled'] == "s"){
			$schreibenErfolgreich =  $this->gibDatenInDb();
		// Daten erfolgreich in db geschrieben
			if($schreibenErfolgreich){
				$this->bestellungAufgegeben=true;
				require_once("onlineShop/endKopf.htm");
				$this->aufenthalt->aktuellerNutzer->printUserShort();
				$this->aktuelleBestellung->zeigeBestellungen($this);
				require_once("onlineShop/endFuss.htm");
		// Fehler beim schreiben der Daten
			} else {
				require_once("onlineShop/registrierFormKopf.htm");
				require_once("onlineShop/registrierForm.php");
			}
			
	/*******************************************************************/
	// nach zweiter Bestätigung ist die Flagvariable vorhanden und auf y
	/*******************************************************************/
		} else if (!$this->bestellungAufgegeben && isset($_POST['formFilled']) && $_POST['formFilled'] == "y"){
			$ausgabePuffer = $this->pruefeFormElemente(0);
	// Eingaben vollständig
			if($ausgabePuffer == ""){
				$this->aufenthalt->aktuellerNutzer->registriereMich();
				require_once("onlineShop/confirmForm.htm");
				$this->aufenthalt->aktuellerNutzer->printUserShort();
				$this->aktuelleBestellung->zeigeBestellungen($this);
				require_once("onlineShop/confirmFormFuss.htm");
			} else {
	// Eingaben nicht vollständig
				require_once("onlineShop/registrierFormKopf.htm");
				print "<P><b><font face=\"Arial\" color=\"#FF0000\" size=\"2\">Angaben nicht vollständig:<br>".
				$ausgabePuffer."</font></b></P>";
				$this->aktuelleBestellung->zeigeBestellungen($this);
				require_once("onlineShop/registrierForm.php");
			}
	/*******************************************************************/
	// nach erster Bestätigung ist die Flagvariable vorhanden und auf j
	/*******************************************************************/
		} else if(!$this->bestellungAufgegeben && isset($_POST['formFilled']) && $_POST['formFilled'] == "j"){
			$ausgabePuffer = $this->pruefeFormElemente(1);
	// Eingaben vollständig
			if($ausgabePuffer == ""){
				require_once("onlineShop/registrierFormKopf.htm");
				$this->gibBestellungAuf();
				require_once("onlineShop/registrierForm.php");
				//$this->aufenthalt->aktuellerNutzer->printRegForm();
	// Eingaben nicht vollständig
			} else {
				require_once("onlineShop/bestellFormKopf.htm");
				print "<font color=\"#FF0000\">".$ausgabePuffer."</font>";
				require_once("onlineShop/bestellForm.php");
			} 
	/*******************************************************************/
	// erster Aufruf
	/*******************************************************************/
		} else {
			$this->bestellungAufgegeben = false;
			require_once("onlineShop/bestellFormKopf.htm");
			require_once("onlineShop/bestellForm.php");
		}
	}
	
	
	
	function schreibeBestellTabelle(){
		 print $this->aktuelleBestellung->zeigeBestellungen($this);
	}
	
	function gibBestellungAuf(){
		$this->aktuelleBestellung = new Bestellung(
		array(
			($_POST['Bestelltyp_Abo']=="ON"?true:false),
			($_POST['Bestelltyp_Einzelheft']=="ON"?true:false),
			($_POST['Bestelltyp_Probeheft']=="ON"?true:false),
			($_POST['Bestelltyp_klProbepaket']=="ON"?true:false),
			($_POST['Bestelltyp_grProbepaket']=="ON"?true:false),
			($_POST['Bestelltyp_ProbepaketMitAbo']=="ON"?true:false),
			($_POST['Bestelltyp_ProbepaketMitLexikon']=="ON"?true:false),
			($_POST['Bestelltyp_Lexikon']=="ON"?true:false) ),
		array($_POST['AboAbAusgabe']==""?"":$_POST['AboAbAusgabe'], 
			($_POST['EinzelheftAusgabeNr']==""?"":$_POST['EinzelheftAusgabeNr'])
			),
		$_POST['destination'],
		date("Y-m-d"),
		$_POST['Sonstiges'],
		$this->preise );

		print $this->aktuelleBestellung->zeigeBestellungen($this);
	}

	function pruefeFormElemente($formNummer){
		$rueckGabe="";
		$nummernPruefString = "/\d+/";
		$emailPruefString = "/\S*@\S*\.\S*/";
		$ausgabenPruefString = "/[\d\s,]/";
		switch($formNummer){
			case 0:
					if($_POST['anrede'] == "") $rueckGabe.="Bitte füllen Sie das Anredefeld aus.<br>";
					if($_POST['Nachname'] == "") $rueckGabe.="Bitte füllen Sie das Nachnamefeld aus.<br>";
					if($_POST['Postadresse'] == "") $rueckGabe.="Bitte füllen Sie das Postadressenfeld aus.<br>";
					if($_POST['Ort'] == "") $rueckGabe.="Bitte füllen Sie das Ortsfeld aus.<br>";
					$emailRichtig = preg_match($emailPruefString, $_POST['EMail']);
					if(!$emailRichtig) $rueckGabe.="Bitte geben Sie eine g&uuml;ltige E-Mail Adresse ein.<br>";
					if($_POST['Land'] == "germany")$this->aktuelleBestellung->destination="inland";
					else if($_POST['Land'] == "sonstigesEU")$this->aktuelleBestellung->destination="euausland";
					else if($_POST['Land'] == "sonstiges")$this->aktuelleBestellung->destination="noneuausland";

					if(	$_POST['bezahlung']=="lastschrift"){
						if($_POST['Bankinstitut'] == "" ) $rueckGabe.="Bitte geben Sie das Bankinstitut ein.<br>";
						$ktnrRichtig = preg_match($nummernPruefString, $_POST['Kontonummer']);
						if(!$ktnrRichtig) $rueckGabe.="Bitte geben Sie nur Nummern in das Kontonummerfeld ein.<br>";
						$blzRichtig = preg_match($nummernPruefString, $_POST['Bankleitzahl']);
						if(!$blzRichtig) $rueckGabe.="Bitte geben Sie nur Nummern in das Bankleitzahlfeld ein.<br>";
					}
				break;
			case 1:
				if($_POST['Bestelltyp_Probeheft'].
				$_POST['Bestelltyp_Einzelheft'].
				$_POST['Bestelltyp_Abo'].
				$_POST['Bestelltyp_Probepaket'].
				$_POST['Bestelltyp_klProbepaket'].
				$_POST['Bestelltyp_grProbepaket'].
				$_POST['Bestelltyp_Lexikon'] == "") $rueckGabe.="Bitte füllen Sie mindestens ein Bestellfeld aus.<br>";
				
				if(	($_POST['EinzelheftAusgabeNr']!="" && !preg_match($ausgabenPruefString, $_POST['EinzelheftAusgabeNr'])) ||
					($_POST['AboAbAusgabe']!="" && !preg_match($ausgabenPruefString, $_POST['AboAbAusgabe'])) )
					$rueckGabe.="Bitte geben Sie in das Feld für die Eingabe der gewünschten Heftausgaben nur Zahlen ein und trennen diese NUR mit Kommas oder Leerzeichen.<br>";
				
				break;
			}
		return $rueckGabe;
	}
	
	function gibDatenInDb(){
		$rueckgabe=true;
		/***********************************************************
		**        Verbindung zur Datenbank               **
		***********************************************************/
		$rueckgabe = $this->aufenthalt->meineVerbindung->verbinde();
		if(!$rueckgabe){
			print "Keine erfolgreiche Verbindung zur DB.<br>";
			} else {
			/***********************************************************
			**        User in Datenbank schreiben               **
			***********************************************************/
			$userInDb = $this->aufenthalt->meineVerbindung->gibUserInDB($this->aufenthalt->aktuellerNutzer);
				if(!$userInDb){
					print "Keine erfolgreiche Verbindung zur DB bei der Speicherung des Benutzers.<br>";
					$rueckgabe=false;
					} else {
			/***********************************************************
			**        Bestellung in Datenbank schreiben               **
			***********************************************************/
				$bestellungErfolgreich = $this->aufenthalt->meineVerbindung->gibBestellungInDB($this->aufenthalt->aktuellerNutzer, $this->aktuelleBestellung);
				if(!$bestellungErfolgreich){
					$rueckgabe=false;
					print "Keine erfolgreiche Verbindung zur DB bei der Speicherung der Bestellung.<br>";
					}
			}
		}
		return $rueckgabe;
	}
	
} // end class BestellAblauf
?>