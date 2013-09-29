<h2 align="left">Schritt 2: Bitte geben Sie ihre Liefer/Rechnungsadresse ein:</h2>
	<FORM METHOD="POST" action="/index/bestellen">
	<input type="hidden" name="formFilled" value=<?php print BestellAblauf::STEP_BENUTZERREG ?> />
	<table border="1" cellspacing="20" class="semanRahmen" noshade>
        <tr>
          <td>
          	<TABLE width="420" noshade>
        <TR >
          <TD ALIGN="right" class="normalFont"><p align="left">Sind Sie schon Abonnent? <br>
            Falls ja, geben Sie bitte Ihre                            <strong>                              Kundennummer </strong>an. <img src="/images/toolTipHelp.gif" alt="The Client-reference-number you can find on every bill as &quot;Kundennummer.&quot;" width="24" height="24" border="0" align="absmiddle" /><br>
            Der Eintrag ist optional, aber hilft uns sehr.                                 </TD>
          </TR>
        <TR >
          <TD height="21" ALIGN="right" class="normalFont"><div align="left">
            <input name="kundenNr" type="text" size="25" value="<?php print BestellAblauf::GetInst()->holeBenutzerDaten('kundenNr') ?>"/>
          </div></TD>
          </TR>
            <tr>
              <td>
				<table width="420" class="auto-style1">
                    <TR>
                      <TD width="173" ALIGN="right" class="normalFont">Anrede</TD>
                      <TD width="235" class="normalFont">
					  <select name="anrede">
					    <option>Bitte w&auml;hlen sie</option>
					    <option value="Herr" <?php print BestellAblauf::GetInst()->holeBenutzerDaten('anrede', 'Herr') ?>>Herr</option>
					    <option value="Frau" <?php print BestellAblauf::GetInst()->holeBenutzerDaten('anrede', 'Frau') ?>>Frau</option>
                      </select></TD>
                    </TR>
                    <TR> 
                      <TD ALIGN="right" class="normalFont"> Nachname  </TD>
                      <TD class="normalFont">   
                        <INPUT NAME="Nachname" SIZE=25 value="<?php print BestellAblauf::GetInst()->holeBenutzerDaten('Nachname') ?>"  />
                      </TD>
                    </TR>
                    <TR> 
                      <TD ALIGN="right" class="normalFont">  Vorname</TD>
                      <TD class="normalFont">   
                        <INPUT NAME="Vorname" SIZE=25 value="<?php print BestellAblauf::GetInst()->holeBenutzerDaten('Vorname') ?>"  />
                      </TD>
                    </TR>
                    <tr> 
                      <TD ALIGN="right" class="normalFont"> Adresse  </TD>
                      <TD class="normalFont">   
                        <INPUT NAME="Postadresse" SIZE=25 value="<?php print BestellAblauf::GetInst()->holeBenutzerDaten('Postadresse') ?>"  />
                      </TD>
                    </tr>
                    <tr> 
                      <TD ALIGN="right" class="normalFont">  Adresse 
                        (Fortsetzung.)</TD>
                      <TD class="normalFont">   
                        <INPUT NAME="Postadresse2" SIZE=25 value="<?php print BestellAblauf::GetInst()->holeBenutzerDaten('Postadresse2') ?>"  />
                      </TD>
                    </tr>
                    <tr> 
                      <TD ALIGN="right" class="normalFont"> Postleitzahl </TD>
                      <TD class="normalFont">   
                        <INPUT NAME="Postleitzahl" SIZE=12 MAXLENGTH=12 value="<?php print BestellAblauf::GetInst()->holeBenutzerDaten('Postleitzahl') ?>"  />
                      </TD>
                    </tr>
                    <tr> 
                      <TD ALIGN="right" class="normalFont"> Stadt  </TD>
                      <TD class="normalFont">   
                        <INPUT NAME="Ort" SIZE=25 value="<?php print BestellAblauf::GetInst()->holeBenutzerDaten('Ort') ?>"  />
                      </TD>
                    </tr>
                    <tr> 
                      <TD ALIGN="right" class="normalFont"> Land </TD>
                      <TD class="normalFont"><p />
                        <label onclick=" javascript:setPayment('germany')">
                          <input name="Land" type="radio" value="germany" <?php if(BestellAblauf::GetInst()->holeBenutzerDaten('Land')=="germany") print "checked=\"checked\"" ?> />
                          Germany</label>
                        <br />
                        <label onclick="javascript:setPayment('else')">
                          <input type="radio" name="Land" value="euausland" <?php if(BestellAblauf::GetInst()->holeBenutzerDaten('Land')!="germany") print "checked=\"checked\"" ?> />
                          EU</label>
                        <input name="sonstigesLandEU" type="text" id="sonstigesLandEU" value="<?php $land = BestellAblauf::GetInst()->holeBenutzerDaten('Land'); if($land!="germany") print $land; ?>" />
                        <br />
                        <label onclick="javascript:setPayment('else')">
                          <input type="radio" name="Land" value="noneuausland" />
                          NON-EU</label>
                        <input name="sonstigesLand" type="text" id="sonstigesLand" />
                      </TD>
                    </tr>
                    <TR> 
                      <TD ALIGN="right" class="normalFont">  Telefon</TD>
                      <TD class="normalFont">   
                        <INPUT NAME="Telefon" id="Telefon" SIZE=25 MAXLENGTH=25 value="<?php print BestellAblauf::GetInst()->holeBenutzerDaten('Telefon') ?>"  />
                      </TD>
                    </TR>
                    <TR>
                      <TD ALIGN="right" class="normalFont"> E-mail</TD>
                      <TD class="normalFont"><INPUT NAME="EMail" SIZE=25 value="<?php print BestellAblauf::GetInst()->holeBenutzerDaten('EMail') ?>"  />
                      </TD>
                    </TR>
                  </TABLE>
			  </td>
				</tr>
  </table>
  <table border="1" cellspacing="20" class="semanRahmen" noshade>
                    <tr>
                      <td>
				  <TABLE width="420">
				  <tr>
				  <td colspan="2">
                <br>
				  <div id="paymentOption" style="visibility:visible; ">
				<table>
				<tr>
					<TD class="normalFont"><input name="bezahlung" type="radio" value="lastschrift"></TD>
				  	<td class="normalFont" colspan="2">Zahlung via <b>Lastschrifteinzug:</b></td>
				</tr>
                <TR>
					<td>&nbsp;</td> 
                      <TD ALIGN="right" class="normalFont"> Name 
                        der Bank:</TD>
                      <TD class="normalFont">   
                        <INPUT NAME="Bankinstitut" SIZE=25 value="<?php print BestellAblauf::GetInst()->holeBenutzerDaten('Bankinstitut') ?>"  />
                      </TD>
				</TR>
				<TR> 
					<td>&nbsp;</td>
                      <TD ALIGN="right" class="normalFont">Kontonummer:</TD>
                      <TD class="normalFont">   
                        <INPUT NAME="Kontonummer" SIZE=12 value="<?php print BestellAblauf::GetInst()->holeBenutzerDaten('Kontonummer') ?>"  />
                      </TD>
				</TR>
				<tr> 
					<td>&nbsp;</td>
                      <TD ALIGN="right" class="normalFont">  Bankleitzahl:</TD>
                      <TD class="normalFont">   
                        <INPUT NAME="Bankleitzahl" SIZE=12 value="<?php print BestellAblauf::GetInst()->holeBenutzerDaten('Bankleitzahl') ?>"  />
                      </TD>
				</tr>
					</table>
					</div>
					</td>
					</tr>
					<tr><td width="22">&nbsp;</td><td width="382">&nbsp;</td></tr>
					<TR> 
						  <TD class="normalFont"><input name="bezahlung" type="radio" value="ueberweisung" checked></TD>
						  <TD class="normalFont" colspan="2">Zahlung via <strong>&Uuml;berweisung</strong>:<br></TD>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td class="normalFont" colspan="2">
						<blockquote>
							Rock&amp;Roll Musikmagazin:<br>
							Volksbank Oldenburg<br>
							Kto-Nr.: 34 32 502 600<br>
							BLZ: 280 618 22								
						</blockquote>
						Bitte vergessen Sie nicht folgende Informationen.<br />
						Anfallende Bankgeb&uuml;hren m&uuml;ssten wir Ihnen zus&auml;tzlich berechnen. <br />
						<blockquote>
							BIC: GENODEF 1EDE <br>
							IBAN: DE02 2806 1822 3432 5026 00								
						</blockquote>
						</td>
					</tr>
                  </TABLE>
				  </td>
			</tr>
    	</table>
		<b>Der n&auml;chste Schritt ist die Best&auml;tigung ihrer Bestellung.</b>
		<p />
		  <INPUT style="width:200px; height:25; color:#336633" TYPE=SUBMIT VALUE="Fortfahren">
		  <INPUT style="width:250px; height:25; color:#663333" TYPE=RESET VALUE="Eingabefelder zur&uuml;cksetzen">
		</td>
		</tr>
	</table>
</FORM>
