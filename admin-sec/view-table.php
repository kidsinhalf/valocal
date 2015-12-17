<?php
session_start();
include '../inc/baseconnect.php';
include '../inc/fonctions.php';
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

$clause_where='';
$tab_clause_where=array();

$tab_champs_primaires=array();
$donnees_champs=db_select('SHOW COLUMNS FROM '.$table);
//var_dump($donnees_champs);

foreach ($donnees_champs as $tab_champs){
	if ($tab_champs['Type']!='text') $tab_champs_nom[]=$tab_champs['Field'];
	if ($tab_champs['Type']!='text') $tab_champs_type[]=$tab_champs['Type'];
	if ($tab_champs['Key']=='PRI') $tab_champs_primaires[]=$tab_champs['Field'];
	if (substr($tab_champs['Type'],0,4)=='enum'){
		$tab_enum[$tab_champs['Field']]=explode("','",substr($tab_champs['Type'],6,-2));
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
			<h1>Accueil</h1>
            <?php if (!empty($tab_alerte)) foreach ($tab_alerte as $alerte) echo $alerte; //Le tableau d'alertes?>
       
<!-- Code à modifier-->
<?php
/*Preparation des filtres*/
$html_form_select='';
foreach ($tab_champs_nom as $champs_nom){
	$html_form_select.='<option value="'.$champs_nom.'">'.$champs_nom.'</option>';
	}

		/*Exemples de filtre en LIKE ou = strict*/
		if (isset($_GET['segment1']) AND !empty($_GET['segment1'])){
			if ($_GET['segment1']=='id_formation') $tab_clause_where[]=' '.$_GET['segment1'].' = "'.$_GET['filtre1'].'"';
			else $tab_clause_where[]=' '.$_GET['segment1'].' LIKE "%'.$_GET['filtre1'].'%"';
			}
		if (isset($_GET['segment2']) AND !empty($_GET['segment2'])){
		if ($_GET['segment2']=='id_formation') $tab_clause_where[]=' '.$_GET['segment2'].' = "'.$_GET['filtre2'].'"';
			else $tab_clause_where[]=' '.$_GET['segment2'].' LIKE "%'.$_GET['filtre2'].'%"';
			}
		
		/*Exemple de filtre directement par nom du champ passé en paramètre GET (attention aux injections SQL)*/
		$array_key_get=array_keys($_GET);
		if (!in_array('id',$array_key_get)){
		$array_clause=array_intersect($array_key_get,$tab_champs_nom);
		if (!empty($array_clause)){
			foreach ($array_clause as $clause) $tab_clause_where[]=' '.$clause.' = "'.$_GET[$clause].'"';
			}
		}
		
		if (!empty($tab_clause_where)) $clause_where=' WHERE '.implode(' AND ',$tab_clause_where);
		
$id_item=0;

$query='SELECT COUNT(id) AS total FROM '.$table.$clause_where;
$donnees_temp=db_select($query);

$nb_items=$donnees_temp[0]['total'];

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

	<?php
	//filtres
	echo '
	
	<form method="get">
		<fieldset>
			<legend>Filtres</legend>
			<div class="flex_parent">
				<div class="flexed flex_parent">
					<div class="flexed"><label class="prefix">filtre 1</label></div><div class="flexed"> <select name="segment1"><option></option>'.$html_form_select.'</select></div><div class="flexed"><input type="text" name="filtre1" value="" /></div>
				</div>
				<div class="flexed flex_parent">
					<div class="flexed"><label class="prefix">filtre 2</label></div><div class="flexed"><select name="segment2"><option></option>'.$html_form_select.'</select></div><div class="flexed"><input type="text" name="filtre2" value="" /></div>
				</div>
				<div class="flexed"><input type="submit" class="button tiny" value="segment" /><input type="hidden" name="table" value="'.$table.'"></div>
			</div>
			</fieldset>
		</form>
	';

		//import fichier
		if ($table=='base_email' OR $table=='inscriptions' OR $table=='recettes' OR $table=='emails_desinscriptions' OR $table=='emails_retourserreurs'){
			echo '
				<form method="POST" enctype="multipart/form-data">
				<img src="css/items/upload-page-green.gif" /><strong> Importer</strong> un fichier csv : <input type="file" name="fichier_csv" />
				<input type="submit" name="action" value="importer" />
				</form>';
				}
	
	?>

	
	
	<form name="main_form" method="post">
	<table id="tabmain">
	<?php
echo '<caption>'.$nb_items.' éléments trouvés</caption>';
echo '
<thead>
<tr>';
echo '<th>action</th>';
foreach ($tab_champs_nom as $champ_nom) echo '<th>'.$champ_nom.'</th>';
echo '</tr>
</thead>
<tbody>
';


$order='ORDER BY '.$tab_champs_nom[0].' DESC';	
$query='SELECT '.implode(',',$tab_champs_nom).' FROM '.$table.$clause_where.' '.$order.' LIMIT '.$deb.', '.$nb_items_par_page;
//echo $query;
$donnees_all=db_select($query);
foreach ($donnees_all as $donnees){
$id_item++;
		
	if (isset($_POST['id']) AND $_POST['id']==$donnees['id']) $tr_couleur='#aa66aa';
	if (isset($donnees['statut']) AND $donnees['statut']=='liste attente') $tr_couleur='#e0d5e3';
	if (isset($donnees['statut']) AND $donnees['statut']=='annulé') $tr_couleur='#f1e4e1';
	if (isset($donnees['statut']) AND $donnees['statut']=='absent') $tr_couleur='#c8eac8';
	if (isset($donnees['statut']) AND $donnees['statut']=='poubelle') $tr_couleur='#e5e6c0';
	
	echo '<tr id="ancre_'.$donnees['id'].'">';
	
	//boutons d'action
	$url_cles_primaires=array();
	
	foreach ($tab_champs_primaires as $nom_cles_primaires){
		$url_cles_primaires[]=$nom_cles_primaires.'='.$donnees[$nom_cles_primaires];
		}
	//print_r($url_cles_primaires);
		
	echo '<td>
			<input type="checkbox" name="id_actif[]" value="'.$donnees['id'].'" />
			<a href="edit-table.php?table='.$table.'&action=modifier&'.implode('&',$url_cles_primaires).'"><img src="css/items/edit-blue.gif" /></a>';
			if ($table!='inscriptions') echo '<a onclick="return confirm(\'êtes-vous sûr de vouloir supprimer ?\');" href="view-table.php?action=supprimer&table='.$table.'&'.implode('&',$url_cles_primaires).'"><img src="css/items/delete-page-red.gif" /></a>
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
			default: echo '<td>'.$donnees[$tab_champs_nom[$i_champ]].'</td>';
			}
		
		}
	}
	
			
			
	echo '</tr>';
	}
echo '
</tbody>';
?>
</table>

<script>
$(document).ready( function () {
    $('#tabmain').DataTable({
		select: true,
		scrollY: 300,
		paging: false
		});
} );
</script>
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
?>
</form>

 
            </div>
          </div>
<?php include 'blocs/footer-admin.php';?>	