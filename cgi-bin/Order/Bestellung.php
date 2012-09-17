<?php
/**
* Klasse zum Managen der Bestellungen.
**/
class Bestellung
{
	static $Produkte = array(
			"Abonnement", 
			"Einzelheft", 
			"Aktuelles Heft",
			"Probeheft", 
			"kleines Probepaket", 
			"gro&szlig;es Probepaket",  
			"Abonnement im Probepaket",  
			"Lexikon im Probepaket", 
			"Lexikon \"When Music Was Still Music\"");
	static $ids = array(
			'Bestelltyp_Abo',
			'Bestelltyp_Einzelheft',
			'Bestelltyp_Probeheft',
			'Bestelltyp_klProbepaket',
			'Bestelltyp_grProbepaket',
			'Bestelltyp_ProbepaketMitAbo',
			'Bestelltyp_ProbepaketMitLexikon',
			'Bestelltyp_Lexikon');
	static $auswahlIds = array(
			'AboAbAusgabe',
			'EinzelheftAusgabeNr');
	
	var $Preise_Inland = array();
	var $Preise_Ausland = array();
	var $Preise=array();
	var $bestellt = array();
	var $ausgabeNr = array();
	var $destination;
	var $portoErgebnis;
	var $gesamtPreis;
	var $kommentar;
	var $bestellDatum;
	var $dbText;

	function Bestellung($bestellungen, $ausGabeNr, $insAusland, $datum, $kommentar, $preise)
	{
		$this->bestellt = $bestellungen;
		$this->ausgabeNr = $ausGabeNr;
		$this->destination = $insAusland;
		$this->bestellDatum = $datum;
		$this->kommentar = $kommentar;
		$this->gesamtPreis = 0;
		
		$numProdukte = count($this->Produkte);
		$this->Preise_Inland = array_slice($preise, 0, $numProdukte);
		$this->Preise_Ausland = array_slice($preise, $numProdukte, $numProdukte);
	}
	
	function gibDBText(){
		$rueckGabe = "";
		switch($this->destination){
			case 'inland':
				$this->Preise=$this->Preise_Inland;
				break;
			default:
				$this->Preise=$this->Preise_Ausland;
				break;
		}
		$this->gesamtPreis = 0;
		//for($ind=0;ind<count($this->Preise);$ind++) print("preise ".$this->Preise[$ind]);
		for($index=0;$index<count($this->bestellt);$index++)
		{
			if($this->bestellt[$index])
			{
				if($index!=0)$rueckGabe .= "<br>";
				$this->anzahlAusgaben[$index] = 1;
				if($index==1)
				{
					$keywords = preg_split("/[\s,]+/", $this->ausgabeNr[$index]);
					$this->anzahlAusgaben[$index] = (count($keywords)>0?count($keywords):1);
				}
				$this->gesamtPreis+=$this->Preise[$index]*$this->anzahlAusgaben[$index];
//				$this->portoErgebnis=($this->portoErgebnis<$this->Porto[$index]?$this->Porto[$index]:$this->portoErgebnis);
				$rueckGabe .= Bestellung::$Produkte[$index];
				if($index==0)
				{
					if($this->ausgabeNr[$index]!="")
						$rueckGabe .= " ab Ausgabe ".$this->ausgabeNr[$index];
				}
			}
		}
		$rueckGabe .= ". berechneter Preis: ".sprintf("%3.2f", ($this->gesamtPreis))."&euro;";
		return $rueckGabe;
	}
	
