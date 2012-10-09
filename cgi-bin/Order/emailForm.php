<form action="/cgi-bin/sendmail.php" method="post" target="_blank">
	<input type=hidden name="recipient" value="mail@rocknroll-magazin.de">
		<textarea type=hidden style="visibility:hidden" name="postvars">
			<?php 
					foreach($_POST as $key=>$value)
					{
						if(NULL!=(Aufenthalt::GetInstance()->GetUser())) 
							print Aufenthalt::GetInstance()->GetUser()->printUserShort();
						print Aufenthalt::GetInstance()->GetAblauf()->aktuelleBestellung->zeigeBestellungen($this);
					}
					?>">
		</textarea>
    <table width="600"  border="1" cellspacing="0" cellpadding="0" class="semanRahmen">
      <tr>
        <td><p><strong>Haben Sie Fehler festgestellt oder wollen Sie uns etwas mitteilen? </strong></p>
          <table width="100%"  border="0" cellspacing="10" cellpadding="0">
            <tr>
              <td width="15%" class="normalFont">Ihr Name  </td>
              <td width="85%"><input name="realname" type="text" size="30" <?php 
              		if(NULL!=(Aufenthalt::GetInstance()->GetUser())) 
              			print "value=\"".html_entity_decode(Aufenthalt::GetInstance()->GetUser()->GetWholeName())."\"";
              	?> /></td>
            </tr>
            <tr>
              <td class="normalFont">Ihre E-mail Adresse </td>
              <td><input name="email" type="text" size="30" <?php 
              		if(NULL!=(Aufenthalt::GetInstance()->GetUser())) 
              			print "value=\"".Aufenthalt::GetInstance()->GetUser()->eMail."\""; 
              	?> /></td>
            </tr>
            <tr>
              <td colspan="2">
              	<textarea name="message" cols="52" rows="4" id="message">Bitte geben Sie hier Ihre Nachricht ein.
	              </textarea></td>
	            </tr>
	          </table>
	          <P>
	            <input style="width:200px; height:25px; color:#336633" type=SUBMIT value="Abschicken">
	            <INPUT align="right" style="width:250px; height:25px; color:#663333" TYPE=RESET VALUE="Eingabefelder zur&uuml;cksetzen">
	          </P>          
	        </td>
	      </tr>
	  </table>
  </form>
