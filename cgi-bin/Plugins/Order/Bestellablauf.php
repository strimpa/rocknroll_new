<?php
	global $serverRoot;

	require_once($serverRoot."/cgi-bin/Utils.php");
	require_once("Bestellung.php");
	require_once("DBHelper_order.php");
/**
* Klasse zum Managen der Bestellungen.
**/
	function BackButtonPressed()
	{
		return array_key_exists("back", $_POST);
	}

class BestellAblauf{
	
	const STEP_BESTELLEN = 0, STEP_BESTELLUNG = 1, STEP_BENUTZERREG = 2, STEP_CONFIRM = 3;
	private $state;
	private $stateName = array("Aufgeben der Bestellung", "Benutzeregistrierung", "Best&auml;tigen der Bestellung", "Best&auml;tigung");
	private $bestellungAufgegeben;
	private static $aktuellerBestellAblauf;

	public $aktuelleBestellung;
	public $inland_preise = array();
	public $ausland_preise = array();
	public $inland_portos = array();
	public $ausland_portos = array();

	function BestellAblauf()
	{
		$this->state=0;
		$this->bestellungAufgegeben = false;
		$back = $this->holepreise();
		if(!$back)
			print "CAn't open price defintion file!";
	}

	static function GetInst()
	{
		if(!isset($_SESSION['BestellAblauf']))
		{
			PrintHtmlComment("New BestellAblauf");
			$_SESSION['BestellAblauf'] = new BestellAblauf();
		}
		$returnObj = $_SESSION['BestellAblauf'];
		return $returnObj;
	}
	
	
	function holepreise(){
		// print "************************".$_SERVER['DOCUMENT_ROOT']."/cgi-bin/preis-definition.txt";
		if(($fp = fopen(realpath(dirname(__FILE__))."/preis-definition.txt", 'r'))!=null)
		{
			$this->inland_preise = array();
			$this->ausland_preise = array();
			$this->inland_portos = array();
			$this->ausland_portos = array();
			$index=0;
			$keys = array_keys(Bestellung::$Produkte);
			while (!feof($fp)) {
				$buffer = fgets($fp);
  				if(strlen($buffer)>2)
				{
					$splitString = explode("\t", $buffer);
					$currKey = $keys[$index];
					$splitString = array_filter($splitString);
					//						print "Preise key:$currKey:$buffer";
					$this->inland_preise[$currKey] = $splitString[0];
					$this->ausland_preise[$currKey] = $splitString[1];
					$index++;
				}
			}
			fclose($fp);
			return TRUE;
		}
		else 
			return FALSE;
	}
	
	private function InsertBackButton($step)
	{
		print "
			<FORM METHOD='POST' action='/index/bestellen'>
			<input type='hidden' name='back' value=true />
			<input type='hidden' name='formFilled' value=$step />
				M&ouml;chten Sie etwas korrigieren? 
				<input class=\"button\" type=\"submit\" value=\"Zur&uuml;ck\" />
			</FORM>
		";
	}
	
