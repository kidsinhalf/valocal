<?php
//print_r($_POST);
$action=$_POST['action'];
$table=$_POST['table'];
$debug=true;;
//var_dump($_POST);

$sql_update='UPDATE '.$table.' SET ';
$sql_insert='INSERT INTO '.$table.' ';


//on parcourt les colonnes de la table (cf recup-get)
$donnees_champs=db_select('SHOW COLUMNS FROM '.$table);
foreach ($donnees_champs as $donnees_champ){
	$nom_colonne=$donnees_champ['Field'];
	switch ($nom_colonne){
			case 'id':
			break;
			
			default:
			$tab_valeurs[$nom_colonne]=$_POST[$nom_colonne];
			//date ?
			if (strpos($nom_colonne, 'date')!==false){
				$tab_valeurs[$nom_colonne]=date_fr2us($_POST[$nom_colonne]);
				}
			break;
			}
		}

		
if ($action=="modifier"){
	$tab_where=array();
	foreach ($tab_champs_primaires as $nom_cles_primaires){
	$tab_where[$nom_cles_primaires]=$_POST[$nom_cles_primaires];
	
	}
	
	$id_en_cours=$_POST['id'];
	$msg=db_update($table, $tab_valeurs, $tab_where, $debug);
	}
elseif ($action=="nouveau"){
	$id_en_cours=db_insert($table, $tab_valeurs, $debug);
	if ($id_en_cours>0) $msg=1; else $msg=-1;
	}


switch ($msg){
	case -1:
		switch($action){
			case 'nouveau': $tab_alerte[]='<div data-alert class="alert-box alert radius">Insertion ratée</div>';break;
			case 'modifier': $tab_alerte[]='<div data-alert class="alert-box alert radius">Modification ratée</div>';break;
			default: $tab_alerte[]='<div data-alert class="alert-box warning radius">Erreur action</div>';break;
			}
	break;
	
	case 0:
		switch($action){
			case 'nouveau': $tab_alerte[]='<div data-alert class="alert-box alert radius">Insertion nulle - debug necessaire</div>';break;
			case 'modifier': $tab_alerte[]='<div data-alert class="alert-box warning radius">Modification sans effet</div>';break;
			default: $tab_alerte[]='<div data-alert class="alert-box warning radius">Erreur action</div>';break;
			}
	break;
	
	case 1:
		switch($action){
			case 'nouveau': $tab_alerte[]='<div data-alert class="alert-box success radius">Insertion réussie</div>';break;
			case 'modifier': $tab_alerte[]='<div class="alert-box success radius">Modification réussie</div>';break;
			default: $tab_alerte[]='<div data-alert class="alert-box warning radius">Erreur action</div>';break;
			}
	break;
	
	}

	/*
if ($msg==1 AND $action!='nouveau') {$tab_alerte[]='<div data-alert class="alert-box success radius">Modification validée</div>';}
if ($id_en_cours>0 AND $action=='nouveau') {$tab_alerte[]='<div data-alert class="alert-box success radius">Insertion réussie : nouvel id '.$id_en_cours.'</div>';}
if ($msg==-1) {$tab_alerte[]='<div data-alert class="alert-box alert radius">Modification/Insertion ratée</div>';}
*/

//special recus et factures
if ($table=='achats' OR $table=='ventes'){
	
	switch ($table){
		case 'achats':
		$tab_alerte[]='<div class="alert_lien"><a target="blank" href="file-gen.php?type=recu&id='.$id_en_cours.'">Reçu disponible</a></div>';
		break;
		
		
		case 'ventes':
		$tab_alerte[]='<div data-alert class="alert_lien"><a target="blank" href="file-gen.php?type=facture&id='.$id_en_cours.'">Facture disponible</a></div>';
		break;
		
		}
	
	}

/*
//if (mysql_affected_rows() AND $table=='actualites') include 'cache_generator/actualites.php';


if (mysql_affected_rows() AND $table=='adhesions'){
	$reponse_special=mysql_query('SELECT MAX(annee) AS annee_derniere_adh FROM adhesions WHERE valide=1 AND id_adherent='.$_POST['id_adherent']);
	$donnees_special=mysql_fetch_assoc($reponse_special);
	mysql_query('UPDATE adherents SET annee_derniere_adhesion='.$donnees_special['annee_derniere_adh'].' WHERE id='.$_POST['id_adherent']);
	if (mysql_affected_rows()) $tab_alerte[]='<div data-alert class="alert-box success radius">Mise à jour du champ <em>annee_derniere_adhesion</em> pour l\'adhérent concerné</div>';
	}
	
	
	
if (isset($_POST['sp_table'])){
	$sp_table=$_POST['sp_table'];
	$sp_id=$_POST['sp_id'];
	switch ($sp_table){
		case 'recettes':
		if ($_POST['sp_id']>0) $sql_sp='UPDATE '.$sp_table.' SET bordereau="'.$_POST['sp_bordereau'].'", date="'.$_POST['sp_date'].'", mode_paiement="'.$_POST['sp_mode_paiement'].'" WHERE id='.$sp_id;
		else $sql_sp='INSERT INTO '.$sp_table.' (id_cotisations, date, montant, bordereau, mode_paiement) VALUES ("'.$id_en_cours.'", "'.$_POST['sp_date'].'", "'.$_POST['montant'].'", "'.$_POST['sp_bordereau'].'", "'.$_POST['sp_mode_paiement'].'")';
		
		break;
		
		
		}
		
	mysql_query($sql_sp);
	if (mysql_affected_rows()) $tab_alerte[]='<div data-alert class="alert-box success radius">opération sp OK '.$sql_sp.'</div>';
	else $tab_alerte[]='<div data-alert class="alert-box success radius">opération sp ERREUR '.$sql_sp.'</div>';
	}	
*/
//echo '<div>'.$sql_final.'</div>';
?>