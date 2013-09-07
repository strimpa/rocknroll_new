<input type="hidden" name="tabelle" value="termine" />
<input name="benoetigt" type="hidden" value="kategorie,titel,datum,ort" />
<p>
<table style="width: 300px">
	<tr>
		<td>Monat: * </td>
		<td 
			<?php if(isset($suggestionErrors) && array_key_exists("kategorie", $suggestionErrors)) print 'class="errorField"'; ?>>
			<input name="kategorie" type="text" <?php if(array_key_exists("kategorie", $_POST)) print "value='".$_POST["kategorie"]."'"; ?> />
		</td>
	</tr>
	<tr>
		<td>Titel: * </td>
		<td 
			<?php if(isset($suggestionErrors) && array_key_exists("titel", $suggestionErrors)) print 'class="errorField"'; ?>>
			<input name="titel" type="text" <?php if(array_key_exists("titel", $_POST)) print "value='".$_POST["titel"]."'"; ?> />
		</td>
	</tr>
	<tr>
		<td>Datum: *</td>
		<td 
			<?php if(isset($suggestionErrors) && array_key_exists("datum", $suggestionErrors)) print 'class="errorField"'; ?>>
			<input name="datum" type="text" <?php if(array_key_exists("datum", $_POST)) print "value='".$_POST["datum"]."'"; ?> />
		</td>
	</tr>
	<tr>
		<td>Ort: *</td>
		<td 
			<?php if(isset($suggestionErrors) && array_key_exists("ort", $suggestionErrors)) print 'class="errorField"'; ?>>
			<input name="ort" type="text" <?php if(array_key_exists("ort", $_POST)) print "value='".$_POST["ort"]."'"; ?> />
		</td>
	</tr>
	<tr>
		<td>K&uuml;nstler: </td>
		<td 
			<?php if(isset($suggestionErrors) && array_key_exists("kuenstler", $suggestionErrors)) print 'class="errorField"'; ?>>
			<input name="kuenstler" type="text" <?php if(array_key_exists("kuenstler", $_POST)) print "value='".$_POST["kuenstler"]."'"; ?> />
		</td>
	</tr>
	<tr>
		<td>Beschreibung: </td>
		<td
			<?php if(isset($suggestionErrors) && array_key_exists("beschreibung", $suggestionErrors)) print 'class="errorField"'; ?>>
			<input name="beschreibung" type="text" <?php if(array_key_exists("beschreibung", $_POST)) print "value='".$_POST["beschreibung"]."'"; ?> />
		</td>
	</tr>
	<tr>
		<td>Zeit: </td>
		<td 
			<?php if(isset($suggestionErrors) && array_key_exists("zeit", $suggestionErrors)) print 'class="errorField"'; ?>>
			<input name="zeit" type="text" <?php if(array_key_exists("zeit", $_POST)) print "value='".$_POST["zeit"]."'"; ?> />
		</td>
	</tr>
	<tr>
		<td>Email: </td>
		<td 
			<?php if(isset($suggestionErrors) && array_key_exists("email", $suggestionErrors)) print 'class="errorField"'; ?>>
			<input name="email" type="text" <?php if(array_key_exists("email", $_POST)) print "value='".$_POST["email"]."'"; ?> />
		</td>
	</tr>
	<tr>
		<td>Telefon: </td>
		<td 
			<?php if(isset($suggestionErrors) && array_key_exists("telefon", $suggestionErrors)) print 'class="errorField"'; ?>>
			<input name="telefon" type="text" <?php if(array_key_exists("telefon", $_POST)) print "value='".$_POST["telefon"]."'"; ?> />
		</td>
	</tr>
	<tr>
		<td>Link: </td>
		<td
			<?php if(isset($suggestionErrors) && array_key_exists("link", $suggestionErrors)) print 'class="errorField"'; ?>>
			<input name="link" type="text" <?php if(array_key_exists("link", $_POST)) print "value='".$_POST["link"]."'"; ?> />
		</td>
	</tr>
</table>
</p>
<p>&nbsp;</p>

