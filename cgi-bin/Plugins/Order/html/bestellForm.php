	<FORM METHOD="POST" action="/index/bestellen">
<input type="hidden" name="formFilled" value=<?php print BestellAblauf::STEP_BESTELLUNG ?> />
<strong>Alle angezeigten Preise verstehen sich inklusive Portokosten.</strong><br />
Die angegebenen Auslandspreise gelten nur f&uuml;r das EU-Ausland. Bei Bestellungen aus dem NON-EU-AUSLAND ergeben sich erh&ouml;hte Portokosten. Wir behalten uns vor, diese gesondert in Rechnung zu stellen. Bitte markieren Sie das f&uuml;r Sie Zutreffende. <br />
Ggf. entstehende Bankgeb&uuml;hren bei &Uuml;berweisungen aus dem Ausland m&uuml;ssen vom Kunden getragen werden.<br />
<table border="0" cellspacing="2" width="630">
	<tr bgcolor="#336699">
	  <td colspan="7">
	  
	  	<input name="destination" type=radio value=germany checked /><strong> Inland </strong><br />
	    <input name="destination" type=radio value=euausland /><strong> EU Ausland</strong><br />
	    <input name="destination" type=radio value=noneuausland /><strong> Non-EU Ausland (No price calculation)</strong>		  </td>
  </tr>
	<tr>
	  <td colspan="3">&nbsp;</td>
	  <td>&nbsp;</td>
	  <td align="right">&nbsp;</td>
	  <td align="right">&nbsp;</td>
	  <td align="right">&nbsp;</td>
  </tr>
	<tr bgcolor="#336699">
	  <td colspan="3" bgcolor="#336699"> <p align="left"> <b>Das &quot;Rock'n'Roll-Musikmagazin&quot;</b> 
	  </td> 
	  <td width=232 bgcolor="#336699">&nbsp;</td>
	  <td width=82 align="right" bgcolor="#336699"> <strong>(ab) Ausgabe(n) Nr. </strong>
	  </td>
	  <td width="65" align="right"><p style="line-height: 100%" align=right><b> Deutschland</b></td>
	  <td width="55" align="right"><p style="line-height: 100%" align=right><b> Ausland</b></td>
	</tr>
	<tr>	
      <td rowspan="5"><img src="/images/cover_blue-with_circle.gif" width="76" height="100" align="right"></td>
      <td colspan="2">&nbsp;</td>
      <td>&nbsp;</td>
      <td align="right">&nbsp;</td>
      <td align="right">&nbsp;</td>
      <td align="right" nowrap>&nbsp;</td>
  </tr>
	<tr>
	  <td bgcolor="#336699">
        <input name="ProHeft" type="checkbox" id="ProHeft" value="ON" <?php if(isset($_POST['ProHeft'])) print "checked"; ?> />
      </td>
	  <td bgcolor="#336699"><strong>Probeheft</strong></td>
	  <td bgcolor="#336699">&nbsp;</td>
	  <td align="right" bgcolor="#336699">nach unserer Wahl</td>
	  <td align="right" bgcolor="#336699"><?php printf(" %4.2f &euro; ", BestellAblauf::GetInst()->inland_preise["ProHeft"]); ?></td>
	  <td align="right" bgcolor="#336699"><?php printf(" %4.2f &euro; ", BestellAblauf::GetInst()->ausland_preise["ProHeft"]); ?></td>
  </tr>
	<tr>
	  <td bgcolor="#336699">
        <input name="AktHeft" type="checkbox" id="AktHeft" value="ON" <?php if(isset($_POST['AktHeft'])) print "checked"; ?> />
      </td>
	  <td bgcolor="#336699"><strong>Aktuelle Ausgabe </strong></td>
	  <td bgcolor="#336699">&nbsp;</td>
	  <td align="right" bgcolor="#336699">&nbsp;</td>
	  <td align="right" bgcolor="#336699"><?php printf(" %4.2f &euro; ", BestellAblauf::GetInst()->inland_preise["AktHeft"]); ?></td>
	  <td align="right" bgcolor="#336699"><?php printf(" %4.2f &euro; ", BestellAblauf::GetInst()->ausland_preise["AktHeft"]); ?></td>
  </tr>
	<tr>
      <td bgcolor="#336699"><p style="line-height: 100%" align="left">
          <INPUT TYPE=CHECKBOX NAME="Heft" value="ON" <?php if(isset($_POST['Heft'])) print "checked"; ?> />
       </td>
      <td bgcolor="#336699"><span style="line-height: 100%"><strong>Einzelne Ausgaben</strong></span></td>
      <td bgcolor="#336699">&nbsp;</td>
      <td align="right" bgcolor="#336699">
          <input name="EinzelheftAusgabeNr" size=10 maxlength=200 value="<?php if(isset($_POST['EinzelheftAusgabeNr'])) print $_POST['EinzelheftAusgabeNr'] ?>"> *
      </td>
      <td align="right" bgcolor="#336699"><p style="line-height: 100%" align=right>
												 <?php printf(" %4.2f &euro; ", BestellAblauf::GetInst()->inland_preise["Heft"]); ?><strong><br></strong> </td>
      <td align="right" nowrap bgcolor="#336699"><?php printf(" %4.2f &euro; ", BestellAblauf::GetInst()->ausland_preise["Heft"]); ?></td>
  </tr>
	<tr>
      <td colspan="2">&nbsp;</td>
      <td align="right" colspan="4"> * Bei der Bestellung mehrerer Exemplare reduziert sich der Gesamtpreis, da die Portokosten variieren. Au&szlig;erdem sind Magazine aus den Jahren vor 2011 preisg&uuml;nstiger. Geben Sier hier ihre Wunschliste ein. Mehrere Eingaben mit Kommata.</td>
  </tr>
	<tr>
	  <td colspan="3">&nbsp;</td>
	  <td>&nbsp;</td>
	  <td align="right">&nbsp;</td>
	  <td align="right">&nbsp;</td>
	  <td align="right">&nbsp;</td>
  </tr>
	<tr>
	  <td rowspan="3"><img src="/images/collage.gif" width="150" height="126" align="right"></td>
	  <td colspan="2">&nbsp;</td>
	  <td>&nbsp;</td>
	  <td align="right">&nbsp;</td>
	  <td align="right">&nbsp;</td>
	  <td align="right" nowrap>&nbsp;</td>
  </tr>
	<tr bgcolor="#336699">
	  <td width="22"><p style="line-height: 100%" align="left">
          <INPUT TYPE="CHECKBOX" NAME="Abo" value="ON" <?php if(isset($_POST['Abo'])) print "checked"; ?> />
	  </td>
      <td width="108"><span style="line-height: 100%"><strong>Abonnement</strong></span></td>
      <td>6 Ausgaben</td>
      <td align="right"> * 
            <input name="AboAbAusgabe" size="5" maxlength="3" value="<?php if(isset($_POST['AboAbAusgabe'])) print $_POST['AboAbAusgabe'] ?>">
      </td>
      <td align="right"><p style="line-height: 100%" align=right><?php printf(" %4.2f &euro; ", BestellAblauf::GetInst()->inland_preise["Abo"]); ?> </td>
      <td align="right"><?php printf(" %4.2f &euro; ", BestellAblauf::GetInst()->ausland_preise["Abo"]); ?></td>
  </tr>
	<tr>
	  <td colspan="2">&nbsp;</td>
	  <td colspan="4" align="left">* wenn keine Angabe = dann ab der aktuellen Ausgabe </td>
  </tr>
	<tr>
	  <td colspan="7">&nbsp;</td>
  </tr>
	<tr>
	  <td width=163 rowspan="2"><img src="/images/Pakete.gif" width="150" align="right"></td>
	  <td bgcolor="#336699"><p style="line-height: 100%" align="left">
          <input name="KlPaket" type="checkbox" id="KlPaket" value="ON" <?php if(isset($_POST['KlPaket'])) print "checked"; ?> />
	  </td>
	  <td bgcolor="#336699"><span style="line-height: 100%"><strong>kleines Probepaket </strong></span></td>
	  <td bgcolor="#336699">10 verschiedene &auml;ltere Ausgaben nach unserer Wahl.  Sie k&ouml;nnen bevorzugte Nummern in das Textfeld am Ende des Bestellformulars eintragen. Wir versuchen es m&ouml;glich zu machen.</td>
	  <td align="right" bgcolor="#336699">nach unserer Wahl</td>
	  <td align="right" bgcolor="#336699"><p style="line-height: 100%" align="right"><?php printf(" %4.2f &euro; ", BestellAblauf::GetInst()->inland_preise["KlPaket"]); ?></td>
	  <td align="right" bgcolor="#336699"><?php printf(" %4.2f &euro; ", BestellAblauf::GetInst()->ausland_preise["KlPaket"]); ?></td>
	</tr>
    
	<tr>
	  <td bgcolor="#336699"><p style="line-height: 100%; margin-bottom: 0" align="left">
          <input name="GrPaket" type="checkbox" id="GrPaket" value="ON" <?php if(isset($_POST['GrPaket'])) print "checked"; ?> />
	  </td>
	  <td bgcolor="#336699"><span style="line-height: 100%; margin-bottom: 0"><strong>gro&szlig;es Probepaket </strong></span></td>
	  <td colspan="2" bgcolor="#336699">50 verschiedene &auml;ltere Ausgaben nach unserer Wahl.  Sie k&ouml;nnen bevorzugte Nummern in das Textfeld am Ende des Bestellformulars eintragen. Wir versuchen es m&ouml;glich zu machen.&nbsp;</td>
	  <td align="right" bgcolor="#336699"><p style="line-height: 100%" align="right"><?php printf(" %4.2f &euro; ", BestellAblauf::GetInst()->inland_preise["GrPaket"]); ?></td>
	  <td align="right" bgcolor="#336699"><?php printf(" %4.2f &euro; ", BestellAblauf::GetInst()->ausland_preise["GrPaket"]); ?></td>
    </tr>
    
	<tr>
	  <td rowspan="3"><img src="/images/Index_.jpg" width="100" align="center"></td>
	  <td colspan="9">&nbsp;</td>
	  <td>&nbsp;</td>
	  <td align="right">&nbsp;</td>
	  <td align="right">&nbsp;</td>
	  <td align="right" nowrap>&nbsp;</td>
  	</tr>
	<tr bgcolor="#336699">
	  <td width="22"><p style="line-height: 100%" align="left">
          <INPUT TYPE=CHECKBOX NAME="Index" value="ON" <?php if(isset($_POST['Index'])) print "checked"; ?> />
	  </td>
      <td width="108"><span style="line-height: 100%"><strong>Der Index</strong></span></td>
      <td colspan="2">Inhaltsverzeichnis aller Magazine von Ausgabe 1 bis 200.</td>
      <td align="right"><p style="line-height: 100%" align=right><?php printf(" %4.2f &euro; ", BestellAblauf::GetInst()->inland_preise["Index"]); ?> </td>
      <td align="right"><?php printf(" %4.2f &euro; ", BestellAblauf::GetInst()->ausland_preise["Index"]); ?></td>
  </tr>
	<tr>
	  <td colspan="2">&nbsp;</td>
	  <td>&nbsp;</td>
	  <td colspan="3" align="left">&nbsp;</td>
  </tr>
 </table>
	<P align="left" />M&ouml;chten Sie uns noch etwas mitteilen oder haben Sie spezielle W&uuml;nsche?
	<p /> 
	  <textarea rows="4" name="Sonstiges" cols="52"></textarea>
	
	<p />&nbsp; 
	<p /><b>Klicken Sie auf Fortfahren um zu best&auml;tigen und zum Schritt 2 zu gelangen, der Eingabe Ihrer pers&ouml;nlichen Daten. </b>
	<p /> 
	  <INPUT style="width:200px; height:25px; color:#336633" TYPE=SUBMIT VALUE="Fortfahren">
	  <INPUT style="width:200px; height:25px; color:#663333" TYPE=RESET VALUE="Eingabefelder zur&uuml;cksetzen">
	
</FORM>
