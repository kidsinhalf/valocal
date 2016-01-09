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
function db_select($sql, $tab_valeurs='', $debug=false, $optnum=''){
	global $bdd;
	if (empty($tab_valeurs)){
		$req = $bdd->query($sql);
		}
	else{
		//$req = $bdd->prepare('SELECT titre, auteur FROM actualites WHERE visible = ?  AND date <= ? ORDER BY date');
		$req = $bdd->prepare($sql);
		$req->execute($tab_valeurs);
		}
	if ($optnum=='num') $donnees = $req->fetchAll(PDO::FETCH_NUM);//tableau associatif
	else $donnees = $req->fetchAll(PDO::FETCH_ASSOC);//tableau iteratif
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
		

		
		
//DATE
function convertir_prix($prix){
	return str_replace('.',',',$prix);
	}
	
function convertir_date($date,$afficher_annee=0){//option=1 on garde l'année
list($comm_an, $comm_mo, $comm_jo) = explode('-', $date);
if ($comm_jo<10) {
	if ($comm_jo=="01") $comm_jo="1er";
	else ($comm_jo=$comm_jo[1]);
	}
if ($comm_mo == "1") $comm_mo="janvier";
if ($comm_mo == "2") $comm_mo="février";
if ($comm_mo == "3") $comm_mo="mars";
if ($comm_mo == "4") $comm_mo="avril";
if ($comm_mo == "5") $comm_mo="mai";
if ($comm_mo == "6") $comm_mo="juin";
if ($comm_mo == "7") $comm_mo="juillet";
if ($comm_mo == "8") $comm_mo="août";
if ($comm_mo == "9") $comm_mo="sept";
if ($comm_mo == "10") $comm_mo="octobre";
if ($comm_mo == "11") $comm_mo="novembre";
if ($comm_mo == "12") $comm_mo="décembre";
if ($afficher_annee) return $comm_jo.' '.$comm_mo.' '.substr($comm_an,2,4);
else return $comm_jo.' '.$comm_mo.' '.$comm_an;
}

function convertir_2_dates($date1,$date2,$option_slash=true,$option_txt=0){
if ($date1=="0000-00-00" OR $date2=="0000-00-00"){
	if ($option_txt==0) return 'date à préciser';
	else return 'nous contacter';
}
else{
if ($date1==$date2 OR ($date2=='')) return 'le '.convertir_date($date1);
else {

	list($comm_an1, $comm_mo1, $comm_jo1) = explode('-', $date1);
	list($comm_an2, $comm_mo2, $comm_jo2) = explode('-', $date2);

	if ($comm_an1!=$comm_an2) return 'du '.convertir_date($date1).' au '.convertir_date($date2);
		else{
		if ($comm_mo1==$comm_mo2) {
			if ($comm_jo1<10) {
				if ($comm_jo1=="01") $comm_jo1="1er";
				else ($comm_jo1=$comm_jo1[1]);
				}
			if ($option_slash) return ''.$comm_jo1.'/'.convertir_date($date2); else return 'du '.$comm_jo1.' au '.convertir_date($date2);
			}
		else{
			if ($option_slash) return ''.convertir_date($date1,0).'/'.convertir_date($date2); else return 'du '.convertir_date($date1,0).' au '.convertir_date($date2);
			}
		}
	}
}
}

function date_fr2us($date){
	list($day, $month, $year) = explode("/", $date);
	return ($year.'-'.$month.'-'.$day);
	}
function date_us2fr($date){
	list($year, $month, $day) = explode("-", $date);
	return ($day.'/'.$month.'/'.$year);
	}

function format_date($date, $format) {
	list($year, $month, $day) = explode("-", $date);
	if(!strcmp($format,"fr")){$lastmodifed = "$day/$month/$year";}
	else {$lastmodifed = "$year/$month/$day";}
	return $lastmodifed;
}


//MAIL
function mail_fake($a,$b,$c,$d) {
return true;
}


//PDF
function generer_recu($id_achats){
	include '';
	
	$num_recu=1;
	return $num_recu;
	}

function generer_facture($id_ventes){
	$num_facture=1;
	return $num_facture;
	}
	
?>