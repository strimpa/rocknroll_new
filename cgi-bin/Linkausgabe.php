<?php
/**
* Diese KLasse behandelt die Linkausgabe
**/
class Linkausgabe
{
var $myName;
var $hintergrundbild;
var $tabellenUeberschriften;
var $ablauf;
var $urls;
var $zeigeDatum;

	function Linkausgabe($derAblauf,$gewuenschteInhalte){
		$this->ablauf = $derAblauf;
		$this->myName = $gewuenschteInhalte;
		//$this->hintergrundbild = "/_pics/BG_".$this->myName;
		$this->urls = array("artists", "texte", "labels", "hitparaden", "instruments", "sellers", "oldtimer", "clothes", "queens", "misc", "swing", "radio", "tv", "magazines");
		$this->zeigeDatum=false;
	}
	function melden(){
		print "<font color=\"FF0000\">*************************************************************Hallo!*************************************************************</font>";
	}
	
	function rubrikAusgabe($inhaltsIndex)
	{
		$result = $this->ablauf->meineVerbindung->gibLinksAusFuerRubrik($this->urls[$inhaltsIndex]);
		
		// Linktabelle malen
			print "
			  <tr>
				<td width=\"500\" height=\"21\" valign=\"top\"><font color=\"#EBD5D5\" face=\"Arial, Helvetica, sans-serif\"><b>Beschreibung</b></font></td>
				<td valign=\"top\"><font color=\"#EBD5D5\" face=\"Arial, Helvetica, sans-serif\"><b>Link</b></font></td>
			  </tr>
			  <tr>
				<td colspan=\"2\" valign=\"top\" bgcolor=\"#CC0000\" height=\"3\"></td>
			  </tr>
			  <tr>
				<td colspan=\"3\" valign=\"top\">
						<div style=\"height:400px; overflow:scroll;\">	  
			<table rules=\"cols\" bordercolor=\"#CC0000\">";
		$bgToggle=false;
		$linkindex=0;
		while($row = mysql_fetch_row($result)){
			print "<tr>\n";
			print "<td width=\"500\"><font class=\"linkdescription\">$row[1]</font></td>";
			if($bgToggle){
				print "<td bgColor=\"#003399\"><a href=\"$row[2]\" target=\"_blank\">$row[2]</a></td>";
				$bgToggle=false;
				} else 
				{
				print "<td><a href=\"$row[2]\" target=\"_blank\">$row[2]</a></td>";
				$bgToggle=true;
/*				print "<td><input class=\"invisible\" id=\"linkchecker_".$linkindex."\" value=\"checking\" onLoad=\"\"></input></td>
				<script language=\"javascript\">
					<!-- 
					linkchecker.register('$row[2]', 'linkchecker_".$linkindex."');
					//-->
				</script>";
*/				}
			if($this->zeigeDatum)print "<td><a href=\"$row[3]\">$row[3]</a></td>";
			print "</tr>\n";
			$linkindex++;
		}		
		print "</table>
						</div>
					</td>
				</tr>
		";
	}
	
	function suchAusgabe($suchEingabe)
	{
		$result = $this->ablauf->meineVerbindung->gibLinksAusFuerSuche($suchEingabe);
		
		// Linktabelle malen
		$bgToggle=false;
			print "
			  <tr>
				<td width=\"500\" height=\"21\" valign=\"top\"><font color=\"#EBD5D5\" face=\"Arial, Helvetica, sans-serif\"><b>Beschreibung</b></font></td>
				<td valign=\"top\"><font color=\"#EBD5D5\" face=\"Arial, Helvetica, sans-serif\"><b>Link</b></font></td>
				<td align=\"right\" valign=\"top\"><font color=\"#EBD5D5\" face=\"Arial, Helvetica, sans-serif\"><b>Rubrik</b></font></td>
			  </tr>
			  <tr>
				<td colspan=\"3\" valign=\"top\" bgcolor=\"#CC0000\" height=\"3\"></td>
			  </tr>
			  <tr>
				<td colspan=\"3\" valign=\"top\">
						<div style=\"height:400px; overflow:scroll;\">	  
			<table width=\"100%\" rules=\"cols\" bordercolor=\"#CC0000\">";
		if(mysql_num_rows($result)<1){
			 print "<font class=\"linkdescription\">Ihre Sucheingabe \"$suchEingabe\" lieferte leider kein Ergebnis (".mysql_num_rows($result).")! <br>Versuchen Sie es erneut mit weniger Begriffen oder achten Sie auf Sonderzeichen, die missverstenden werden können. Viel Erfolg!</font>";
		} else {
			 print "<font class=\"linkdescription\">Ihre Sucheingabe \"$suchEingabe\" lieferte folgende(s) Ergebnis(se)!</font>";
			while($row = mysql_fetch_row($result)){
				print "<tr>\n";
				print "<td width=\"500\"><font class=\"linkdescription\">$row[1]</font></td>";
				if($bgToggle){
					print "<td bgColor=\"#003399\"><a href=\"$row[2]\" target=\"_blank\">$row[2]</a></td>";
					$bgToggle=false;
					} else 
					{
					print "<td><a href=\"$row[2]\" target=\"_blank\">$row[2]</a></td>";
					$bgToggle=true;
					}
				print "<td><font class=\"linkdescription\" align=\"right\">$row[0]</font></td>";
				if($this->zeigeDatum)print "<td><a href=\"$row[3]\">$row[2]</a></td>";
				print "</tr>\n";
			}		
		}
		print "</table>
						</div>
					</td>
				</tr>
		";
	}
	
	function showTableHeader(){
		print "
                          <tr>
                            <td colspan=\"3\" valign=\"top\">
									<div style=\"height:400px; overflow:scroll;\">	  
							";
	}
	function showTableFooter(){
		print "
									</div>
								</td>
                            </tr>
		";
	}

	function linkEingabe()
	{
	}
	
	function printHeader(){
		
	}

}
?>