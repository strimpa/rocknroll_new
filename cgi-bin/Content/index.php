<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<title>Adminbereich</title>
<link href="../../mainStyles.css" rel="stylesheet" type="text/css" />
<link href="../../adminStyles.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="../../script/jquery-1.5.1.min.js"></script>
<script type="text/javascript" src="adminApp.js"></script>
</head>

<body> <!--  onload="adminApp.Init();" -->

<div class="editfield" id="choice">
	<fieldset class="LeftGroupBox">
		<legend>Artikel auswaehlen:</legend>
		<select id="pagesDropDown" class="fullSizeControl">
			<option></option>
		</select>
	</fieldset>
	<fieldset class="RightGroupBox">
		<legend>Neu erstellen:</legend>
		<input id="createNameTf" class="fullSizeControl" type="text" />
		<input id="createButton" type="button" value="create" />
	</fieldset>
</div>
<div class="editfield" id="meta">
	<fieldset class="LeftGroupBox">
		<legend>Submenu Eintraege:</legend>
		<div id="submenuEntries" class="listBox" style="width:150px; height:110px"></div>
	</fieldset>
	<fieldset class="RightGroupBox">
		<legend>Neu erstellen:</legend>
		<input name="Text1" class="fullSizeControl" type="text" />
		<input id="createButton" type="button" value="create" />
	</fieldset>
	<fieldset class="RightGroupBox" style="top:60px;">
		<legend>Absatz-Anker Link:</legend>
		<select id="paragraphDropDown" class="fullSizeControl">
			<option></option>
		</select>
	</fieldset>
</div>
<div class="editfield" id="admincontent">
</div>

<div class="editfield" id="outputDiv">
</div>

</body>

</html>
