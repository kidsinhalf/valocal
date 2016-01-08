<?php
$table=$_GET['table'];
$data_csv=file_get_contents($_FILES['fichier_csv']['tmp_name']);
$tab_lignes=explode('
',$data_csv);//on suppose que le séparateur de ligne est \n
$nb_produits=sizeof($tab_lignes)-2;
//echo $nb_produits;
$ligne_en_tete=$tab_lignes[0];
$tab_champs_en_tete=explode(';',$ligne_en_tete);
foreach ($tab_champs_en_tete as $champ_en_tete){
	$champ_en_tete=trim($champ_en_tete);
	if (!empty($champ_en_tete)) $tab_en_tete[]=$champ_en_tete;
	}
$pos_id=0;
if (in_array('id',$tab_en_tete)) $pos_id = array_search('id', $tab_en_tete);
//en tete pour insert SQL =>suppr id
unset($tab_en_tete[$pos_id]);

$champs_en_tete_insert=implode(',',$tab_en_tete);

$i_success=0;
for ($id=1;$id<=$nb_produits;$id++){
		$tab_champs_produit=explode(';',$tab_lignes[$id]);
		if (!empty($tab_champs_produit)){
		unset($tab_champs_produit[$pos_id]);
		
		$tab_champ_final=array();
		//en tete pour insert SQL
		unset($tab_champs_produit[$pos_id]);
		for ($j=1;$j<=sizeof($tab_en_tete);$j++) $tab_champ_final[]='"'.trim($tab_champs_produit[$j],'"').'"';

		$q='INSERT INTO '.$_GET['table'].' ('.$champs_en_tete_insert.') VALUES ('.implode(',',$tab_champ_final).')';
		echo '<div>'.$q.'</div>';
		//mysql_query($q);
		if (mysql_affected_rows()>0) $i_success++;
	}
}
$tab_alerte[]='<div class="alerte_valide">'.$i_success.' / '.$nb_produits.' importés</div>';
//fin export direct