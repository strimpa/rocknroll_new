<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<title>Adminbereich</title>
<link href="css/adminStyles.css" rel="stylesheet" type="text/css" />
<link href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />

<script data-main="scripts/main" src="scripts/require.js"></script>
<script>
</script>
</head>

<body> <!--  onload="adminApp.Init();" -->

<span id="TitleSeite"><img src="images/Seite.png" /></span>
<div class="editfield" id="choice">
	<fieldset class="LeftGroupBox">
		<legend>Artikel auswaehlen:</legend>
		<select id="pagesDropDown" class="fullSizeControl">
			<option></option>
		</select>
		<input id="refreshButton" class="editButton" type="image" title="Refresh" src="images/061 Sync.png" />
		<table>
			<tr>
				<td>Name im Hauptmenu:</td>
				<td><input id="pageTitle" class="fullSizeControl" type="text" value="" readonly="true" /></td>
			</tr>
			<tr>
				<td>Anzeigepriorit&auml;t im Hauptmenu:</td>
				<td><input id="menuPriority" class="shortControl" type="text" value="" readonly="true" /></td>
			</tr>
			<tr>
				<td>Plugin:</td>
				<td>
					<select id="pluginDropDown" class="fullSizeControl">
						<option></option>
					</select>
				</td>
			</tr>
		</table>
	</fieldset>
	<fieldset class="RightGroupBox">
		<legend>Bearbeiten:</legend>
		<input id="createPageButton" class="editButton" type="image" title="Neu" src="images/022 Document Add.png" />
		<input id="editPageButton" class="editButton" type="image" title="Bearbeiten" src="images/081 Pen.png" />
		<input id="deletePageButton" class="deleteButton" type="image" title="Loeschen" src="images/023 Document Delete.png" />
	</fieldset>
</div>
<div class="editfield" id="settings">
	<fieldset class="LeftGroupBox">
		<legend>Ansicht</legend>
		Anzahl Tabellenspalten:<input id="spinNumTableCols" value="5"/>
	</fieldset>
</div>
<span id="TitleNavigation"><img src="images/Navigation.png" /></span>
<div class="editfield" id="meta">
	<fieldset class="LeftGroupBox">
		<legend>Submenu Eintraege:</legend>
		<div id="submenuEntries" class="listBox" style="width:150px; height:110px"></div>
		<input id="upMenuItemButton" class="editButton" type="image" title="Eintrag nach oben bewegen" src="images/037 ArrowUp.png" /><br />
		<input id="downMenuItemButton" class="editButton" type="image" title="Eintrag nach unten bewegen" src="images/038 ArrowDown.png" />
	</fieldset>
	<fieldset class="RightGroupBox">
		<legend>Bearbeiten:</legend>
		<input id="createMenuEntryButton" class="editButton" type="image" title="Neu" src="images/022 Document Add.png" />
		<input id="editMenuEntryButton" class="editButton" type="image" title="Bearbeiten" src="images/081 Pen.png" />
		<input id="deleteMenuEntryButton" class="deleteButton" type="image" title="Loeschen" src="images/023 Document Delete.png" />
	</fieldset>
	<fieldset class="RightGroupBox" style="top:60px;">
		<legend>Absatz-Anker Link:</legend>
		<select id="paragraphDropDown" class="fullSizeControl">
			<option></option>
		</select>
	</fieldset>
</div>

<span id="TitleInhalt"><img src="images/Inhalt.png" /></span>
<div class="editfield" id="contentHolder">
	<div id="contentCreate">
		<fieldset class="ThinGroupBox">
			<legend>Bearbeiten:</legend>
			<input id="createParagraphButton" class="editButton" type="image" title="Neuen Absatz erstellen" src="images/022 Document Add.png" />
			Bestehenden Absatz einfuegen:<select id="insertParagraphSelect" class="fullSizeControl">
					<option></option>
			</select>
		</fieldset>
		<fieldset class="ThinGroupBox">
			<legend>Parsing Fehler:</legend>
			<input type="button" id="errorOutputDelete" value="Loeschen" />
			<div id="errorOutput"></div>
		</fieldset>
	</div>
	<div id="admincontent">
	</div>
</div>

</body>

</html>
