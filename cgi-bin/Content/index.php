<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<title>Adminbereich</title>
<link href="../../adminStyles.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="../../script/jquery-1.5.1.min.js"></script>
<script type="text/javascript" src="adminApp.js"></script>
</head>

<body onload="adminApp.Init();">

<div class="editfield" id="choice" style="left: 112px; top: 0px; height: 45px">
	<form method="post">
		<fieldset name="pagesDropDown" style="width: 171px">
			<legend>Select page:</legend>
			<select name="Select1" style="width: 156px">
			<option></option>
			</select>
		</fieldset>
		<fieldset name="pagesCreate" style="position:absolute; left:200px; top:5px; width: 397px;">
			<legend>Create new:</legend>
			<input name="Text1" style="width: 165px" type="text" /><input name="Submit1" type="submit" value="submit" />
		</fieldset>
	</form>
</div>
<div class="editfield" id="meta">
</div>
<div class="editfield" id="content">
</div>

<div class="editfield" id="outputDiv">
</div>

</body>

</html>
