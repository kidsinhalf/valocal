<?php
$table=$_GET['table'];

$sql_delete="DELETE FROM $table ";


//récupération des noms et types de champs
$reponse_champ=mysql_query("SHOW COLUMNS FROM $table");
while ($donnees_champ=mysql_fetch_array($reponse_champ)){
if ($donnees_champ[3]=='PRI') $tab_champs_primaires[]=$donnees_champ[0];
}


foreach ($tab_champs_primaires as $nom_cles_primaires){
	$sql_post_cles_primaires[]=$nom_cles_primaires.'="'.$_GET[$nom_cles_primaires].'"';
	}
	$sql_delete.=' WHERE '.implode(' AND ',$sql_post_cles_primaires);

mysql_query($sql_delete);

if (mysql_affected_rows()) $tab_alerte[]=alerte('valide',mysql_affected_rows().' élément(s) supprimé(s)');
if (!mysql_affected_rows()) $tab_alerte[]=alerte('erreur','aucun élément supprimé');
?>