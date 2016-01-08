<?php
session_start();
include '../inc/baseconnect.php';
include '../inc/fonctions.php';
$nonutf8=true;//NSP
switch($_GET['type']){
	//espace adhérent
	case 'facture':
	include '../inc/tcpdf6/facture.php';
	break;
	case 'recu':
	include '../inc/tcpdf6/recu.php';
	break;
	default:break;
	}
mysql_close();
?>