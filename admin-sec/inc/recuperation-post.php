<?php

if (isset($_POST['action'])){
	switch ($_POST['action']){
		case 'nouveau':include 'formulaires/edit-table-v.php';
		break;
		case 'modifier':include 'formulaires/edit-table-v.php';
		break;
		case 'importer':include 'formulaires/import-table-v.php';
		break;
		case 'supprimer ces ids':include 'formulaires/delete-ids-v.php';
		break;
		case 'action sur ids':include 'formulaires/action-ids-v.php';
		break;
		
	}
}


/*
//Autres actions possibles [not featured]
if (isset($_POST['requete_sql'])){
	$reponse=mysql_query(stripslashes($_POST['requete_sql']));
	$tab_alerte[]=alerte('valide', mysql_errno().': '.mysql_error().'<br />');
	}
	
	
if (isset($_POST['email_maise_en_attente'])){
	$expediteur='contact@latrace.net';
	$to=$_POST['envoi_mail_to'];
	$email_titre=$_POST['envoi_mail_titre'];
	$email_texte=$_POST['envoi_mail_texte'];
	$id_lettres_type=7;
	
	$headers ='From: "LA TRACE - Gite des Ecouges" <'.$expediteur.'>'."\n";
    $headers.='Reply-To: '.$expediteur.' '."\n";
	$headers.='Content-Type: text/html; charset="UTF-8"'."\n";
    $headers.='Content-Transfer-Encoding: 8bit';	
	
	if (mail($to,$email_titre,$email_texte,$headers)){
		$sql_email='INSERT INTO emails_envoyes (titre,destinataire,id_lettres_type, date_envoi, heure_envoi, texte) VALUES ("'.addslashes($email_titre).'","'.$to.'",'.$id_lettres_type.',"'.date('Y-m-d').'","'.date('H:i:s').'","'.addslashes($email_texte).'")';
		mysql_query($sql_email);
		$tab_alerte[]=alerte('valide','Email envoyé avec succès à '.$to);
		}
		else{
			$tab_alerte[]=alerte('erreur','ECHEC ENVOI MAIL');
		}
	
	}
*/
?>