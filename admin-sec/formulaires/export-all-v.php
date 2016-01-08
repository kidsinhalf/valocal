<?php
$file = 'base_Ecouges_';
$type=date("Y-m-d");
$filename = $file.'_'.$type;
$clause_where='';

require( "fonctions/zip.lib.php" ) ; // librairie ZIP
$zip = new zipfile () ; //on crée une instance zip

$tab_tables_interdites=array();

$reponse_temp=mysql_query('SHOW TABLE STATUS');
while ($donnees_temp=mysql_fetch_array($reponse_temp)){
	if (!in_array($donnees_temp[0],$tab_tables_interdites)){
		$table=$donnees_temp[0];
		$nom_fichier=$table.'.csv';
		ob_start();//on lance la mise en cache

		$result = mysql_query('SHOW COLUMNS FROM '.$table);
		$l = 0;
		if (mysql_num_rows($result) > 0) {
			while ($row = mysql_fetch_assoc($result)) {
			echo $row['Field']."; ";
			$l++;
			}
			}
			echo "\n";

		$values = mysql_query('SELECT * FROM '.$table.$clause_where);
		while ($rowr = mysql_fetch_row($values)) {
			for ($j=0;$j<$l;$j++) {
		$d=str_replace('"','""',$rowr[$j]);
echo '"'.str_replace('
','\n',$d).'";';
}
echo "\n";
}
 $zip->addfile(utf8_decode(ob_get_contents()), $nom_fichier) ; //on ajoute le fichier
 ob_end_clean();	//on vide le cache
}
}


    $archive = $zip->file() ; // on associe l'archive

    // on enregistre l'archive dans un fichier
    $open = fopen( 'exports/'.$filename.'.zip' , "wb");
    fwrite($open, $archive);
    fclose($open);
	

     // code à insérer à la place des 3lignes ( fopen, fwrite, fclose )
     header('Content-Type: application/x-zip') ; //on détermine les en-tête
     header('Content-Disposition: inline; filename='.$filename.'.zip') ;

     echo $archive ;

?>

