<?php
$db_host='localhost';
$db_name='valocal';
$db_charset='utf8';
$db_user='root';
$db_pass='';


try
{
	// On se connecte � MySQL
	$bdd = new PDO('mysql:host='.$db_host.';dbname='.$db_name.';charset='.$db_charset.'', $db_user, $db_pass);
}
catch(Exception $e)
{
	// En cas d'erreur, on affiche un message et on arr�te tout
        die('Erreur Connexion: '.$e->getMessage());
}
?>