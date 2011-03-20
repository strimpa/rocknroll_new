<?php

require_once("Order/Bestellung.php");
/**
*	Diese Klasse beschreibt einen Benutzer
*/
class Benutzer
{
	var $anrede;
	var $vorName;
	var $nachName;
	var $adresse;
	var $postleitzahl;
	var $ort;
	var $land;
	var $telBuero;
	var $telHome;
	var $fax;
	var $eMail;
	var $bezahlung;
	var $bankInstitut;
	var $ktnr;
	var $blz;
	var $sessionNummer;
	var $kundenNummer;
	var $Abonnent;
	var $aufenthalt;
	var $kommentar;
	var $bestellDatum;
	
	function Benutzer($args){
		if(!is_array($args)){
			//echo "Deklaration ohne Werte";
			$this->aufenthalt = $args;
			$this->nachName="";
			$this->kundenNummer="";
			$this->Abonnent=false;
		} else {
			$this->kundenNummer=$args[0];
			$this->nachName=$args[1];
			$this->Abonnent=true;
			$this->aufenthalt = "";
		}
		$this->vorName="";
		$this->adresse="";
		$this->postleitzahl="";
		$this->ort="";
		$this->land="";
		$this->telBuero="";
		$this->telHome="";
		$this->fax="";
		$this->eMail="";
		$this->bankInstitut="";
		$this->ktnr="";
		$this->blz="";
		$this->sessionNummer="";
		$this->kommentar="";
	}

	
	function printUserShort(){
		print "
		<table border=\"0\" cellspacing=\"2\" cellpadding=\"2\" width=\"600px\">
                <TR bgcolor=\"#EBD5D5\">
                  <TD colspan=\"2\"><font size=\"2\" face=\"Arial\" color=\"black\" ><strong>Pers&ouml;nliche Daten:</strong></font></TD>
                </TR>
                <TR bgcolor=\"#336699\">
                  <TD width=\"200\" ALIGN=\"right\" class=\"normalFont\">vorheriger Abonnent</TD>
                  <TD class=\"normalFont\">$this->Abonnent</TD>
                </TR>
                <TR bgcolor=\"#336699\">
                  <TD ALIGN=\"right\" class=\"normalFont\">Anrede</TD>
                  <TD class=\"normalFont\">$this->anrede</TD>
                </TR>
                <TR bgcolor=\"#336699\">
                  <TD ALIGN=\"right\" class=\"normalFont\"> Vorname</TD>
                  <TD class=\"normalFont\">$this->vorName
                  </TD>
                </TR>
                <TR bgcolor=\"#336699\">
                  <TD ALIGN=\"right\" class=\"normalFont\"> Nachname </TD>
                  <TD class=\"normalFont\">$this->nachName
                  </TD>
                </TR>
                <tr bgcolor=\"#336699\">
                  <TD ALIGN=\"right\" class=\"normalFont\"> Adresse </TD>
                  <TD class=\"normalFont\">$this->adresse
                  </TD>
                </tr>
                <tr bgcolor=\"#336699\">
                  <TD ALIGN=\"right\" class=\"normalFont\"> Postleitzahl</TD>
                  <TD class=\"normalFont\">$this->postleitzahl
                  </TD>
                </tr>
                <tr bgcolor=\"#336699\">
                  <TD ALIGN=\"right\" class=\"normalFont\"> Stadt </TD>
                  <TD class=\"normalFont\">$this->ort
                  </TD>
                </tr>
                <tr bgcolor=\"#336699\">
                  <TD ALIGN=\"right\" class=\"normalFont\"> Land </TD>
                  <TD class=\"normalFont\">$this->land
				   </TD>
                </tr>
                <TR bgcolor=\"#336699\">
                  <TD ALIGN=\"right\" class=\"normalFont\"> Telefon</TD>
                  <TD class=\"normalFont\">$this->telHome
                  </TD>
                </TR>
                <TR bgcolor=\"#336699\">
                  <TD ALIGN=\"right\" class=\"normalFont\"> E-mail</TD>
                  <TD class=\"normalFont\">$this->eMail
                  </TD>
                </TR>
                <TR bgcolor=\"#EBD5D5\">
                  <TD class=\"normalFont\" colspan=\"2\"><font size=\"2\" face=\"Arial\" color=\"black\" ><strong>Zahlungsart:</strong></font></TD>
                </TR>
                <TR bgcolor=\"#336699\">
                  <TD ALIGN=\"right\" valign=\"top\" class=\"normalFont\">gew�hlte Methode:</TD>
                  <TD class=\"normalFont\">
				";
				if(	$this->bezahlung=="lastschrift") {
					print "
						<strong>Lastschriftverfahren.</strong><br>
						Bankinstitut:      $this->bankInstitut <br>
						Kontonummer:        ";
					for($ind=0;$ind<strlen($this->ktnr);$ind++){
						if($ind<4)print(substr($this->ktnr,$ind,1));
						else print("X");
					}
					print "		<br>
						Bankleitzahl: ";
					for($ind=0;$ind<strlen($this->blz);$ind++){
						if($ind<4)print(substr($this->blz,$ind,1));
						else print("X");
					}
					print "<br>";
				} else print"
						<strong>&Uuml;berweisung</strong> auf<br>
									Rock&Roll Musikmagazin:<br>
									Volksbank Oldenburg<br>
									Kto-Nr.: 140 325 026<br>
									BLZ: 280 900 45
				";
				print "
                  </TD>
                </TR>
            </TABLE><p></p><p></p>";
	}

	
	function registriereMich(){
		$rueckgabe = true;
		// Muss Eingaben abfragen.
		$this->Abonnent = (isset($_POST['schonKunde'])?$_POST['schonKunde']:"nein");
		$this->nachName = $_POST['Nachname'];
		$this->bezahlung = $_POST['bezahlung'];
		$this->anrede = $_POST['anrede'];
		$this->vorName = $_POST['Vorname'];
		$this->adresse = $_POST['Postadresse'].$_POST['Postadresse2'];
		$this->postleitzahl = $_POST['Postleitzahl'];
		$this->ort = $_POST['Ort'];
		$this->land = ($_POST['Land']=="germany"?$_POST['Land']:($_POST['Land']=="sonstigesEU"?$_POST['sonstigesLandEU']:$_POST['sonstigesLand']));
		$this->telHome=$_POST['Telefon'];
		$this->eMail = $_POST['EMail'];
		$this->bankInstitut = $_POST['Bankinstitut'];
		$this->ktnr = $_POST['Kontonummer'];
		$this->blz = $_POST['Bankleitzahl'];
		$this->kundenNummer = $_POST['kundenNr'];
		
		$this->aktuelleBestellung->kommentar.="\n ".
			($this->bezahlung=="lastschrift"?"Der Benutzer m�chte das Geld per Lastschrift eingezogen bekommen":"Der Benutzer bezahlt per &Uuml;berweisung");

		return $rueckgabe;
	}
	
	function holeStatus(){
	}
	
	function logOut(){
	}
	
	function getName(){
		print "mein Name ist ".$this->Name;
	}
	function melden(){
		print "Hallo!";
	}
	
}
?>