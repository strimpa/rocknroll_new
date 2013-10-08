<?php
	require_once('../Aufenthalt.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<title><?php echo lang("UI_APP_TITLE"); ?></title>
<link href="css/adminStyles.css" rel="stylesheet" type="text/css" />
<link href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />

<script data-main="scripts/main" src="scripts/require.js"></script>
</head>

<body>
<div class="editfield" id="settings">
	<H3><?php echo lang("UI_SETTINGS"); ?></H3>
	<?php echo lang("UI_NUM_TABLEROWS"); ?><input id="spinNumTableCols" value="5"/>
</div>
<div class="editfield" id="choice">
	<H3><?php echo lang("UI_CHOOSE_ARTICLE"); ?></H3>
	<fieldset class="RightGroupBox">
		<legend><?php echo lang("UI_EDIT"); ?></legend>
		<input id="createPageButton" class="editButton" type="image" title="Neu" src="images/022 Document Add.png" />
		<input id="editPageButton" class="editButton" type="image" title="Bearbeiten" src="images/081 Pen.png" />
		<input id="deletePageButton" class="deleteButton" type="image" title="Loeschen" src="images/023 Document Delete.png" />
	</fieldset>
	<select id="pagesDropDown" class="fullSizeControl">
		<option></option>
	</select>
	<input id="refreshButton" class="editButton" type="image" title="Refresh" src="images/061 Sync.png" />
	<table>
		<tr>
			<td><?php echo lang("UI_NAME_MAINMENU"); ?></td>
			<td><input id="pageTitle" class="fullSizeControl" type="text" value="" readonly="true" /></td>
		</tr>
		<tr>
			<td><?php echo lang("UI_MENU_PRIORITY"); ?></td>
			<td><input id="menuPriority" class="shortControl" type="text" value="" readonly="true" /></td>
		</tr>
		<tr>
			<td><?php echo lang("UI_PLUGIN"); ?></td>
			<td>
				<select id="pluginDropDown" class="fullSizeControl">
					<option></option>
				</select>
			</td>
		</tr>
	</table>
</div>
<div class="editfield" id="meta">
	<H3><?php echo lang("UI_SUBMENU_ENTRIES"); ?></H3>
	<fieldset class="RightGroupBox">
		<legend><?php echo lang("UI_EDIT"); ?></legend>
		<input id="createMenuEntryButton" class="editButton" type="image" title="Neu" src="images/022 Document Add.png" />
		<input id="editMenuEntryButton" class="editButton" type="image" title="Bearbeiten" src="images/081 Pen.png" />
		<input id="deleteMenuEntryButton" class="deleteButton" type="image" title="Loeschen" src="images/023 Document Delete.png" />
	</fieldset>
	<div id="submenuEntries" class="listBox" style="width:150px; height:110px"></div>
	<input id="upMenuItemButton" class="editButton" type="image" title="Eintrag nach oben bewegen" src="images/037 ArrowUp.png" /><br />
	<input id="downMenuItemButton" class="editButton" type="image" title="Eintrag nach unten bewegen" src="images/038 ArrowDown.png" />
	<br style="clear:both;">
</div>

<div class="editfield" id="contentHolder">
	<H3><?php echo lang("UI_CONTENT_HEADER"); ?></H3>
	<div id="contentCreate">
		<fieldset class="ThinGroupBox">
			<legend><?php echo lang("UI_EDIT"); ?></legend>
			<input id="createParagraphButton" class="editButton" type="image" title="Neuen Absatz erstellen" src="images/022 Document Add.png" />
			<?php echo lang("UI_INSERT_EXISTING_PARA"); ?><select id="insertParagraphSelect" class="fullSizeControl">
					<option></option>
			</select>
		</fieldset>
		<fieldset class="ThinGroupBox">
			<legend><?php echo lang("UI_PARSE_ERRORS"); ?>:</legend>
			<input type="button" id="errorOutputDelete" value="Loeschen" />
			<div id="errorOutput"></div>
		</fieldset>
	</div>
	<div id="admincontent">
	</div>
</div>

</body>

</html>
