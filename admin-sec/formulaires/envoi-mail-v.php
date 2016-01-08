<?php
	$headers ='From: "Association negaWatt"<contact@negawatt.org>'."\n";
    $headers.='Reply-To: contact@negawatt.org'."\n";
	$headers.='Content-Type: text/html; charset="iso-8859-1"'."\n";
    $headers.='Content-Transfer-Encoding: 8bit';

foreach ($tab_email_envoi as $email){
if (mail($email, $mail_titre, $mail_corps, $headers)) $tab_alerte[]='<div class="alerte_valide">envoi à '.$email.' réussi</div>'; else $tab_alerte[]='<div class="alerte_erreur">envoi à '.$email.' raté</div>';
}
?>