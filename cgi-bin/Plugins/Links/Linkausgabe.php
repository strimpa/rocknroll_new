<?php

require_once('DBHelper_links.php');

/**
* Diese KLasse behandelt die Linkausgabe
**/
class Linkausgabe
{
var $hintergrundbild;
var $tabellenUeberschriften;
var $urls;
var $zeigeDatum;
	private static $instance;

	private function Linkausgabe(){
		//$this->hintergrundbild = "/_pics/BG_".$this->myName;
		$this->urls = array("artists", "texte", "labels", "hitparaden", "instruments", "sellers", "oldtimer", "clothes", "queens", "misc", "swing", "radio", "tv", "magazines");
		$this->zeigeDatum=false;
	}
	
	public static function &GetInst()
	{
		if(!isset(self::$instance))
			self::$instance = new Linkausgabe(); 
		
		return self::$instance;
	}
	
	public function PrintSections()
	{
		$results = getLinkSections();
		$builder = ContentMgr::GetInst()->GetBuilder();
		print "<ul>";
		foreach($results as $row)
		{
			$formString =$builder->BuildFormForLink($row['category']); 
			print "<li>$formString</li>";
		}
		print "</ul>";
	} 
	
	public function TableOutput($result)
	{
		// Linktabelle malen
			print "
			<table rules=\"cols\" border=\"0\">
			  <tr>
				<td width=\"500\" height=\"21\" valign=\"top\"><b>Beschreibung</b></td>
				<td valign=\"top\"><b>Link</b></td>
			  </tr>
			  <tr>
				<td colspan=\"2\" valign=\"top\" bgcolor=\"#730B24\" height=\"3\"></td>
			  </tr>
			";
		$bgToggle=false;
		$linkindex=0;
		foreach($result as $row)
		{
			print "<tr>\n";
			$descr = htmlentities(utf8_decode($row['description']));
			$link = $row['url'];
			print "<td width=\"500\">$descr</td>";
			$bg = "";
			if($bgToggle=!$bgToggle) 
				$bg = "bgColor=\"#003399\"";
			print "<td $bg><a href=\"$link\" target=\"_blank\">$link</a></td>";
/*				print "<td><input class=\"invisible\" id=\"linkchecker_".$linkindex."\" value=\"checking\" onLoad=\"\"></input></td>
				<script language=\"javascript\">
					<!-- 
					linkchecker.register('$row[2]', 'linkchecker_".$linkindex."');
					//-->
				</script>";
*/
//			if($this->zeigeDatum)print "<td><a href=\"$row[3]\">$row[3]</a></td>";
			print "</tr>\n";
			$linkindex++;
		}		
		print "
				</table>
		";
	}
	
	public function rubrikAusgabe($inhalt)
	{
		$result = gibLinksAusFuerRubrik($inhalt);
		$this->TableOutput($result);
	}
	
	function suchAusgabe($suchEingabe)
	{
		$result = gibLinksAusFuerSuche($suchEingabe);
		
		// Linktabelle malen
		$this->TableOutput($result);
	}
	
}
?>