<?php
	global $suggestionErrors;
	//$suggestionErrors = array();
	require_once("Content/ContentAccess.php"); 
	require_once('recaptchalib.php');

	if(count($suggestionErrors)>0)
	{
		print "Bitte f&uuml;llen Sie alle mit Sternchen versehenden Felder aus, oder &uuml;berpr&uuml;fen Sie das Captcha Feld:";
		print "<ul class='errorText'>";
		foreach ($suggestionErrors as $error => $msg) {
			print "<li>$error: $msg</li>";
		}
		print "</ul>";
	}
	else if(count($_POST))
		print "Ihr Vorschlag wurde eingef&uuml;gt. Unsere Administratoren werden ihn so bald es geht best&auml;tigen. Vielen Dank!";
	
	$actionString = "Popup.php";
	if(count($_GET)>0)
	{
		$actionString .= "?";
		foreach ($_GET as $key => $value) {
			$actionString .= "$key=$value&";
		}
	} 
?>

<form action="<?php $actionString?>" method="post">
<input type="hidden" name="suggestion" />
<?php 
	if(!isset($_GET) || !array_key_exists('vorschlag', $_GET))
		print "<p class='errorText'>Es wurde keine Variable den Vorschlagstyp angegeben!</p>";
	else {
		switch ($_GET['vorschlag']) {
			case 'link':
				require_once("ContentForms/Link.php");
				break;
			case 'termin':
				require_once("ContentForms/termin.php");
				break;
			default:
				print "<p class='errorText'>Es wurde keine guuml;ltige Variable den Vorschlagstyp angegeben: ".$_GET['vorschlag']."</p>";
				break;
		}
	}

	$publickey = "6LfSqN4SAAAAAKrBIxoX0NnPr1OT-jcEw-QIMmSx";
	echo recaptcha_get_html($publickey);
?>

<input type="submit" />

</form>