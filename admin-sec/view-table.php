<?php
session_start();
include '../inc/baseconnect.php';
include '../fonctions/general.php';
$tab_alerte=array();
include 'inc/recuperation-get.php';
if (!empty($_POST)) include 'inc/recuperation-post.php';

/* Options fondamentales */
$table=$_GET['table'];
$nb_items_par_page=100;

/* Parametres GET de filtre */
$url_filtres='';
if (isset($_GET['segment1']) AND !empty($_GET['segment1'])) $url_filtres.='&segment1='.$_GET['segment1'].'&filtre1='.$_GET['filtre1'];
if (isset($_GET['segment2']) AND !empty($_GET['segment2'])) $url_filtres.='&segment2='.$_GET['segment2'].'&filtre2='.$_GET['filtre2'];
if (isset($_GET['page'])) $deb=($_GET['page']-1)*$nb_items_par_page; else $deb=0;

$tab_champs_primaires=array();
$reponse_champ=mysql_query("SHOW COLUMNS FROM $table");
while ($donnees_champ=mysql_fetch_array($reponse_champ)){
	//echo '<div>'.$donnees_champ[0].' '.$donnees_champ[1].' '.$donnees_champ[2].' '.$donnees_champ[3].'</div>';
	if ($donnees_champ[1]!='text') $tab_champs_nom[]=$donnees_champ[0];
	if ($donnees_champ[1]!='text') $tab_champs_type[]=$donnees_champ[1];
	if ($donnees_champ[3]=='PRI') $tab_champs_primaires[]=$donnees_champ[0];
	if (substr($donnees_champ[1],0,4)=='enum'){
		$tab_enum[$donnees_champ[0]]=explode("','",substr($donnees_champ[1],6,-2));
		}
}


?>
<?php include 'blocs/header-admin.php';?>  
      <div class="row">
        <div class="large-12 columns">
		<?php include 'blocs/menu-admin.php';?>  
          </div>
        </div>

        <div class="row">
          <div class="large-12 columns">
            <div class="row">

              <div class="large-8 columns">
                  <h1>Accueil</h1>
                <?php if (!empty($tab_alerte)) foreach ($tab_alerte as $alerte) echo $alerte; //LE tableau d'alertes?>
       
              </div>
			  
			  
              <div class="large-4 columns">
				<h2>Col Droite</h2>
			  </div>
			  
            </div>
          </div>
        </div>
<?php include 'blocs/footer-admin.php';?>




		<?php

?>

<?php


<?php
	echo '<form method="get" name="form_clause">
	<img src="css/items/sitemap-blue.gif" /> <strong>Filtres</strong> - ';
	foreach ($tab_enum as $enum_nom=>$enum){
		echo '.<strong>'.$enum_nom.'</strong> : <select name="'.$enum_nom.'">
					<option></option>';
					foreach ($enum as $enum_poss){
					if (isset($_GET[$nom_enum]) AND $_GET[$nom_enum]==$enum_poss) $selected=' selected="selected"'; else $selected='';
					echo '<option value="'.$enum_poss.'"'.$selected.'>'.$enum_poss.'</option>';
					}
					
					echo '</select>';
					}
					echo '
					<input type="hidden" name="table" value="'.$table.'">
					<input type="button" value="clause" onclick="select_clause_submit(\'form_clause\')" />
	</form>';
				
//fin enum
}

