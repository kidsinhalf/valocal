<?php
$tab_personnes=array();
$tabl_activites=array();
$tabl_prestations=array();


//stockage id_personnes =>nom prenom
$sql_start='SELECT id, nom, prenom FROM personnes';
$reponse_start=mysql_query($sql_start);
while ($donnees_start=mysql_fetch_assoc($reponse_start)){
	$tab_personnes[$donnees_start['id']]=array('nom'=>$donnees_start['nom'], 'prenom'=>$donnees_start['prenom']);
	}


//stockage id_personnes =>nom prenom
$sql_start='SELECT id, titre, date_debut FROM activites';
$reponse_start=mysql_query($sql_start);
while ($donnees_start=mysql_fetch_assoc($reponse_start)){
	$tabl_activites[$donnees_start['id']]=$donnees_start['titre'].' '.formateDate($donnees_start['date_debut']);
	}
$sql_start='SELECT id, titre, date_debut FROM prestations';
$reponse_start=mysql_query($sql_start);
while ($donnees_start=mysql_fetch_assoc($reponse_start)){
	$tabl_prestations[$donnees_start['id']]=$donnees_start['titre'].' '.formateDate($donnees_start['date_debut']);
	}
$sql_start='SELECT id, id_personnes, categorie FROM cotisations';
$reponse_start=mysql_query($sql_start);
while ($donnees_start=mysql_fetch_assoc($reponse_start)){
	$tabl_cotisations[$donnees_start['id']]=$tab_personnes[$donnees_start['id_personnes']];
	$tabl_cotisations[$donnees_start['id']]['categorie']=$donnees_start['categorie'];
	}


$file = 'export';
$type=date('Ymd');;
$separateur='|';
	
		$table=$_GET['table'];
		$clause_where='';
		$tab_clause_where=array();
		if (isset($_GET['segment1']) AND !empty($_GET['segment1'])){
			if ($_GET['segment1']=='date') $tab_clause_where[]=' '.$_GET['segment1'].' LIKE "%'.$_GET['filtre1'].'%"';
			else $tab_clause_where[]=' '.$_GET['segment1'].'="'.$_GET['filtre1'].'"';
			$type.=$_GET['segment1'].'_'.$_GET['filtre1'];
			}
			
		if (isset($_GET['segment2']) AND !empty($_GET['segment2'])){
			$type.=$_GET['segment2'].'_'.$_GET['filtre2'];
			$tab_clause_where[]=' '.$_GET['segment2'].'="'.$_GET['filtre2'].'"';
			}
		
		if (!empty($tab_clause_where)) $clause_where=' WHERE '.implode(' AND ',$tab_clause_where);
		
		$filename = $table.'_'.$type.'.csv';
		
		$ecriture = fopen ('exports/'.$filename, 'w+') or die ('Impossible d ouvrir '.$filename.' en ecriture');
		ob_start();//on lance la mise en cache
		


//export direct
//header("Content-type: application/vnd.ms-excel");
//header("Content-disposition: csv" . date("Y-m-d") . ".csv");
//header( "Content-disposition: filename=".$filename);
$tab_fields=array();
$left_join='';

		$result = mysql_query('SHOW COLUMNS FROM '.$table);
		$i = 0;
		if (mysql_num_rows($result) > 0) {
			

			
			while ($row = mysql_fetch_assoc($result)) {
			echo '"'.$row['Field'].'"'.$separateur;
			$tab_fields[]=$table.'.'.$row['Field'];
			$i++;
			}
			}
			echo "\n";

			$sql_final='SELECT '.implode(',',$tab_fields).' FROM '.$table.$left_join.$clause_where;
			//echo $sql_final;
		$values = mysql_query($sql_final);
		while ($rowr = mysql_fetch_array($values)) {
		if ($table=='logs_cb'){
			if ($rowr['categorie_commande']=='cotisations'){
			echo $tabl_cotisations[$rowr['id_commande']]['nom'].$separateur.$tabl_cotisations[$rowr['id_commande']]['nom'].$separateur;
			echo $tabl_cotisations[$rowr['id_commande']]['categorie'].$separateur.$separateur;
			}
			
		else{
			$reponse_temp=mysql_query('SELECT id_personnes, id_activites, id_prestations FROM inscriptions WHERE id='.$rowr['id_commande']);
			$donnees_temp=mysql_fetch_assoc($reponse_temp);
		
			if (isset($tab_personnes[$donnees_temp['id_personnes']])){
				echo $tab_personnes[$donnees_temp['id_personnes']]['nom'].$separateur.$tab_personnes[$donnees_temp['id_personnes']]['prenom'].$separateur;
				}
			else echo 'N/A'.$separateur.'N/A'.$separateur;
			if (isset($tabl_activites[$donnees_temp['id_activites']])) echo $tabl_activites[$donnees_temp['id_activites']].$separateur; else echo 'N/A'.$separateur;
			if (isset($tabl_prestations[$donnees_temp['id_prestations']])) echo $tabl_prestations[$donnees_temp['id_prestations']].$separateur;  else echo 'N/A'.$separateur;
			//adhesions
			//if (isset($tabl_prestations[$donnees_temp['id_prestations']])) echo $tabl_prestations[$donnees_temp['id_prestations']].$separateur;  else echo 'N/A'.$separateur;
			}
		}
		
			for ($j=0;$j<$i;$j++) {
		$d=str_replace('"','""',$rowr[$j]);
echo '"'.str_replace('
','\n',$d).'"'.$separateur;
}
echo "\n";
}

fwrite ($ecriture, utf8_decode(ob_get_contents()));
fclose ($ecriture);
ob_end_clean();	//on vide le cache


//fin export direct
echo '<div><a href="exports/'.$filename.'">'.$filename.'</a></div>';
?>