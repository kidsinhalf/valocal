<?php

//INSERT
$tab_client=array('nom'=>'Rabourdin', 'prenom'=>'Baptiste');
$id_client=db_insert('clients', $tab_client, $debug);
echo 'Client inséré '.$id_client;

//UPDATE
$tab_client=array('nom'=>'Rabourdin1', 'prenom'=>'Baptiste1');
$affected_rows=db_update('clients', $tab_client, array('id'=>1), $debug);

//DELETE
$affected_rows=db_delete('clients', array('nom'=>'Rabourdin'), $debug);
echo $affected_rows;

//SELECT
$clients=db_select('SELECT * FROM clients');
foreach($clients as $client){
	echo '<div>'.$client['nom'].' '.$client['prenom'].' '.$client['id'].'</div>';
	}

//SHOW
db_show_on_table($clients);

?>