<?php

require_once("Order/Bestellung.php");
/**
*	Diese Klasse beschreibt einen Benutzer
*/
class Benutzer{
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
	
	function GetWholeName()
	{
		$wholeName =($this->anrede." ".$this->vorName." ".$this->nachName);
		//print "whole anem : $wholeName"; 
		return $wholeName;
	}

	function GetMember($id)
	{
		switch (strtolower($id))
		{
		case "kundennr":
			return $this->kundenNummer;
			break;
		case "anrede":
			return $this->anrede;
			break;
		case "nachname":
			return $this->nachName;
			break;
		case "vorname":
			return $this->vorName;
			break;
		case "postadresse":
			return $this->adresse;
			break;
		case "postleitzahl":
			return $this->postleitzahl;
			break;
		case "ort":
			return $this->ort;
			break;
		case "land":
			return $this->land;
			break;
		case "telefon":
			return $this->telHome;
			break;
		case "email":
			return $this->eMail;
			break;
		default:
			return '';
			break;
		}
	}
	
	function Benutzer($args=NULL){
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
		$retVal = "
		<table border=\"0\" cellspacing=\"2\" cellpadding=\"2\" width=\"600px\">
                <TR bgcolor=\"#666699\">
                  <TD colspan=\"2\"><strong>Pers&ouml;nliche Daten:</strong></TD>
                </TR>
                <TR bgcolor=\"#336699\">
                  <TD width=\"200\" ALIGN=\"right\">Bereits Abonnent</TD>
                  <TD>$this->Abonnent</TD>
                </TR>
                <TR bgcolor=\"#336699\">
                  <TD ALIGN=\"right\">Anrede</TD>
                  <TD>$this->anrede</TD>
                </TR>
                <TR bgcolor=\"#336699\">
                  <TD ALIGN=\"right\"> Vorname</TD>
                  <TD>$this->vorName
                  </TD>
                </TR>
                <TR bgcolor=\"#336699\">
                  <TD ALIGN=\"right\"> Nachname </TD>
                  <TD>$this->nachName
                  </TD>
                </TR>
                <tr bgcolor=\"#336699\">
                  <TD ALIGN=\"right\"> Adresse </TD>
                  <TD>$this->adresse
                  </TD>
                </tr>
                <tr bgcolor=\"#336699\">
                  <TD ALIGN=\"right\"> Postleitzahl</TD>
                  <TD>$this->postleitzahl
                  </TD>
                </tr>
                <tr bgcolor=\"#336699\">
                  <TD ALIGN=\"right\"> Stadt </TD>
                  <TD>$this->ort
                  </TD>
                </tr>
                <tr bgcolor=\"#336699\">
                  <TD ALIGN=\"right\"> Land </TD>
                  <TD>$this->land
				   </TD>
                </tr>
                <TR bgcolor=\"#336699\">
                  <TD ALIGN=\"right\"> Telefon</TD>
                  <TD>$this->telHome
                  </TD>
                </TR>
                <TR bgcolor=\"#336699\">
                  <TD ALIGN=\"right\"> E-mail</TD>
                  <TD>$this->eMail
                  </TD>
                </TR>
                <TR bgcolor=\"#666699\">
                  <TD colspan=\"2\"><strong>Zahlungsart:</strong></TD>
                </TR>
                <TR bgcolor=\"#336699\">
                  <TD ALIGN=\"right\" valign=\"top\">gew&auml;hlte Methode:</TD>
                  <TD>
				";
				if(	$this->bezahlung=="lastschrift") {
					$retVal .=  "
						<strong>Lastschriftverfahren.</strong><br>
						Bankinstitut:      $this->bankInstitut <br>
						Kontonummer:        ";
					for($ind=0;$ind<strlen($this->ktnr);$ind++){
						if($ind<4) 
							$retVal .= (substr($this->ktnr,$ind,1));
						else 
							$retVal .= ("X");
					}
					$retVal .=  "		<br>
						Bankleitzahl: ";
					for($ind=0;$ind<strlen($this->blz);$ind++){
						if($ind<4)
							$retVal .= (substr($this->blz,$ind,1));
						else 
							$retVal .= ("X");
					}
					$retVal .=  "<br>";
				} else $retVal .= "
						<strong>&Uuml;berweisung</strong> auf<br>
									Rock&amp;Roll Musikmagazin:<br>
									Volksbank Oldenburg<br>
									Kto-Nr.: 140 325 026<br>
									BLZ: 280 900 45
				";
				$retVal .=  "
                  </TD>
                </TR>
            </TABLE>";
		return $retVal;
	}

	
	function registriereMich(){
		$rueckgabe = true;
		// Muss Eingaben abfragen.
		$this->Abonnent = (isset($_POST['schonKunde'])?$_POST['schonKunde']:"nein");
		$this->nachName = SafeDBString($_POST['Nachname']);
		$this->bezahlung = SafeDBString($_POST['bezahlung']);
		$this->anrede = SafeDBString($_POST['anrede']);
		$this->vorName = SafeDBString($_POST['Vorname']);
		$this->adresse = SafeDBString($_POST['Postadresse'].$_POST['Postadresse2']);
		$this->postleitzahl = SafeDBString($_POST['Postleitzahl']);
		$this->ort = SafeDBString($_POST['Ort']);
		$this->land = ($_POST['Land']=="germany"?$_POST['Land']:($_POST['Land']=="sonstigesEU"?$_POST['sonstigesLandEU']:$_POST['sonstigesLand']));
		$this->telHome=SafeDBString($_POST['Telefon']);
		$this->eMail = SafeDBString($_POST['EMail']);
		$this->bankInstitut = SafeDBString($_POST['Bankinstitut']);
		$this->ktnr = SafeDBString($_POST['Kontonummer']);
		$this->blz = SafeDBString($_POST['Bankleitzahl']);
		$this->kundenNummer = SafeDBString($_POST['kundenNr']==NULL?"":$_POST['kundenNr']);
		
		Aufenthalt::GetInstance()->GetAblauf()->aktuelleBestellung->kommentar.="\n ".
			($this->bezahlung=="lastschrift"?"Der Benutzer m&ouml;chte das Geld per Lastschrift eingezogen bekommen":"Der Benutzer bezahlt per &Uuml;berweisung");

		return $rueckgabe;
	}
	
}
?>