<?php
$donnees=db_select('SELECT * FROM cours WHERE date= ? ', array(date('Y-m-d')), true);
//var_dump($donnees);
if (empty($donnees)){
	
	

//mise à jour du cours du cuivre
$tab_url=array();
$tab_regex=array();

//$tab_url[]='http://www.lme.com/metals/non-ferrous/copper/';
//$tab_regex[]='<td>Cash Buyer</td>\s<td>(.*)</td>';

//$tab_url[]='http://www.infomine.com/investment/metal-prices/copper/';
$tab_url[]='blocs/exemple.txt';
$tab_regex[]='\| ([0123456789 .,]*) EUR/t'; //| 4,310.75 EUR/t)





$j=0;
foreach ($tab_url as $url){
	//echo '<div>'.$url.'</div>';
	$content=file_get_contents($url);
	//$content=strip_tags($content);
	$regexp = $tab_regex[$j];
	//echo '<div>regex : '.$regexp.'</div>';
	if (preg_match_all("#$regexp#", $content, $matches)) {
		$value=$matches[1][0];
		//echo ($value);//format americain
		$cours_cuivre=str_replace(',','', $value);
		//echo $cours_cuivre;
		}
	$j++;
	}

$tab_cours=array('date'=>date('Y-m-d'), 'valeur_euro_tonne'=>$cours_cuivre);
$id_cours=db_insert('cours', $tab_cours);
$tab_alerte[]='<div data-alert class="alert-box success radius">Cours du jour inséré (n° '.$id_cours.')</div>';
}
else{
	$donnee=$donnees[0];
	$tab_alerte[]='<div data-alert class="alert-box alert radius">Cours du jour n°'.$donnee['id']. ' : '.$donnee['valeur_euro_tonne'].' €/tonne</div>';
	}

?>