	function aktuellerBestellSchritt()
	{
		$ausgabePuffer = "";
		$errorsOccured = false;
		
		if($this->bestellungAufgegeben)
			$_POST['formFilled'] = BestellAblauf::STEP_BESTELLEN;
		
		if(!BackButtonPressed() && array_key_exists("formFilled", $_POST) && !$this->bestellungAufgegeben)
		{
			switch($_POST['formFilled'])
			{
				case BestellAblauf::STEP_BESTELLUNG:
				case BestellAblauf::STEP_BENUTZERREG:
					$errorsOccured = !$this->pruefeFormElemente($_POST['formFilled'], $ausgabePuffer);
					break;
				case BestellAblauf::STEP_CONFIRM:
					$errorsOccured = !$this->gibDatenInDb($ausgabePuffer);
					break;
			}

			if($errorsOccured)
				$_POST['formFilled'] -= 1;
		}
		
	/*******************************************************************/
	// nach dritter Best�tigung ist die Flagvariable vorhanden und auf s
	/*******************************************************************/
		if(isset($_POST['formFilled']) && $_POST['formFilled'] == BestellAblauf::STEP_CONFIRM)
		{
			$this->bestellungAufgegeben=true;
			require_once("html/endKopf.htm");
			$summary = Aufenthalt::GetInst()->GetUser()->printUserShort();
			$summary .= $this->aktuelleBestellung->zeigeBestellungen($this);
			print $summary;
			SendDebugMail("Eine Bestellung wurde aufgegeben: <br />".$summary);
			require_once("html/emailForm.php");
	/*******************************************************************/
	// nach zweiter Best�tigung ist die Flagvariable vorhanden und auf y
	/*******************************************************************/
		} else if (isset($_POST['formFilled']) && $_POST['formFilled'] == BestellAblauf::STEP_BENUTZERREG){
			if($errorsOccured)
			{
				require_once("html/confirmFormError.php");
				print "<font color=\"#FF0000\">".$ausgabePuffer."</font>";
				require_once("html/emailForm.php");
			}
			else
				require_once("html/confirmForm.php");
			if(!BackButtonPressed() && !$errorsOccured)
			{
				Aufenthalt::GetInst()->GetUser()->registriereMich();
			}
			print Aufenthalt::GetInst()->GetUser()->printUserShort();
			print $this->aktuelleBestellung->zeigeBestellungen($this);
			$this->InsertBackButton(BestellAblauf::STEP_BESTELLUNG);
			require_once("html/confirmFormFuss.htm");
	/*******************************************************************/
	// nach erster Best�tigung ist die Flagvariable vorhanden und auf j
	/*******************************************************************/
		} 
		else if(isset($_POST['formFilled']) && $_POST['formFilled'] == BestellAblauf::STEP_BESTELLUNG)
		{
			require_once("html/registrierFormKopf.htm");
			print "<font color=\"#FF0000\">".$ausgabePuffer."</font>";
			if(!BackButtonPressed() && !$errorsOccured)
			{
				$this->gibBestellungAuf();
			}
			print $this->aktuelleBestellung->zeigeBestellungen($this);
			$this->InsertBackButton(BestellAblauf::STEP_BESTELLEN);
			require_once("html/registrierForm.php");
	/*******************************************************************/
	// erster Aufruf
	/*******************************************************************/
		} else {
			$this->bestellungAufgegeben = false;
			$back = $this->holepreise();

			require_once("html/bestellFormKopf.htm");
			print "<font color=\"#FF0000\">".$ausgabePuffer."</font>";
			require_once("html/bestellForm.php");
		}
	}
	
	
	
	function schreibeBestellTabelle(){
		 print $this->aktuelleBestellung->zeigeBestellungen($this);
	}
	
	function gibBestellungAuf(){
		$this->aktuelleBestellung = new Bestellung(
		$_POST,
		array(
			"Abo"=>SafeDBString($_POST['AboAbAusgabe']==""?"":$_POST['AboAbAusgabe']), 
			"Heft"=>SafeDBString($_POST['EinzelheftAusgabeNr']==""?"":$_POST['EinzelheftAusgabeNr'])
			),
		$_POST['destination'],
		date("Y-m-d"),
		$_POST['Sonstiges']);
	}
	
	function holeBenutzerDaten($key, $equals=NULL)
	{
		$retVal = "";
		if(NULL!=Aufenthalt::GetInst()->GetUser())
		{
			switch ($key) {
				case 'kundenNr':
					$retVal = Aufenthalt::GetInst()->GetUser()->kundenNummer;
					break;
				case 'anrede':
					if(Aufenthalt::GetInst()->GetUser()->anrede==$equals)
						$retVal = "selected";
					break;
				case 'Nachname':
					$retVal = Aufenthalt::GetInst()->GetUser()->nachName;
					break;
				case 'Vorname':
					$retVal = Aufenthalt::GetInst()->GetUser()->vorName;
					break;
				case 'Postadresse':
					$retVal = Aufenthalt::GetInst()->GetUser()->adresse;
					break;
				case 'Postleitzahl':
					$retVal = Aufenthalt::GetInst()->GetUser()->postleitzahl;
					break;
				case 'Ort':
					$retVal = Aufenthalt::GetInst()->GetUser()->ort;
					break;
				case 'Land':
					$retVal = Aufenthalt::GetInst()->GetUser()->land;
					break;
				case 'Telefon':
					$retVal = Aufenthalt::GetInst()->GetUser()->telHome;
					break;
				case 'EMail':
					$retVal = Aufenthalt::GetInst()->GetUser()->eMail;
					break;
				case 'Bankinstitut':
					$retVal = Aufenthalt::GetInst()->GetUser()->bankInstitut;
					break;
				case 'Kontonummer':
					$retVal = Aufenthalt::GetInst()->GetUser()->ktnr;
					break;
				case 'Bankleitzahl':
					$retVal = Aufenthalt::GetInst()->GetUser()->blz;
					break;
			}
		}
		
		if($retVal=="" && isset($_POST[$key]))
			return $_POST[$key];
		
		return $retVal;
	}

