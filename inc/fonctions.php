<?php
//BASE DE DONNEES
	//INSERTION
function db_insert($table, $tab_valeurs, $debug=false){
	global $bdd;
	$tab_champs=array_keys($tab_valeurs);
	
	$sql = 'INSERT INTO '.$table.' ('.implode(', ', $tab_champs).') VALUES (:'.implode(', :', $tab_champs).')';
	$req = $bdd->prepare($sql);
	if ($req->execute($tab_valeurs)){
		$last_id = $bdd->lastInsertId();
		}
	else $last_id=-1;
	
	if ($debug) echo '<pre><code>'.$sql.'</code></pre>';
	
	return $last_id;
	}

	//UPDATE	
function db_update($table, $tab_valeurs, $tab_where, $debug=false){
	global $bdd;
	
	$sql = 'UPDATE '.$table.' SET ';
	
	$delim='';
	foreach ($tab_valeurs as $col=>$val){	
			$sql.=$delim.' '.$col.' = :'.$col;
			$delim=',';
			}
	
	if (!empty($tab_where)){
		$delim=' WHERE';
		foreach ($tab_where as $col=>$val){
			$sql.=$delim.' '.$col.' = :'.$col.' ';
			$delim='AND';
		}
	}
	else{
		echo 'Requete sans clause where impossible';
		}
		
	if ($debug) echo '<pre><code>'.$sql.'</code></pre>';
	
	$req = $bdd->prepare($sql);
	$tab_merged=array_merge($tab_valeurs, $tab_where);
	if ($req->execute($tab_merged)){
		$affected_rows = $req->rowCount();
		}
	else $affected_rows=-1;
	return $affected_rows;
	
	}
	
	//DELETE
function db_delete($table, $tab_where, $debug=false){
	global $bdd;
	
	$sql = 'DELETE FROM '.$table;
	
	if (!empty($tab_where)){
		$delim=' WHERE';
		foreach ($tab_where as $col=>$val){
		$sql.=$delim.' '.$col.' = :'.$col.' ';
		$delim='AND';
		}
		
	}
	else{
		echo 'Requete sans clause where impossible';
		}
		
	if ($debug) echo '<pre><code>'.$sql.'</code></pre>';
	
	$req = $bdd->prepare($sql);
	if ($req->execute($tab_where)){
		$affected_rows = $req->rowCount();
		}
	else $affected_rows=-1;
	return $affected_rows;
	
	}

	//SELECT
function db_select($sql, $tab_valeurs='', $debug=false){
	global $bdd;
	if (empty($tab_valeurs)){
		$req = $bdd->query($sql);
		}
	else{
		//$req = $bdd->prepare('SELECT titre, auteur FROM actualites WHERE visible = ?  AND date <= ? ORDER BY date');
		$req = $bdd->prepare($sql);
		$req->execute($tab_valeurs);
		}
	$donnees = $req->fetchAll(PDO::FETCH_ASSOC);//tableau associatif
	$req->closeCursor(); 
	
	return $donnees;
	}

	//SHOW
function db_show_on_table($donnees, $tab_th){
	echo '<table>';
	foreach ($tab_th as $titre=>$cols){
		echo '
		<th>'.$titre.'</th>';
		}
	foreach ($donnees as $tab_value){
		echo '
			<tr>
			';
			foreach ($tab_th as $cols){
				echo '<td>'.$tab_value[$cols].'</td>
				';
				}
		echo '
		</tr>
		';
		}
	echo '</table>';
	}

	
//FORMULAIRES
		

?>