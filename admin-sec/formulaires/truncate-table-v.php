<?php
$table=$_GET['table'];

$sql_truncate="TRUNCATE $table ";
mysql_query($sql_truncate);

$tab_alerte[]=alerte('valide','table vidée');

?>