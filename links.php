<?php

Print '<p>
                                Wir haben f&uuml;r Sie eine umfangreiche Liste an Internet-Links zum Thema Rock\'n\'Roll zusammengestellt. <br>
                                W&auml;hlen Sie eine der nachfolgenden Kategorien, um zu unseren Surftipps zu gelangen: </p>
 
                              <form name="form1" method="post" action="'.$_SERVER['REQUEST_URI'].'">
								<input name="linkMenu" type="hidden" value="-1">
								<input type="submit" name="Submit" value="Themen&uuml;bersicht anzeigen"><br />
                              </form>
                              <form name="form1" method="post" action="'.$_SERVER['REQUEST_URI'].'">
                                <input name="searchInput" type="text" value="Suchbegriff">
								<input type="submit" name="Submit" value="Senden"><br />
                              </form>
							  ';

// Abfrage nach Durchgang des Aufrufes und der Vollständigkeit der Angaben.

if( (!isset($_POST['linkMenu'])) || ($_POST['linkMenu']==-1)) 
{
	if( (isset($_POST['searchInput'])) && ($_POST['searchInput']!="Suchbegriff") )
	{
		$sucheErfolgreich = Aufenthalt::GetInstance()->Links()->suchAusgabe($_POST['searchInput']);
	} 
	else 
	{
		Aufenthalt::GetInstance()->Links()->PrintSections();
	}
} 
else 
{
	Aufenthalt::GetInstance()->Links()->rubrikAusgabe($_POST['linkMenu']);
}
													
Print '
							  <p align=center><a href="mailto:schreib@gunnardroege.de">Fehler melden</a></p>
<p>Hinweise zu Links:<br>
Mit Urteil vom 12. Mai 1998; Az. 312 O 85/98 - Haftung f&uuml;r Links;<br>
hat das Landgericht (LG) Hamburg entschieden, dass man durch die Erstellung eines Links die Inhalte der gelinkten Seite ggf. mit zu verantworten hat. <br>
Dies kann - so das Gericht - nur dadurch verhindert werden, dass man sich ausdr&uuml;cklich von diesen Inhalten distanziert. <br>
Dies tuen wir hiermit, wir &uuml;bernehmen keine Verantwortung f&uuml;r den Inhalt von gelinkten Homepages. <br>
Sollte einer der Links nicht mehr aktuell sein, bitten wir um Benachrichtigung!<a href="javascript:mausevent()"> </a>
';
?>