	function pruefeFormElemente($formNummer, &$rueckGabe)
	{
		$nummernPruefString = "/[\d\s]+/";
		$emailPruefString = "/\S*@\S*\.\S*/";
		$ausgabenPruefString = "/[^\d\s,]/";
		$aboPruefString = "/[^\d]/";
		switch($formNummer){
			case BestellAblauf::STEP_BENUTZERREG:
					if(!isset($_POST['anrede']) || $_POST['anrede'] == "" || $_POST['anrede'] == "Bitte wählen sie") $rueckGabe.="Bitte f&uuml;llen Sie das Anredefeld aus.<br>";
					if(isset($_POST['Nachname']) && $_POST['Nachname'] == "") $rueckGabe.="Bitte f&uuml;llen Sie das Nachnamefeld aus.<br>";
					if(isset($_POST['Postadresse']) && $_POST['Postadresse'] == "") $rueckGabe.="Bitte f&uuml;llen Sie das Postadressenfeld aus.<br>";
					if(isset($_POST['Ort']) && $_POST['Ort'] == "") $rueckGabe.="Bitte f&uuml;llen Sie das Ortsfeld aus.<br>";
					if(isset($_POST['EMail']))
					{
						$emailRichtig = preg_match($emailPruefString, $_POST['EMail']);
						if(!$emailRichtig) 
							$rueckGabe.="Bitte geben Sie eine g&uuml;ltige E-Mail Adresse ein.<br>";
					}
					if(isset($_POST['Land']))
						$this->aktuelleBestellung->destination = $_POST['Land'];

					if(	isset($_POST['bezahlung']) && $_POST['bezahlung']=="lastschrift")
					{
						if($_POST['Bankinstitut'] == "" ) 
							$rueckGabe.="Bitte geben Sie das Bankinstitut ein.<br>";
						$ktnrRichtig = preg_match($nummernPruefString, $_POST['Kontonummer']);
						if(!$ktnrRichtig) 	
							$rueckGabe.="Bitte geben Sie nur Nummern in das Kontonummerfeld ein.<br>";
						$blzRichtig = preg_match($nummernPruefString, $_POST['Bankleitzahl']);
						if(!$blzRichtig) 
							$rueckGabe.="Bitte geben Sie nur Nummern in das Bankleitzahlfeld ein.<br>";
					}
				break;
			case BestellAblauf::STEP_BESTELLUNG:
				if(	!isset($_POST['ProHeft']) &&
					!isset($_POST['AktHeft']) &&
					!isset($_POST['Heft']) &&
					!isset($_POST['Abo']) &&
					!isset($_POST['KlPaket']) &&
					!isset($_POST['GrPaket']) &&
					!isset($_POST['Index']))
				{ 
					$rueckGabe.="Bitte f&uuml;llen Sie mindestens ein Bestellfeld aus.<br>";
				}
				
				if(	$_POST['EinzelheftAusgabeNr']!="" && preg_match($ausgabenPruefString, $_POST['EinzelheftAusgabeNr']))
					$rueckGabe.="Bitte geben Sie in das Feld f&uuml;r die Eingabe der gew&uuml;nschten Heftausgaben nur Zahlen ein und trennen diese NUR mit Kommata oder Leerzeichen.<br>";
				if(	$_POST['AboAbAusgabe']!="" && preg_match($aboPruefString, $_POST['AboAbAusgabe']) )
					$rueckGabe.="Es kann nur eine Angabe zu der gew&uuml;nschten Start Ausgabe des Abonnements gemacht werden.<br>";
									
				break;
			}
		return $rueckGabe=="";
	}
	
	function gibDatenInDb(&$ausgabePuffer)
	{
		try{
			$kundenId = DBCntrl::GetInst()->Conn()->gibUserInDB(Aufenthalt::GetInst()->GetUser());
			gibBestellungInDB(Aufenthalt::GetInst()->GetUser(), $this->aktuelleBestellung, $kundenId);
		}
		catch(Exception $e)
		{
			$ausgabePuffer= $e->getMessage();
			return false;
		} 
		return true;
	}
	
} // end class BestellAblauf
?>