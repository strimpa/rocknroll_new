<?php
$to = 'mail@rocknroll-magazin.de';

$subject = 'Webmail form submit';
$message = $_POST['realname']." hat eine Nachricht geschrieben.\n\n";
$message .= "E-mail: ".$_POST['email']."\n\n";
$message .= "Nachricht\n\n: ".$_POST['message'];
$message .= "Bestellung: ".$_POST['postvars'];

// To send HTML mail, the Content-type header must be set
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

// Additional headers
$headers .= 'From: R&R website mail script <mail@rocknroll-magazin.de>' . "\r\n";

if(isset($_POST['email']) && $_POST['email']!="")
{
	$success = mail($to, $subject, $message, $headers);
	if($success)
		echo "Ihre Nachricht wurde erfolgreich abgeschickt. Sie koennen dieses Fenster schliessen.";
	else
		echo "Es gab einen Fehler waehrend der Bearbeitung ihrer Nachricht. Bitte schreiben Sie uns direkt an <a href=\"mailto:mail@rocknroll-magazin.de\">mail@rocknroll-magazin.de</a>.";
}
else
	echo "Bitte geben Sie Ihre email Adresse an oder schreiben Sie uns direkt an <a href=\"mailto:mail@rocknroll-magazin.de\">mail@rocknroll-magazin.de</a>.";
?>