foreach ($tab_champs_nom as $champs_nom){
	$html_form_select.='<option value="'.$champs_nom.'">'.$champs_nom.'</option>';
	}

		$clause_where='';
		$tab_clause_where=array();
		if (isset($_GET['segment1']) AND !empty($_GET['segment1'])){
			if ($_GET['segment1']=='id_formation') $tab_clause_where[]=' '.$_GET['segment1'].' = "'.$_GET['filtre1'].'"';
			else $tab_clause_where[]=' '.$_GET['segment1'].' LIKE "%'.$_GET['filtre1'].'%"';
			}
		if (isset($_GET['segment2']) AND !empty($_GET['segment2'])){
		if ($_GET['segment2']=='id_formation') $tab_clause_where[]=' '.$_GET['segment2'].' = "'.$_GET['filtre2'].'"';
			else $tab_clause_where[]=' '.$_GET['segment2'].' LIKE "%'.$_GET['filtre2'].'%"';
			}
		
		
		$array_key_get=array_keys($_GET);
		if (!in_array('id',$array_key_get)){
		$array_clause=array_intersect($array_key_get,$tab_champs_nom);
		if (!empty($array_clause)){
			foreach ($array_clause as $clause) $tab_clause_where[]=' '.$clause.' = "'.$_GET[$clause].'"';
			}
		}
		
		if (!empty($tab_clause_where)) $clause_where=' WHERE '.implode(' AND ',$tab_clause_where);
		
		


$id_item=0;$i_couleur=0;

$query='SELECT '.$tab_champs_nom[0].' FROM '.$table.$clause_where;
$nb_items=mysql_num_rows(mysql_query($query));
$nb_pages=floor($nb_items/$nb_items_par_page)+1;
if ($nb_items>$nb_items_par_page){
if (!isset($_GET['page'])) $page=1; else $page=$_GET['page'];
	$page_dizaine=floor($page/$nb_items_par_page);
	$page_suivante=$page+1;
	$page_precedente=$page-1;
	$self=url_param_get($_GET, array('page','action','id'));
	
	echo '<div id="barre_pages">';
		if ($page>1){echo '<a href="'.$self.'&page='.($page_precedente).'"><<</a> ';}
		$i=0;
		while ($i<$nb_pages){
			$i++;
			if ($i%40==0) echo '<br />';
			//$i_dizaine=$i+$page_dizaine*100;
			if ($page==$i){echo '<strong>'.$i.'</strong> ';}
			else{echo '<a href="'.$self.'&page='.$i.'">'.$i.'</a> ';}
			}
		if ($page<$nb_pages){echo ' <a href="'.$self.'&page='.($page_suivante).'">>></a> ';}
	echo '</div>';
	}
	
	?>
	<div style="margin:6px auto; width:80%; font-size:11px; border:dotted 1px #aaaaaa;">
	<?php
	//filtres
	echo '
	<form method="get">
	<img src="css/items/sitemap-blue.gif" /> <strong>Filtres</strong> - 
	<input type="hidden" name="table" value="'.$table.'">
	filtre 1 : <select name="segment1"><option></option>'.$html_form_select.'</select> <input type="text" name="filtre1" value="" /> - 
	filtre 2 : <select name="segment2"><option></option>'.$html_form_select.'</select> <input type="text" name="filtre2" value="" /> - 
	<input type="submit" value="segment" />
	</form>
	';

		//import fichier
		if ($table=='base_email' OR $table=='inscriptions' OR $table=='recettes' OR $table=='emails_desinscriptions' OR $table=='emails_retourserreurs') echo '
		<form method="POST" enctype="multipart/form-data">
		<img src="css/items/upload-page-green.gif" /><strong> Importer</strong> un fichier csv : <input type="file" name="fichier_csv" />
		<input type="submit" name="action" value="importer" />
		</form>';
	?>
	</div>
	
	
	<form name="main_form" method="post">
	<?php

//LA TABLE !
echo '<table id="tabmain" class="data sortable">';
echo '<caption>'.$nb_items.' éléments trouvés</caption>';
echo '<tr>';
echo '<th>action</th>';
foreach ($tab_champs_nom as $champ_nom) echo '<th>'.$champ_nom.'</th>';
echo '</tr>';

$order='ORDER BY '.$tab_champs_nom[0].' DESC';
	
