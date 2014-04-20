<?php
/**
* Klasse zum Managen der Bestellungen.
**/
class Bestellung{
	static $Produkte = array(
		"Abo"=>"Abonnement", 
		"Heft"=>"Einzelheft", 
		"AktHeft"=>"Aktuelles Heft",
		"ProHeft"=>"Probeheft", 
		"KlPaket"=>"Kleines Probepaket", 
		"GrPaket"=>"Gro&szlig;es Probepaket",  
		"Index"=>"Index", 
	);
		
	var $bestellt = array();
	var $ausgabeNr = array();
	var $destination;
	var $gesamtPreis;
	var $kommentar;
	var $bestellDatum;
	var $dbText;
	var $anzahlAusgaben;
	var $preise;
	var $portos;
	var $bezahlVerfahren;
	
	function Bestellung($bestellungen, $ausGabeNr, $insAusland, $datum, $kommentar)
	{
		$this->bestellt = $bestellungen;
		$this->ausgabeNr = $ausGabeNr;
		$this->destination = $insAusland;
		$this->bestellDatum = $datum;
		$this->kommentar = EncodeUmlaute($kommentar);
		$this->gesamtPreis = 0;
		$this->dbText = $this->gibDBText();
		$this->bezahlVerfahren = "Ueberweisung";
	}
	
	function GetPorto($key)
	{
		if(!array_key_exists($key, $this->portos))
			return $this->portos[count($this->portos)-1];
		return $this->portos[$key];
	}
	
	function SetBezahlVerfahren($verfahren)
	{
		$this->bezahlVerfahren = $verfahren;
	}
	
	function gibDBText()
	{
		$rueckGabe = "";
		$this->gesamtPreis = 0;
		$this->preise = BestellAblauf::GetInst()->inland_preise;
		$this->portos = BestellAblauf::GetInst()->inland_portos;
		if($this->destination != 'germany')
		{
			$this->preise=BestellAblauf::GetInst()->ausland_preise;
			$this->portos=BestellAblauf::GetInst()->ausland_portos;
		}
		
		$mitBerechnung = true;

		foreach($this->bestellt as $key=>$bestellung)
		{
			if(array_key_exists($key, $this->preise))
			{
				$produktPreis = $this->preise[$key];
				
				$this->gesamtPreis += $produktPreis;
				$rueckGabe .= Bestellung::$Produkte[$key];
				if($key=="Abo")
					$rueckGabe .= " ab Ausgabe ".$this->ausgabeNr["Abo"];
				if($key=="Heft")
				{
					$keywords = preg_split("/[\s,]+/", $this->ausgabeNr["Heft"]);
					$this->anzahlAusgaben[$key] = (count($keywords)>0?count($keywords):1);
					if($this->anzahlAusgaben[$key]>1)
						$mitBerechnung = false;
					$rueckGabe .= " ab Ausgabe ".$this->ausgabeNr["Heft"];
				}
				$rueckGabe .= "<br>";
			}
		}
		if($mitBerechnung)
			$rueckGabe .= ". berechneter Preis: ".sprintf("%3.2f", $this->gesamtPreis)."&euro;";
		else
			$rueckGabe .= "Keine Preisberechnung hat stattgefunden.";
		return $rueckGabe;	
	}
	
