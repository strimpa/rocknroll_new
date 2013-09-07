<input type="hidden" name="tabelle" value="verlinkung" />
<input name="benoetigt" type="hidden" value="kategorie,beschreibung,link" />
<p>
<table style="width: 300px">
	<tr>
		<td>Kategorie: *&nbsp; </td>
		<td 
			<?php if(isset($suggestionErrors) && array_key_exists("kategorie", $suggestionErrors)) print 'class="errorField"'; ?>>
			<input name="kategorie" type="text" <?php if(array_key_exists("kategorie", $_POST)) print "value='".$_POST["kategorie"]."'"; ?> />
		</td>
	</tr>
	<tr>
		<td>Beschreibung: *&nbsp; </td>
		<td
			<?php if(isset($suggestionErrors) && array_key_exists("beschreibung", $suggestionErrors)) print 'class="errorField"'; ?>>
			<input name="beschreibung" type="text" <?php if(array_key_exists("beschreibung", $_POST)) print "value='".$_POST["beschreibung"]."'"; ?> />
		</td>
	</tr>
	<tr>
		<td>Link: *</td>
		<td
			<?php if(isset($suggestionErrors) && array_key_exists("link", $suggestionErrors)) print 'class="errorField"'; ?>>
			<input name="link" type="text" <?php if(array_key_exists("link", $_POST)) print "value='".$_POST["link"]."'"; ?> />
		</td>
	</tr>
</table>
</p>
<p>&nbsp;</p>

