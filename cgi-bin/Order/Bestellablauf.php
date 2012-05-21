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
	
	function InsertPostVars(&$text)
	{
		$pattern = "/%%Preis\((\d+)\)%%/";
		$text = preg_replace_callback($pattern, "GetPrice", $text);
		$pattern = "/%%ServerVar\(([^\)]+)\)%%/";
		$text = preg_replace_callback($pattern, "GetServerVars", $text);
		$pattern = "/<([^\s]+)\s.+id=\"([^\"]+)\"[^>]+>/";
		$text = preg_replace_callback($pattern, "GetPostVarValue", $text);
		//		printf(" %4.2f &euro; ", $_SESSION['aufenthalt']->aktuellerBestellAblauf->preise[9]);
	}
	
	function importHTML($filename, &$parentNode)
	{
		PrintHtmlComment("Loading ".$filename);
		$importdoc = new DOMDocument();
		$thePath = pathinfo(__FILE__,PATHINFO_DIRNAME);
		$htmlString = file_get_contents($thePath."/".$filename);
		$this->InsertPostVars($htmlString);
		if($importdoc->loadHTML($htmlString))//$thePath."/".$filename))//"<balls>".$this->content."</balls>");
		{
			$doc = $parentNode->ownerDocument;
			$text = $doc->importNode($importdoc->documentElement, true);
			$parentNode->appendChild($text);
		}
	}
	
	function addResetButton(&$parentNode)
	{
		$text = 
		'<FORM METHOD="POST" action="%%ServerVar(REQUEST_URI)%%" onReset="return confirm(\"Do you really want to reset the form?\")">
		<input type="hidden" name="resetBestellung" id="resetBestellung" value="true" />
	 	<input style="width:200px; height:25px; color:#FF0000" TYPE=SUBMIT VALUE="Bestellung neu starten." />
	 	</FORM>';
		$this->addTextNode($text, $parentNode);
	}

	function addTextNode($text, &$parentNode)
	{
		$importdoc = new DOMDocument();
		$importdoc->encoding = "utf-8";
		$this->InsertPostVars($text);
		if($importdoc->loadHTML($text))
		{
			$doc = $parentNode->ownerDocument;
			$textNode = $doc->importNode($importdoc->documentElement, true);
			$parentNode->appendChild($textNode);
		}
	}
	
	function addErrorText($errorText, &$parentNode)
	{
		$importdoc = new DOMDocument();
		$importdoc->encoding = "utf-8";
		$doc = $parentNode->ownerDocument;
		$errorTextHolder = $doc->createElement("ul");
		$errorTextHolder->setAttribute("class", "errorText");
		if($importdoc->loadHTML($errorText))
		{
			$textNode = $doc->importNode($importdoc->documentElement, true);
			$errorTextHolder->appendChild($textNode);
		}
		$parentNode->appendChild($errorTextHolder);
	}
	
	function aktuellerBestellSchritt(&$parentNode)
	{
	/*******************************************************************/
	// nach dritter Best�tigung ist die Flagvariable vorhanden und auf s
	/*******************************************************************/
		if(!$this->bestellungAufgegeben && isset($_POST['formFilled']) && $_POST['formFilled'] == "s")
		{
			$errorText =  $this->gibDatenInDb();
		// Daten erfolgreich in db geschrieben
			if($errorText==""){
				$this->bestellungAufgegeben=true;
				$this->importHTML("endKopf.htm", $parentNode);
				$this->addTextNode($this->aufenthalt->GetUser()->printUserShort(), $parentNode);
				$this->addTextNode($this->aktuelleBestellung->zeigeBestellungen($this), $parentNode);
				$this->importHTML("endFuss.htm", $parentNode);
		// Fehler beim schreiben der Daten
			} else {
				$this->addErrorText($errorText,$parentNode);
				$this->importHTML("confirmForm.htm", $parentNode);
				$this->addTextNode($this->aufenthalt->GetUser()->printUserShort(), $parentNode);
				$this->addTextNode($this->aktuelleBestellung->zeigeBestellungen($this), $parentNode);
				$this->importHTML("confirmFormFuss.htm", $parentNode);
			}
			
	/*******************************************************************/
	// nach zweiter Best�tigung ist die Flagvariable vorhanden und auf y
	/*******************************************************************/
		} else if (!$this->bestellungAufgegeben && isset($_POST['formFilled']) && $_POST['formFilled'] == "y"){
			$ausgabePuffer = $this->pruefeFormElemente(0);
	// Eingaben vollst�ndig
			if($ausgabePuffer == ""){
				$this->aufenthalt->GetUser()->registriereMich();
				$this->importHTML("confirmForm.htm", $parentNode);
				$this->addTextNode($this->aufenthalt->GetUser()->printUserShort(), $parentNode);
				$this->addTextNode($this->aktuelleBestellung->zeigeBestellungen($this), $parentNode);
				$this->importHTML("confirmFormFuss.htm", $parentNode);
			} else {
	// Eingaben nicht vollst�ndig
				$this->importHTML("registrierFormKopf.htm", $parentNode);
				$ausgabePuffer = "Angaben nicht vollst�ndig:".$ausgabePuffer; 
				$this->addErrorText($ausgabePuffer,$parentNode);
				$this->aktuelleBestellung->zeigeBestellungen($this);
				$this->importHTML("registrierForm.htm", $parentNode);
			}
	/*******************************************************************/
	// nach erster Best�tigung ist die Flagvariable vorhanden und auf j
	/*******************************************************************/
		} else if(!$this->bestellungAufgegeben && isset($_POST['formFilled']) && $_POST['formFilled'] == "j"){
			$ausgabePuffer = $this->pruefeFormElemente(1);
	// Eingaben vollst�ndig
			if($ausgabePuffer == "")
			{
				$this->importHTML("registrierFormKopf.htm", $parentNode);
				$this->gibBestellungAuf();
				$this->addTextNode($this->aktuelleBestellung->zeigeBestellungen($this), $parentNode);
				$this->importHTML("registrierForm.htm", $parentNode);
	// Eingaben nicht vollst�ndig
			} else {
				$this->importHTML("bestellFormKopf.htm", $parentNode);
				$this->addErrorText($ausgabePuffer,$parentNode);
				$this->importHTML("bestellForm.php", $parentNode);
			} 
	/*******************************************************************/
	// erster Aufruf
	/*******************************************************************/
		} else {
			$this->bestellungAufgegeben = false;
			$this->importHTML("bestellFormKopf.htm", $parentNode);
			if(isset($_POST['resetBestellung']))
			{
				$this->aktuelleBestellung = NULL;
			}
			else if(NULL!=$this->aktuelleBestellung)
			{
				$this->addResetButton($parentNode);
			}
			$this->importHTML("bestellForm.php", $parentNode);
		}
	}
	
	
	
	function schreibeBestellTabelle(){
		 print $this->aktuelleBestellung->zeigeBestellungen($this);
	}
	
	function gibBestellungAuf()
	{
		$orderflags = array(8);
		foreach(Bestellung::$ids as $id)
			array_push($orderflags, isset($_POST[$id]));
		$this->aktuelleBestellung = new Bestellung(
		$orderflags,
		array($_POST['AboAbAusgabe'],$_POST['EinzelheftAusgabeNr']),
		$_POST['destination'],
		date("Y-m-d"),
		$_POST['Sonstiges'],
		$this->preise );
	}

	function pruefeFormElemente($formNummer){
		$rueckGabe="";
		$nummernPruefString = "/\d+/";
		$emailPruefString = "/\S*@\S*\.\S*/";
		$ausgabenPruefString = "/[\d\s,]/";
		switch($formNummer){
			case 0:
					if($_POST['anrede'] == "" || 0!=preg_match("/Bitte/", $_POST['anrede'])) 
						$rueckGabe.="<li>Bitte f&uuml;llen Sie das Anredefeld aus.</li>";
					if($_POST['Nachname'] == "") $rueckGabe.="<li>Bitte f&uuml;llen Sie das Nachnamefeld aus.</li>";
					if($_POST['Postadresse'] == "") $rueckGabe.="<li>Bitte f&uuml;llen Sie das Postadressenfeld aus.</li>";
					if($_POST['Ort'] == "") $rueckGabe.="<li>Bitte f&uuml;llen Sie das Ortsfeld aus.</li>";
					$emailRichtig = preg_match($emailPruefString, $_POST['EMail']);
					if(!$emailRichtig) $rueckGabe.="<li>Bitte geben Sie eine g&uuml;ltige E-Mail Adresse ein.</li>";
					if($_POST['Land'] == "germany")$this->aktuelleBestellung->destination="inland";
					else if($_POST['Land'] == "sonstigesEU")$this->aktuelleBestellung->destination="euausland";
					else if($_POST['Land'] == "sonstiges")$this->aktuelleBestellung->destination="noneuausland";

					if(	$_POST['bezahlung']=="lastschrift"){
						if($_POST['Bankinstitut'] == "" ) $rueckGabe.="<li>Bitte geben Sie das Bankinstitut ein.</li>";
						$ktnrRichtig = preg_match($nummernPruefString, $_POST['Kontonummer']);
						if(!$ktnrRichtig) $rueckGabe.="<li>Bitte geben Sie nur Nummern in das Kontonummerfeld ein.</li>";
						$blzRichtig = preg_match($nummernPruefString, $_POST['Bankleitzahl']);
						if(!$blzRichtig) $rueckGabe.="<li>Bitte geben Sie nur Nummern in das Bankleitzahlfeld ein.</li>";
					}
				break;
			case 1:
				$allkeys = array(	'Bestelltyp_Probeheft', 
									'Bestelltyp_Einzelheft', 
									'Bestelltyp_Abo', 
									'Bestelltyp_Probepaket', 
									'Bestelltyp_klProbepaket',
									'Bestelltyp_grProbepaket',
									'Bestelltyp_Lexikon'
				);
				$anythingSet = false;
				foreach($allkeys as $key)
				{
					if(isset($_POST[$key]))
					{
						$anythingSet = true;
						break;
					}
				}
				if(!$anythingSet)
					$rueckGabe.="<li>Bitte f&uuml;llen Sie mindestens ein Bestellfeld aus.</li>";
				
				if(	($_POST['EinzelheftAusgabeNr']!="" && !preg_match($ausgabenPruefString, $_POST['EinzelheftAusgabeNr'])) ||
					($_POST['AboAbAusgabe']!="" && !preg_match($ausgabenPruefString, $_POST['AboAbAusgabe'])) )
					$rueckGabe.="<li>Bitte geben Sie in das Feld f�r die Eingabe der gew�nschten Heftausgaben nur Zahlen ein und trennen diese NUR mit Kommas oder Leerzeichen.</li>";
				break;
			}
		return $rueckGabe;
	}
	
	function gibDatenInDb()
	{
		$rueckgabe="";
		try{
			$kundenId = Aufenthalt::GetInstance()->DBConn()->gibUserInDB($this->aufenthalt->GetUser());
			Aufenthalt::GetInstance()->DBConn()->gibBestellungInDB($this->aufenthalt->GetUser(), $this->aktuelleBestellung, $kundenId);
		}
		catch(Exception $e)
		{
			$rueckgabe= $e->getMessage();
		} 
		return $rueckgabe;
	}
	
} // end class BestellAblauf
?>