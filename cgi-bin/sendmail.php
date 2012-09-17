<?php
$to = 'mail@rocknroll-magazin.de';
$subject = 'Webmail form submit';
$message = $_POST['realname']." hat eine Nachricht geschrieben.\n\n";
$message .= "E-mail: ".$_POST['email']."\n\n";
$message .= "Nachricht: ".$_POST['message'];

$success = mail($to, $subject, $message);
if($success)
	echo "Ihre Nachricht wurde erfolgreich abgeschickt.";
else
	echo "Es gab einen Fehler waehrend der Bearbeitung ihrer Nachricht. Bitte schreiben Sie uns direkt an <a href=\"mailto:mail@rocknroll-magazin.de\">mail@rocknroll-magazin.de</a>.";
?>