	function zeigeBestellungen($ablauf)
	{
		$this->dbText = $this->gibDBText();
		// bestellTebelle	
		$mitBerechnung = (($this->destination!='noneuausland') ? true : false);
		$rueckGabe = "
			<strong>Sie haben folgende Artikel ausgew&auml;hlt.</strong> <br>";
	
		$rueckGabe .= "
			<table border=\"0\" cellspacing=\"2\" cellpadding=\"2\" width=\"600px\">
			<thead>
				<tr bgcolor=\"#336699\"> 
				  <td width=40%> <strong>Produkt:</strong></td>
				  <td width=40%><strong>Ab Ausgabe Nr.</strong></td>
				  <td width=40%><strong>Anzahl</strong></td>";	
		if($mitBerechnung)
			$rueckGabe .= "
			  <td width=40%><strong>Preis pro St&uuml;ck</strong></td>
			  <td width=20%><strong>Preis</strong></td>";
			  
		/*******************************
		Zeilen mit berechnung		
		********************************/
		$rueckGabe .= "
			</tr>
			</thead>
			<tbody>
			";
			for($index=0;$index<count($this->bestellt);$index++)
				if($this->bestellt[$index]){
					$ausgabeString = "";
					if($index<2)
						$ausgabeString = $this->ausgabeNr[$index];
					$rueckGabe .= "
					<tr bgcolor=\"#336699\"> 
					  <td width=40%>".Bestellung::$Produkte[$index]."</td>
					  <td width=40%>".$ausgabeString."</td>
					  <td width=40%>".$this->anzahlAusgaben[$index]."</td>";
					if($mitBerechnung)
					  $rueckGabe .= "
					  <td width=40%>".sprintf("%3.2f", $this->Preise[$index])."</td>
					  <td width=20% align=\"right\">".
						sprintf("%3.2f", $this->Preise[$index]*$this->anzahlAusgaben[$index])."</td>";
				$rueckGabe .= "
					</tr>";
				}
		if($mitBerechnung)
			$rueckGabe .= "
			<tr> 
				<td colspan=\"5\">&nbsp;</td>
			<tr>
			<tr bgcolor=\"#666699\"> 
				<td colspan=\"2\">&nbsp;</td>
				<td colspan=\"2\">Komplett Preis</td>
				<td align=\"right\">".sprintf("%3.2f", $this->gesamtPreis)."</td>
			<tr>";
/*			<tr bgcolor=\"#336699\"> 
				<td colspan=\"2\">&nbsp;</td>
				<td colspan=\"2\">Complete Porto</td>
				<td align=\"right\"><b>".sprintf("%3.2f", $this->portoErgebnis)."</td>
			<tr>
			<tr bgcolor=\"#666699\"> 
				<td colspan=\"2\">&nbsp;</td>
				<td colspan=\"2\">Complete Bill</td>
				<td border=\"1\" align=\"right\"><b>".sprintf("%3.2f", ($this->portoErgebnis+$this->gesamtPreis))."</td>
			<tr>";
*/		$rueckGabe .= "
			</tbody>
		</table>
		
		<p>Technisch bedingt können Fehler in obiger Berechnung auftreten. 
		Wir behalten uns vor ggf. eine Neuberechnung des Preises zuzustellen. In diesem Fall ist Ihre Bestellung selbstverständlich zu Ihrem Schutz NICHT bindend. 
		</P>";
		/*******************************
		Bank zeuch		
		********************************/
		if($ablauf->bestellungAufgegeben){
			if($this->destination=='inland'){
				if($ablauf->aufenthalt->GetUser()->bezahlung=="lastschrift"){
					$rueckGabe .= "Sie haben gew&auml;hlt per Lastschrift zu zahlen.
									Vielen Dank. Wir werden den oben stehenden Betrag von ihrem Konto einziehen und ihre Bestellung schnellstmöglich abschicken.";
				} else {
					$rueckGabe .= "Sie haben gew&auml;hlt per &Uuml;berweisung zu bezahlen. Bitte veranlassen sie eine Transaktion auf folgendes Konto:
								<blockquote>
									Rock&Roll Musikmagazin:<br>
									Volksbank Oldenburg<br>
									Kto-Nr.: 34 32 502 600
									BLZ: 280 618 22	
								</blockquote>
								For transactions from abroad please don´t forget to enter:
								<blockquote>
									BIC: GENODEF 1EDE <br />
									IBAN: DE02 2806 1822 3432 5026 00	
								</blockquote>
								Ihre Bestellung ist unterwegs sobald der Zahlungseingang auf unserem Konto best&auml;tigt wurde.
								";
				}
			}else{
				if($ablauf->aufenthalt->aktuellerNutzer->bezahlung=="lastschrift")
					$rueckGabe .= "
					<strong>You chose to pay by direct debit. 
					We are sorry to say that we don't offer this posibility for customers from abroad. 
					Sorry for any inconvenience.</strong>";
				$rueckGabe .= "
				
				<p>Orders from <strong>ABROAD</strong> of germany result in <strong>HIGHER SHIPPING COSTS</strong>. We will bill these as well. Eventually occuring costs of bank-transactions must be payed by the client. <br>
				The Payment is ONLY posible per TRANSACTION.
				<blockquote>
					Rock&Roll Musikmagazin:<br>
					Volksbank Oldenburg<br>
					Kto-Nr.: 34 32 502 600
					BLZ: 280 618 22	
				</blockquote>
				For transactions from abroad please don´t forget to enter:
				<blockquote>
					BIC: GENODEF 1EDE <br />
					IBAN: DE02 2806 1822 3432 5026 00	
				</blockquote>
				Ihre Bestellung ist unterwegs sobald der Zahlungseingang auf unserem Konto best&auml;tigt wurde.
				<br>";
			}
		/*******************************
		Zurück button		
		********************************/
		}else $rueckGabe .= "
				<p>M&ouml;chten Sie etwas korrigieren? <input class=\"button\" type=\"button\" value=\"Zur&uuml;ck\" onClick=\"javascript:history.back();\" /></p><br>";
	
		// rechtlicher Text
		//*********************************
			if($this->bestellt[0] || $this->bestellt[5]) $rueckGabe .="
			<table border=\"0\" class=\"semanRahmen\" noshade>
			<tr><td>
			<strong>Konditionen f&uuml;r Abonements:</strong><br>Das &quot;Rock'n'Roll-Musikmagazin&quot; erscheint alle 2 Monate. Ein Abonnement dauert 6 Ausgaben (ein Jahr) und kostet 35 &euro; in Deutschland und 40 &euro; im Europäischen Ausland inkl. Porto. 
			  <p align=\"left\">Falls der Vertrag nicht mindestens 4 Wochen vor Erhalt der 5. Ausgabe gekündigt wird, verlängert er sich um weitere 6 Ausgaben. 
			 <p>Lieferungen ins <strong>Ausland</strong> resultieren in <strong>H&Ouml;HEREN PORTOKOSTEN</strong>. Wir werden diese ggf. extra berechnen. Evtl. auftretende Bankgebühren m&uuml;ssen vom Kunden getragen werden. 
			  </td></tr></table>";
	
		return $rueckGabe;
	}
}
?>