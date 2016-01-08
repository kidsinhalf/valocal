<?php
switch ($_POST['type_action']){
	case 'supprimer';
	$query_action='DELETE FROM '.$_GET['table'].' WHERE id IN ('.implode(',',$_POST['id_actif']).')';break;
	
	case 'valider';
	$query_action='UPDATE '.$_GET['table'].' SET valide=1 WHERE id IN ('.implode(',',$_POST['id_actif']).')';break;
	
	case 'invalider';
	$query_action='UPDATE '.$_GET['table'].' SET valide=0 WHERE id IN ('.implode(',',$_POST['id_actif']).')';break;
	
	default:$query_action='';
	break;
	}

//echo $query_action;
mysql_query($query_action);
$tab_alerte[]='<div class="alerte_valide">'.mysql_affected_rows().' élements affectés </div>';
?>