	function zeigeBestellungen()
	{
		$ablauf = BestellAblauf::GetInst();
		
		// bestellTebelle	
		$mitBerechnung = (($this->destination!='noneuausland') ? true : false);
		$rueckGabe = "
			<strong>Sie haben folgende Artikel ausgew&auml;hlt.</strong> <br>";
	
		$rueckGabe .= "
			<table border=\"0\" cellspacing=\"2\" cellpadding=\"2\" width=\"630px\">
			<thead>
				<tr bgcolor=\"#336699\"> 
				  <td width=\"200px\"> <strong>Produkt:</strong> 
				  </td>
				  <td><strong>(Ab) Ausgabe(n) Nr.</strong>
				  </td>
			  <td width=\"80px\"><strong>Preis pro St&uuml;ck (in &euro;)</strong></td>";
			  
		/*******************************
		Zeilen mit berechnung		
		********************************/
		
		$rueckGabe .= "
			</tr>
			</thead>
			<tbody>
			";
			foreach($this->bestellt as $key=>$bestellung)
			{
				if(array_key_exists($key, Bestellung::$Produkte))
				{
					$abAusgabe = "";
					if(array_key_exists($key, $this->ausgabeNr))
						$abAusgabe = $this->ausgabeNr[$key];
					$rueckGabe .= "
					<tr bgcolor=\"#336699\"> 
					  <td>".Bestellung::$Produkte[$key]."</td>
					  <td>$abAusgabe</td>";

					if($key=="Heft")
					{
						$keywords = preg_split("/[\s,]+/", $this->ausgabeNr["Heft"]);
						$this->anzahlAusgaben[$key] = (count($keywords)>0?count($keywords):1);
						if($this->anzahlAusgaben[$key]>1)
							$mitBerechnung = false;
					}
					$produktPreis = $this->preise[$key];
					$rueckGabe .= "
					<td align=\"right\">".sprintf("%3.2f", $produktPreis)."</td>
					</tr>";
				}
			}
		if($mitBerechnung)
		{
			$rueckGabe .= "
			<tr> 
				<td colspan=\"5\">&nbsp;</td>
			<tr>
			<tr bgcolor=\"#666699\"> 
				<td>&nbsp;</td>
				<td>Komplett Preis</td>
				<td align=\"right\"><strong	>".sprintf("%3.2f", $this->gesamtPreis)." &euro;</strong></td>
			<tr>";
		}
		else
		{
			$rueckGabe .= "
			<tr> 
				<td colspan=\"5\">&nbsp;</td>
			<tr>
			<tr bgcolor=\"#666699\"> 
				<td colspan=\"5\">Eine automatische Berechnung des Gesamtpreises ist nicht m&ouml;glich. Portokosten sinken mit mehreren Heftbestellungen oder sind nicht kalkulierbar f&uuml;r Bestellungen au&szlig;erhalb der EU.</td>
			<tr>";
		}
		//$this->kommentar
		$rueckGabe .= "
			<tr bgcolor=\"#336699\"> 
				<td colspan=\"3\">Ihr Kommentar:<p />".$this->kommentar."</td>
			<tr>
			</tbody>
		</table>
		
		Technisch bedingt k&ouml;nnen Fehler in obiger Berechnung auftreten. 
		Wir behalten uns vor ggf. eine Neuberechnung des Preises zuzustellen. In diesem Fall ist Ihre Bestellung selbstverst&auml;ndlich zu Ihrem Schutz NICHT bindend. 
		";
		/*******************************
		Bank zeuch		
		********************************/
		if($this->destination=='germany'){
			if(Aufenthalt::GetInst()->GetUser()->bezahlung=="lastschrift"){
				$rueckGabe .= "Sie haben gew&auml;hlt per Lastschrift zu zahlen.
								Vielen Dank. Wir werden den oben stehenden Betrag von ihrem Konto einziehen und ihre Bestellung schnellstm&ouml;glich abschicken.";
			} else {
				$rueckGabe .= "Sie haben gew&auml;hlt per &Uuml;berweisung zu bezahlen. Bitte veranlassen sie eine Transaktion auf folgendes Konto:
							<blockquote>
								Rock&amp;Roll Musikmagazin:<br>
								Volksbank Oldenburg<br>
								BIC: GENODEF 1EDE <br />
								IBAN: DE02 2806 1822 3432 5026 00	
							</blockquote>
							Ihre Bestellung ist unterwegs sobald der Zahlungseingang auf unserem Konto best&auml;tigt wurde.
							";
			}
		}else{
			if(Aufenthalt::GetInst()->GetUser()->bezahlung=="lastschrift")
				$rueckGabe .= "<p name=\"noDirectDebit\" />
				<strong>You chose to pay by direct debit. 
				We are sorry to say that we don't offer this posibility for customers from abroad. 
				Sorry for any inconvenience.</strong>";
			$rueckGabe .= "
			
			Orders from <strong>ABROAD</strong> of germany result in <strong>HIGHER SHIPPING COSTS</strong>. We will bill these as well. Eventually occuring costs of bank-transactions must be payed by the client. <br>
			The Payment is ONLY posible per TRANSACTION.
			<blockquote>
				Rock&amp;Roll Musikmagazin:<br>
				Volksbank Oldenburg<br>
				BIC: GENODEF 1EDE <br />
				IBAN: DE02 2806 1822 3432 5026 00	
			</blockquote>
			Ihre Bestellung ist unterwegs sobald der Zahlungseingang auf unserem Konto best&auml;tigt wurde.
			<br>";
		}
		/*******************************
		Zurueck button		
		********************************/
	
		// rechtlicher Text
		//*********************************
			if(	array_key_exists("Abo", $this->bestellt) ||
				array_key_exists("AboKlPaket", $this->bestellt)	|| 
				array_key_exists("AboGrPaket", $this->bestellt)) 
					$rueckGabe .="
			<table border=\"1\" cellspacing=\"20\" class=\"semanRahmen\" noshade>
			<tr><td>
			<strong>Konditionen f&uuml;r Abonements:</strong><br>Das &quot;Rock'n'Roll-Musikmagazin&quot; erscheint alle 2 Monate. Ein Abonnement dauert 6 Ausgaben (ein Jahr) und kostet 35 &euro; in Deutschland und 40 &euro; im Europ&auml;ischen Ausland inkl. Porto. 
			  <p align=\"left\">Falls der Vertrag nicht mindestens 4 Wochen vor Erhalt der 5. Ausgabe gek&uuml;ndigt wird, verl&auml;ngert er sich um weitere 6 Ausgaben. 
			 Lieferungen ins <strong>Ausland</strong> resultieren in <strong>H&Ouml;HEREN PORTOKOSTEN</strong>. Wir werden diese ggf. extra berechnen. Evtl. auftretende Bankgeb&uuml;hren m&uuml;ssen vom Kunden getragen werden. 
			  </td></tr></table>";
	
		return $rueckGabe;
	}
}
?>