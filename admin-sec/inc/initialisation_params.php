<?php
$tab_cuivres_achat=array();
$tab_cuivres_vente=array();

$quick=db_select('SELECT titre, pourcentage_interne FROM parametres WHERE categorie=?', array('categorie_ventes'));
foreach($quick as $res){
	$tab_cuivres_vente[$res['titre']]=$res['pourcentage_interne'];
	}
	
$quick=db_select('SELECT titre, pourcentage_interne FROM parametres WHERE categorie=?', array('categorie_achats'));
foreach($quick as $res){
	$tab_cuivres_achat[$res['titre']]=$res['pourcentage_interne'];
	}
?>