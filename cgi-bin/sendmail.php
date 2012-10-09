<?php
$to = 'mail@rocknroll-magazin.de';
$subject = 'Webmail form submit';
$message = $_POST['realname']." hat eine Nachricht geschrieben.\n\n";
$message .= "E-mail: ".$_POST['email']."\n\n";
$message .= "Nachricht\n\n: ".$_POST['message'];
$message .= "Bestellung: ".$_POST['postvars'];

$success = mail($to, $subject, $message);
if($success)
	echo "Ihre Nachricht wurde erfolgreich abgeschickt. Sie koennen dieses Fenster schliessen.";
else
	echo "Es gab einen Fehler waehrend der Bearbeitung ihrer Nachricht. Bitte schreiben Sie uns direkt an <a href=\"mailto:mail@rocknroll-magazin.de\">mail@rocknroll-magazin.de</a>.";
?>