$query='SELECT '.implode(',',$tab_champs_nom).' FROM '.$table.$clause_where.' '.$order.' LIMIT '.$deb.', '.$nb_items_par_page;
//echo $query;
$reponse=mysql_query($query);
while ($donnees=mysql_fetch_array($reponse)){
$id_item++;
//les goutes te les couleurs
		switch ($i_couleur){
		case 0: $tr_couleur='#FBF2B7';$i_couleur=1;
		break;
		case 1: $tr_couleur='#ffffff';$i_couleur=0;
		break;
		}
		
	if (isset($_POST['id']) AND $_POST['id']==$donnees['id']) $tr_couleur='#aa66aa';
	if (isset($donnees['statut']) AND $donnees['statut']=='liste attente') $tr_couleur='#e0d5e3';
	if (isset($donnees['statut']) AND $donnees['statut']=='annulé') $tr_couleur='#f1e4e1';
	if (isset($donnees['statut']) AND $donnees['statut']=='absent') $tr_couleur='#c8eac8';
	if (isset($donnees['statut']) AND $donnees['statut']=='poubelle') $tr_couleur='#e5e6c0';
	
	echo '<tr style="background-color:'.$tr_couleur.'" id="ancre_'.$donnees['id'].'">';
	
	//boutons d'action
	$url_cles_primaires=array();
	
	foreach ($tab_champs_primaires as $nom_cles_primaires){
		$url_cles_primaires[]=$nom_cles_primaires.'='.$donnees[$nom_cles_primaires];
		}
	//print_r($url_cles_primaires);
		
	echo '<td>
			<input type="checkbox" name="id_actif[]" value="'.$donnees['id'].'" />
			<a href="edit-table.php?table='.$table.'&action=modifier&'.implode('&',$url_cles_primaires).'"><img src="css/items/edit-blue.gif" /></a>';
			if ($table!='inscriptions') echo '<a onclick="return confirm(\'Êtes-vous sûr de vouloir supprimer ?\');" href="view-table.php?action=supprimer&table='.$table.'&'.implode('&',$url_cles_primaires).'"><img src="css/items/delete-page-red.gif" /></a>
		</td>';
		
	
	for ($i_champ=0;$i_champ<sizeof($tab_champs_nom);$i_champ++) {

		if ($tab_champs_type[$i_champ] == 'text' OR $tab_champs_type[$i_champ] == 'mediumtext') echo '<td onclick="zoom_message(\'message_'.$tab_champs_nom[$i_champ].'_'.$id_item.'\');"><div class="cellule_a_zoomer" id="message_'.$tab_champs_nom[$i_champ].'_'.$id_item.'">'.$donnees[$i_champ].'</div></td>';
		else {
			
		//qqs champs en particulier
		switch ($tab_champs_nom[$i_champ]){
			case 'contact_mail': echo '<td><a href="mailto:'.$donnees[$i_champ].'">'.$donnees[$i_champ].'</a></td>';
			break;
			case 'url_site': echo '<td><a target="blank" href="http://'.str_replace('http://','',$donnees[$i_champ]).'">'.$donnees[$i_champ].'</a></td>';
			break;
			//qqs champs en particulier
			default: echo '<td>'.$donnees[$i_champ].'</td>';
			}
		
		}
	}
	
			
			
	echo '</tr>';
	}
echo '</table>';
		?>
<input type="button" name="CheckAll" value="Tous" onClick="checkall_uncheckall('id_actif',1)">
<input type="button" name="UnCheckAll" value="Aucun" onClick="checkall_uncheckall('id_actif',0)">
<?php if ($table!='inscriptions'){
echo '<select name="type_action">
	<option>---</option>
	<option value="supprimer">supprimer</option>
	<option value="valider">valider</option>
	<option value="invalider">invalider</option>
</select>
<input type="submit" name="action" value="action sur ids" />';
}
else{
echo '<select name="type_action2">
	<option>changer statut</option>
	<option value="poubelle">poubelle</option>
	<option value="annulé">annulé</option>
	<option value="inscrit">inscrit</option>
	<option value="spécifique">spécifique</option>
	<option value="liste attente">liste attente</option>
</select>
<input type="submit" name="action" value="action sur ids" />';
	}
?>
</form>

	</div>	
		
	</div>

		<?php
	mysql_close();
	include 'blocs/pied.php';//LE MENU DU BAS
	?>

	<script type="javascript">
	<?php
	//if (isset($_GET['filtre1'])) echo 'document.formu.segement1.selected=true;';
	?>
	</script>
</body>
</html>