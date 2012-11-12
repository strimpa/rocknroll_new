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
		"AboKlPaket"=>"Abonnement im Probepaket",  
		"GrPaket"=>"Gro&szlig;es Probepaket",  
		"AboGrPaket"=>"Abonnement im Probepaket",  
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
	
	function Bestellung($bestellungen, $ausGabeNr, $insAusland, $datum, $kommentar)
	{
		$this->bestellt = $bestellungen;
		$this->ausgabeNr = $ausGabeNr;
		$this->destination = $insAusland;
		$this->bestellDatum = $datum;
		$this->kommentar = EncodeUmlaute($kommentar);
		$this->anzahlAusgaben = array(
			"Abo"=>6, 
			"Heft"=>1, 
			"AktHeft"=>1,
			"ProHeft"=>1, 
			"KlPaket"=>10, 
			"AboKlPaket"=>6,  
			"GrPaket"=>50,  
			"AboGrPaket"=>6,  
			"Index"=>1
		);
		$this->gesamtPreis = 0;
	}
	
	function GetPorto($key)
	{
		if(!array_key_exists($key, $this->portos))
			return $this->portos[count($this->portos)-1];
		return $this->portos[$key];
	}
	
	function gibDBText()
	{
		$rueckGabe = "";
		$this->gesamtPreis = 0;
		$this->preise = Aufenthalt::GetInstance()->GetAblauf()->inland_preise;
		$this->portos = Aufenthalt::GetInstance()->GetAblauf()->inland_portos;
		if($this->destination != 'inland')
		{
			$this->preise=Aufenthalt::GetInstance()->GetAblauf()->ausland_preise;
			$this->portos=Aufenthalt::GetInstance()->GetAblauf()->ausland_portos;
		}

		foreach($this->bestellt as $key=>$bestellung)
		{
			if(array_key_exists($key, $this->preise))
			{
				//$this->anzahlAusgaben[$key] = 1;
				if($key=="Heft")
				{
					$keywords = preg_split("/[\s,]+/", $this->ausgabeNr["Heft"]);
					$this->anzahlAusgaben[$key] = (count($keywords)>0?count($keywords):1);
					$produktPreis = $this->preise[$key] * $this->anzahlAusgaben[$key];
				}
				else
					$produktPreis = $this->preise[$key];
					
				if("Index"==$key)
					$portoErgebnis = $this->GetPorto("Index");
				else
					$portoErgebnis = $this->GetPorto($this->anzahlAusgaben[$key]);
				
				$this->gesamtPreis += $produktPreis + $portoErgebnis;
				$rueckGabe .= Bestellung::$Produkte[$key];
				if($key=="Abo")
					$rueckGabe .= " ab Ausgabe ".$this->ausgabeNr["Abo"];
				$rueckGabe .= "<br>";
			}
		}
		$rueckGabe .= ". berechneter Preis: ".sprintf("%3.2f", $this->gesamtPreis)."&euro;";
		return $rueckGabe;
	}
	
	function zeigeBestellungen()
	{
		$ablauf = Aufenthalt::GetInstance()->GetAblauf();
		$this->dbText = $this->gibDBText();
		
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
				  <td><strong>Ab Ausgabe Nr.</strong>
				  </td>
				  <td width=\"40px\"><strong>Anzahl</strong>
				  </td>";	
		if($mitBerechnung)
			$rueckGabe .= "
			  <td width=\"80px\"><strong>Preis Produktanzahl (in &euro;)</strong></td>
			  <td width=\"80px\"><strong>Porto <a href=\"/Preistabelle.htm\" target=\"_blank\" rel=\"lightbox['portoliste']\" ><img src=\"/images/toolTipHelp.gif\" /></a> (in &euro;)</strong></td>
			  <td width=\"80px\"><strong>Preis inkl. P+V</strong> (in &euro;)</td>";
			  
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
					  <td>$abAusgabe</td>
					  <td>".$this->anzahlAusgaben[$key]."</td>";

					if($mitBerechnung)
					{
						//$this->anzahlAusgaben[$key] = 1;
						if($key=="Heft")
						{
							$keywords = preg_split("/[\s,]+/", $this->ausgabeNr["Heft"]);
							$this->anzahlAusgaben[$key] = (count($keywords)>0?count($keywords):1);
							$produktPreis = $this->preise[$key] * $this->anzahlAusgaben[$key];
						}
						else
							$produktPreis = $this->preise[$key];
							
						if("Index"==$key)
							$portoErgebnis = $this->GetPorto("Index");
						else
							$portoErgebnis = $this->GetPorto($this->anzahlAusgaben[$key]);
						
						$rueckGabe .= "
						<td>".sprintf("%3.2f", $produktPreis)."</td>
						<td>".sprintf("%3.2f", $portoErgebnis)."</td>
						<td align=\"right\">".sprintf("%3.2f", $produktPreis + $portoErgebnis)."</td>";
					}
					$rueckGabe .= "
					</tr>";
				}
			}
		if($mitBerechnung)
			$rueckGabe .= "
			<tr> 
				<td colspan=\"5\">&nbsp;</td>
			<tr>
			<tr bgcolor=\"#666699\"> 
				<td colspan=\"2\">&nbsp;</td>
				<td colspan=\"3\">Komplett Preis</td>
				<td align=\"right\"><strong	>".sprintf("%3.2f", $this->gesamtPreis)." &euro;</strong></td>
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
			<tr bgcolor=\"#336699\"> 
				<td colspan=\"6\">Ihr Kommentar:<p />".$this->kommentar."</td>
			<tr>
			</tbody>
		</table>
		
		Technisch bedingt k&ouml;nnen Fehler in obiger Berechnung auftreten. 
		Wir behalten uns vor ggf. eine Neuberechnung des Preises zuzustellen. In diesem Fall ist Ihre Bestellung selbstverst&auml;ndlich zu Ihrem Schutz NICHT bindend. 
		";
		/*******************************
		Bank zeuch		
		********************************/
		if($ablauf->bestellungAufgegeben){
			if($this->destination=='inland'){
				if(Aufenthalt::GetInstance()->GetUser()->bezahlung=="lastschrift"){
					$rueckGabe .= "Sie haben gew&auml;hlt per Lastschrift zu zahlen.
									Vielen Dank. Wir werden den oben stehenden Betrag von ihrem Konto einziehen und ihre Bestellung schnellstm&ouml;glich abschicken.";
				} else {
					$rueckGabe .= "Sie haben gew&auml;hlt per &Uuml;berweisung zu bezahlen. Bitte veranlassen sie eine Transaktion auf folgendes Konto:
								<blockquote>
									Rock&amp;Roll Musikmagazin:<br>
									Volksbank Oldenburg<br>
									Kto-Nr.: 34 32 502 600
									BLZ: 280 618 22	
								</blockquote>
								For transactions from abroad please don't forget to enter:
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
				
				Orders from <strong>ABROAD</strong> of germany result in <strong>HIGHER SHIPPING COSTS</strong>. We will bill these as well. Eventually occuring costs of bank-transactions must be payed by the client. <br>
				The Payment is ONLY posible per TRANSACTION.
				<blockquote>
					Rock&amp;Roll Musikmagazin:<br>
					Volksbank Oldenburg<br>
					Kto-Nr.: 34 32 502 600
					BLZ: 280 618 22	
				</blockquote>
				For transactions from abroad please don't forget to enter:
				<blockquote>
					BIC: GENODEF 1EDE <br />
					IBAN: DE02 2806 1822 3432 5026 00	
				</blockquote>
				Ihre Bestellung ist unterwegs sobald der Zahlungseingang auf unserem Konto best&auml;tigt wurde.
				<br>";
			}
		/*******************************
		Zurueck button		
		********************************/
		}else $rueckGabe .= "
				
				
				<br>";
	
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