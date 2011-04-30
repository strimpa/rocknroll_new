<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<title>Adminbereich</title>
<link href="../../css/mainStyles.css" rel="stylesheet" type="text/css" />
<link href="../../css/adminStyles.css" rel="stylesheet" type="text/css" />
<link href="../../css/dot-luv/jquery-ui-1.8.12.custom.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="../../script/jquery-1.5.1.min.js"></script>
<script type="text/javascript" src="../../script/ui/jquery.ui.core.js"></script>
<script type="text/javascript" src="../../script/ui/jquery.ui.widget.js"></script>
<script type="text/javascript" src="../../script/ui/jquery.ui.button.js"></script>
<script type="text/javascript" src="../../script/ui/jquery.ui.spinner.js"></script>
<script type="text/javascript" src="../../script/ui/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="creationTemplates.json"></script>
<script type="text/javascript" src="adminApp.js"></script>
</head>

<body> <!--  onload="adminApp.Init();" -->

<div class="editfield" id="choice">
	<fieldset class="LeftGroupBox">
		<legend>Artikel auswaehlen:</legend>
		<select id="pagesDropDown" class="fullSizeControl">
			<option></option>
		</select>
		<input id="pageTitle" class="fullSizeControl" type="text" value="dummy" readonly="true" />
	</fieldset>
	<fieldset class="RightGroupBox">
		<legend>Neu erstellen:</legend>
		<input id="createPageButton" class="createButton" type="button" value="create" />
		<input id="editPageButton" class="editButton" type="button" value="edit" />
		<input id="deletePageButton" class="deleteButton" type="button" value="delete" />
	</fieldset>
</div>
<div class="editfield" id="meta">
	<fieldset class="LeftGroupBox">
		<legend>Submenu Eintraege:</legend>
		<div id="submenuEntries" class="listBox" style="width:150px; height:110px"></div>
	</fieldset>
	<fieldset class="RightGroupBox">
		<legend>Neu erstellen:</legend>
		<input id="createMenuEntryButton" class="createButton" type="button" value="create" />
		<input id="editMenuEntryButton" class="editButton" type="button" value="edit" />
		<input id="deleteMenuEntryButton" class="deleteButton" type="button" value="delete" />
	</fieldset>
	<fieldset class="RightGroupBox" style="top:60px;">
		<legend>Absatz-Anker Link:</legend>
		<select id="paragraphDropDown" class="fullSizeControl">
			<option></option>
		</select>
	</fieldset>
</div>
<div class="editfield" id="contentCreate">
	<input id="createParagraphButton" class="createButton" type="button" value="Neuen Absatz erstellen" />
	Bestehenden Absatz einfuegen:<select id="insertParagraphSelect" class="fullSizeControl">
			<option></option>
	</select>
</div>
<div class="editfield" id="admincontent">
</div>

<div class="editfield" id="outputDiv">
</div>

</body>

</html>
