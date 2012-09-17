<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<title>Adminbereich</title>
<link href="../../css/mainstyles.css" rel="stylesheet" type="text/css" />
<link href="../../css/adminStyles.css" rel="stylesheet" type="text/css" />
<link href="../../css/dot-luv/jquery-ui-1.8.12.custom.css" rel="stylesheet" type="text/css" />
<link href="../../css/microgallery_style.css" rel="stylesheet" type="text/css" />
<link href="../../css/jquery.timeentry.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="../../script/jquery-1.5.1.min.js"></script>
<script type="text/javascript" src="../../script/ui/jquery.ui.core.js"></script>
<script type="text/javascript" src="../../script/ui/jquery.ui.widget.js"></script>
<script type="text/javascript" src="../../script/ui/jquery.ui.button.js"></script>
<script type="text/javascript" src="../../script/ui/jquery.ui.spinner.js"></script>
<script type="text/javascript" src="../../script/ui/jquery.ui.datepicker.js"></script>

<script type="text/javascript" src="../../script/tiny_mce/jquery.tinymce.js"></script>
<!--<script type="text/javascript" src="../../script/jquery.microgallery.js"></script>-->
<script type="text/javascript" src="../../script/jquery.timeentry.min.js"></script>

<script type="text/javascript" src="../../script/jquery.jeditable.mini.js"></script>

<script type="text/javascript" src="creationTemplates.json"></script>
<script type="text/javascript" src="adminApp.js"></script>
</head>

<body> <!--  onload="adminApp.Init();" -->

<span id="TitleSeite"><img src="images/Seite.png" /></span>
<div class="editfield" id="choice">
	<fieldset class="LeftGroupBox">
		<legend>Artikel auswaehlen:</legend>
		<select id="pagesDropDown" class="fullSizeControl">
			<option></option>
		</select>
		<input id="pageTitle" class="fullSizeControl" type="text" value="" readonly="true" />
		<input id="menuPriority" class="shortControl" type="text" value="" readonly="true" />
	</fieldset>
	<fieldset class="RightGroupBox">
		<legend>Neu erstellen:</legend>
		<input id="createPageButton" class="createButton" type="button" value="Neu" />
		<input id="editPageButton" class="editButton" type="button" value="Bearbeiten" style="width:75px;" />
		<input id="deletePageButton" class="deleteButton" type="button" value="Loeschen" style="width:70px;" />
	</fieldset>
</div>
<span id="TitleNavigation"><img src="images/Navigation.png" /></span>
<div class="editfield" id="meta">
	<fieldset class="LeftGroupBox">
		<legend>Submenu Eintraege:</legend>
		<div id="submenuEntries" class="listBox" style="width:150px; height:110px"></div>
		<input id="upMenuItemButton" class="moveMenuItemButton" type="button" value="Nach oben" />
		<input id="downMenuItemButton" class="moveMenuItemButton" type="button" value="Nach unten" />
	</fieldset>
	<fieldset class="RightGroupBox">
		<legend>Neu erstellen:</legend>
		<input id="createMenuEntryButton" class="createButton" type="button" value="Neu" />
		<input id="editMenuEntryButton" class="editButton" type="button" value="Bearbeiten" style="width:75px;" />
		<input id="deleteMenuEntryButton" class="deleteButton" type="button" value="Loeschen" style="width:70px;" />
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
		<input id="createParagraphButton" class="createButton" type="button" value="Neuen Absatz erstellen" />
		Bestehenden Absatz einfuegen:<select id="insertParagraphSelect" class="fullSizeControl">
				<option></option>
		</select>
		<fieldset id="errorOutput">
			<legend>Parsing Fehler:</legend>
			
		</fieldset>
	</div>
	<div id="admincontent">
	</div>
</div>

</body>

</html>
