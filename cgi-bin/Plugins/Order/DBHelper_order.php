<?php

/***********************************************************************************
**   Bestellung
***********************************************************************************/

function gibBestellungInDB($user, $aktuelleBestellung, $kundenID)
{
	$rueckGabe = "";
	$abbruch=false;
	
	$bestellDBString = $aktuelleBestellung->dbText;
	$bestellDBString.="\nBezahlverfahren: ".$aktuelleBestellung->bezahlVerfahren;
	$bestellNamensArray = array("kundenID", "bestellungen", "kommentar", "bestellDatum");
	$bestellAusGabeArray = array(	
	// Eingabewerte mit auszugebenden Werten vergleichen
		($kundenID),
		$bestellDBString,
		($aktuelleBestellung->kommentar == "" ? NULL : $aktuelleBestellung->kommentar),
		($aktuelleBestellung->bestellDatum == "" ? NULL : $aktuelleBestellung->bestellDatum));
	
		$result = DBCntrl::GetInst()->Conn()->InsertTableContent(
					array(
						'table'=>'bestellung', 
						'fields'=>$bestellNamensArray,
						'values'=>$bestellAusGabeArray
						));
	return $result;
}


?>