<FORM METHOD="POST" action="%%ServerVar(REQUEST_URI)%%">
<input type="hidden" name="formFilled" id="formFilled" value="j" />
Die angegebenen Auslandspreise gelten nur f&uuml;r das EU-Ausland. Bei Bestellungen aus dem NON-EU-AUSLAND ergeben sich erh&ouml;hte Portokosten. Wir behalten uns vor, diese gesondert in Rechnung zu stellen. Bitte markieren Sie das f&uuml;r Sie Zutreffende. <br />
Ggf. entstehende Bankgeb&uuml;hren bei &Uuml;berweisungen aus dem Ausland m&uuml;ssen vom Kunden getragen werden.<br />
<table border="0" cellspacing="2">
	<thead>
		<tr bgcolor="#336699">
		  <td colspan="7">
		  	<div class="radiolabel"><input name="destination" id="destination" type="radio" value="inland" checked />Input</div>
		    <div class="radiolabel"><input name="destination" type="radio" value="euausland" />EU Ausland</div>
		    <div class="radiolabel"><input name="destination" type="radio" value="noneuausland" />Non-EU Ausland (No price calculation)</div>		  
		 </td>
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
		  </p></td> 
		  <td bgcolor="#336699">&nbsp;</td>
		  <td align="right" bgcolor="#336699"> <strong>(ab) Ausgabe(n) Nr. </strong>
		  </td>
		  <td align="right"><p style="line-height: 100%" align=right><b> Inland</b></p></td>
		  <td align="right"><p style="line-height: 100%" align=right><b> Ausland</b></p></td>
		</tr>
	</thead>
	<tbody>
		<tr>
		  <td rowspan="3"><img src="/images/collage.gif" height="126" align="right"></td>
		  <td colspan="2">&nbsp;</td>
		  <td>&nbsp;</td>
		  <td align="right">&nbsp;</td>
		  <td align="right">&nbsp;</td>
		  <td align="right" nowrap>&nbsp;</td>
	  </tr>
		<tr bgcolor="#336699">
		  <td><p style="line-height: 100%" align="left">
              <input type="checkbox" name="Bestelltyp_Abo" id="Bestelltyp_Abo" value="Bestelltyp_Abo" />
		  </td>
          <td><span style="line-height: 100%"><strong>Abonnement</strong></span></td>
          <td>6 Ausgaben ab der <a href="aktuell.php">aktuellen Ausgabe</a></td>
          <td align="right"> * 
                <input name="AboAbAusgabe" id="AboAbAusgabe" size=5 maxlength=3 value="" />
          </td>
          <td align="right"><p style="line-height: 100%" align=right>%%Preis(0)%%;</p></td>
          <td align="right">%%Preis(8)%%</td>
	  </tr>
		<tr>
		  <td colspan="2">&nbsp;</td>
		  <td>&nbsp;</td>
		  <td colspan="3" align="left">* keine Angabe = ab der aktuellen Ausgabe </td>
	  </tr>
		<tr>
		  <td colspan="7">&nbsp;</td>
	  </tr>
		<tr>
		  <td rowspan="4"><img src="/images/cover_blue-with_circle.gif" height="100" align="right" /></td>
		  <td colspan="2">&nbsp;</td>
		  <td>&nbsp;</td>
		  <td align="right">&nbsp;</td>
		  <td align="right">&nbsp;</td>
		  <td align="right" nowrap>&nbsp;</td>
	  </tr>
		<tr>
		  <td bgcolor="#336699"> <p style="line-height: 100%" align="left"> 
			  <input type="checkbox" name="Bestelltyp_Einzelheft" id="Bestelltyp_Einzelheft" value="Bestelltyp_Einzelheft" />
			   
		  </td>
		  <td bgcolor="#336699"><span style="line-height: 100%"><strong>einzelne Ausgaben</strong></span></td>
		  <td bgcolor="#336699">&nbsp;</td>
		  <td align="right" bgcolor="#336699">
			* 
			<input name="EinzelheftAusgabeNr" id="EinzelheftAusgabeNr" size=5 maxlength=200 value="" />		  </td>
		  <td align="right" bgcolor="#336699"> <p style="line-height: 100%" align=right>%%Preis(1)%%<strong><br>
		  </strong> </td>
		  <td align="right" nowrap bgcolor="#336699">%%Preis(9)%%</td>
		</tr>
		<tr>
		  <td bgcolor="#336699">
		    <input name="Bestelltyp_Probeheft" id="Bestelltyp_Probeheft" type="checkbox" value="Bestelltyp_Probeheft" />
          </td>
		  <td bgcolor="#336699"><strong>Probeheft</strong></td>
		  <td bgcolor="#336699">&nbsp;</td>
		  <td align="right" bgcolor="#336699">nach unserer Wahl</td>
		  <td align="right" bgcolor="#336699">%%Preis(2)%%</td>
		  <td align="right" bgcolor="#336699">%%Preis(10)%%</td>
	    </tr>
		<tr>
		  <td colspan="2">&nbsp;</td>
		  <td>&nbsp;</td>
		  <td align="right">&nbsp;</td>
		  <td align="right">&nbsp;</td>
		  <td align="right">&nbsp;</td>
	  </tr>
		<tr>
		  <td colspan="7">&nbsp;</td>
	  </tr>
		<tr>
		  <td rowspan="3"><img src="/images/collage.gif" height="135" align="right"></td>
		  <td bgcolor="#336699"><p style="line-height: 100%" align="left">
              <input name="Bestelltyp_klProbepaket" id="Bestelltyp_klProbepaket" type="checkbox" value="Bestelltyp_klProbepaket" />
		  </p></td>
		  <td bgcolor="#336699"><span style="line-height: 100%"><strong>kleines Probepaket </strong></span></td>
		  <td bgcolor="#336699">10 verschiedene &auml;ltere Ausgaben nach unserer Wahl.  Sie k&ouml;nnen bevorzugte Nummern in das Textfeld am Ende des Bestellformulars eintragen. Wir versuchen es m&ouml;glich zu machen.</td>
		  <td align="right" bgcolor="#336699">nach unserer Wahl</td>
		  <td align="right" bgcolor="#336699"><p style="line-height: 100%" align="right">%%Preis(3)%%</p></td>
		  <td align="right" bgcolor="#336699">%%Preis(11)%%</td>
		</tr>
		<tr>
		  <td bgcolor="#336699"><p style="line-height: 100%; margin-bottom: 0" align="left">
              <input name="Bestelltyp_grProbepaket" id="Bestelltyp_grProbepaket" type="checkbox" value="Bestelltyp_grProbepaket" />
		  </p></td>
		  <td bgcolor="#336699"><span style="line-height: 100%; margin-bottom: 0"><strong>gro&szlig;es Probepaket </strong></span></td>
		  <td colspan="2" bgcolor="#336699">50 verschiedene &auml;ltere Ausgaben nach unserer Wahl.  Sie k&ouml;nnen bevorzugte Nummern in das Textfeld am Ende des Bestellformulars eintragen. Wir versuchen es m&ouml;glich zu machen.&nbsp;</td>
		  <td align="right" bgcolor="#336699"><p style="line-height: 100%" align="right">%%Preis(4)%%</p></td>
		  <td align="right" bgcolor="#336699">%%Preis(12)%%</td>
	    </tr>
		<tr bgcolor="#336699">
		  <td colspan="2"><p style="line-height: 100%; margin-bottom: 0" align="left">&nbsp;</p></td>
		  <td colspan="2">
            <input name="Bestelltyp_ProbepaketMitAbo" id="Bestelltyp_ProbepaketMitAbo" type="checkbox" value="Bestelltyp_ProbepaketMitAbo" />
          mit Abonnement ab aktueller Ausgabe </td>
		  <td align="right"><p style="line-height: 100%" align="right">%%Preis(5)%%</p></td>
		  <td align="right">%%Preis(13)%%</td>
	    </tr>
	</tbody>
  </table>
	<P align="left">M&ouml;chten Sie uns noch etwas mitteilen oder haben Sie spezielle W&uuml;nsche?</P>
	<P> 
	  <textarea rows="4" name="Sonstiges" id="Sonstiges" cols="52"></textarea>
	</P>
	<P>&nbsp; </P>
	<P><b>Klicken Sie auf Fortfahren um zu best&auml;tigen und zum Schritt 2 zu gelangen, der Eingabe Ihrer pers&ouml;nlichen Daten. </b></P>
	<p> 
	  <input style="width:200px; height:25px; color:#000000" TYPE=SUBMIT VALUE="Fortfahren" />
	</p>
</FORM>