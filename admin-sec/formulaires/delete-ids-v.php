<?php
$query_delete='DELETE FROM '.$_GET['table'].' WHERE id IN ('.implode(',',$_POST['id_suppr']).')';
//echo $query_delete;
mysql_query($query_delete);
$tab_alerte[]='<div class="alerte_valide">'.mysql_affected_rows().' élements supprimés (doublons)</div>';
?>