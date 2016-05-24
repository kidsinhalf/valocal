<?php
session_start();
include '../inc/baseconnect.php';
include '../inc/fonctions.php';
$tab_alerte=array();
include 'inc/recuperation-get.php';
if (!empty($_POST)) include 'inc/recuperation-post.php';

/* Options fondamentales */
$table=$_GET['table'];
$action=$_GET['action'];
switch ($action){
	case 'nouveau': $action_titre='Insertion';break;
	case 'modifier': $action_titre='Modification';break;
	default: $action_titre='Kesako ?';break;
	}
$tab_champs_nom=array();$tab_champs_type=array();$tab_champs_valeur_defaut=array();$tab_champs_primaires=array();$tab_champs_comments=array();

//récupération des noms et types de champs
$donnees_champs=db_select('SHOW FULL COLUMNS FROM '.$table);
//var_dump($donnees_champs);
foreach ($donnees_champs as $tab_champs){
	$tab_champs_nom[]=$tab_champs['Field'];
	$tab_champs_type[]=$tab_champs['Type'];
	$tab_champs_valeur_defaut[]=$tab_champs['Default'];
	$tab_champs_comments[]=$tab_champs['Comment'];
	if ($tab_champs['Key']=='PRI') $tab_champs_primaires[]=$tab_champs['Field'];
}

	$url_cles_primaires=array();
	foreach ($_GET as $nom_get=>$value_get){
		if (in_array($nom_get,$tab_champs_primaires)) $url_cles_primaires[]=$nom_get.'='.$value_get;
		}
	$sql_cle_primaire=implode('AND',$url_cles_primaires);

	
//CAS MODIF UPDATE (recupeation des données existantes)
if ($action!='nouveau'){
$sql='SELECT * FROM '.$table.' WHERE '.$sql_cle_primaire;
$donnees_all=db_select($sql);
$donnees=$donnees_all[0];
}

//CAS NOUVEAU
if ($action=='nouveau'){
$donnees=array();
//initialisation avec valeurs par défaut
for ($j=0;$j<sizeof($tab_champs_nom);$j++){
	switch($tab_champs_comments[$j]){
		case 'aujourdhui':$donnees[$tab_champs_nom[$j]]=date('Y-m-d');
		break;
		case 'maintenant':$donnees[$tab_champs_nom[$j]]=date('H:i:s');
		break;
		default:$donnees[$tab_champs_nom[$j]]=$tab_champs_valeur_defaut[$j];
		}
	}
}


	//paramleters get
	//gestion des parametres précédents: segment1
	$parameters_get='';
	if (isset($_SERVER['HTTP_REFERER'])){
	if (strpos($_SERVER['HTTP_REFERER'],'&')>0){
		$pos=strpos($_SERVER['HTTP_REFERER'],'&');
		$parameters_get=substr($_SERVER['HTTP_REFERER'],$pos);
		}
	}
	
	if ($table=='achats' OR $table=='ventes'){
		include 'inc/initialisation_params.php';
		}
	
	//echo '<a href="view-table.php?table='.$table.$parameters_get.'"><img src="images/icons/table-select.png" alt="voir" />voir '.$table.'</a>';
	?>

<?php include 'blocs/header-admin.php';?>  
    <div class="row">
        <div class="large-12 columns">
		<?php include 'blocs/menu-admin.php';?>  
          </div>
    </div>

    <div class="row">
        <div class="large-12 columns">
			<h1 class="text-centered"><?php echo $action_titre.' '.$table;?></h1>
            <?php if (!empty($tab_alerte)) foreach ($tab_alerte as $alerte) echo $alerte; //Le tableau d'alertes?>
    
<div class="row">
		<div class="large-8 columns">	
<!-- Code à modifier-->
	
	
	<form name="main_form" action="view-table.php?table=<?php echo $table.$parameters_get;?>" method="post" enctype="multipart/form-data">
	
	
<?php	
for ($i=0;$i<sizeof($tab_champs_nom);$i++){
	echo '
	<div class="row">
		<div class="small-3 columns"><label for="fid_'.$i.'" class="right inline">'.$tab_champs_nom[$i].'</label></div>
		<div class="small-9 columns">';
	//echo '<em>'.$tab_champs_comments[$i].'</em>';
		
		//INSPECTION COMMENTAIRE TABLE
		switch (substr($tab_champs_comments[$i],0,4)){
			case 'join':
				$tab_jointure=explode(',',substr($tab_champs_comments[$i],5));
				$sql_join='SELECT '.$tab_jointure[1].', '.$tab_jointure[2].' FROM '.$tab_jointure[0];
				echo '<select id="fid_'.$i.'" name="'.$tab_champs_nom[$i].'">';
				echo '<option value="0">aucun</option>';
				$reponse_temp=db_select($sql_join,'',false, 'num');
				foreach ($reponse_temp as $donnees_temp){
					if ($donnees[$tab_champs_nom[$i]]==$donnees_temp[0]) echo '<option selected="selected" value="'.$donnees_temp[0].'">'.$donnees_temp[1].' (n°'.$donnees_temp[0].')</option>';
					else echo '<option value="'.$donnees_temp[0].'">'.$donnees_temp[1].' (n°'.$donnees_temp[0].')</option>';
					}
				echo '</select>';
				//echo $sql_join;
			break;
			
			case 'dero':
			$sql_join='SELECT DISTINCT '.$tab_champs_nom[$i].' FROM '.$table.' ORDER BY '.$tab_champs_nom[$i];
				//echo $sql_join;
				echo '<select name="'.$tab_champs_nom[$i].'" onchange="choisir_select(this);">
							<option value="">-</option>';
				$reponse_temp=db_select($sql_join,'',false, 'num');
				foreach ($reponse_temp as $donnees_temp){
					if ($donnees[$tab_champs_nom[$i]]==$donnees_temp[0]) echo '<option selected="selected" value="'.$donnees_temp[0].'">'.$donnees_temp[0].'</option>';
					else echo '<option value="'.$donnees_temp[0].'">'.$donnees_temp[0].'</option>';
					}
					
				echo '<option value="choix">Choisir</option>
				</select>';
			break;
			
			
			//FIN INSPECTION COMMENTAIRE TABLE
			default:		
			
				//INSPECTION TYPE CHAMP
			switch (substr($tab_champs_type[$i],0,4)){
			case 'date':
			echo '
			<input type="text" data-date name="'.$tab_champs_nom[$i].'" value="'.date_us2fr($donnees[$tab_champs_nom[$i]]).'" class="fdatepicker" id="fid_'.$i.'" />
			';
			break;
			
			case 'time':
			echo '
			<input type="text" data-time name="'.$tab_champs_nom[$i].'" value="'.$donnees[$tab_champs_nom[$i]].'" class="ftimepicker" id="fid_'.$i.'" />
			';
			break;
			
			break;
			case 'text':
				$nom_texte=$tab_champs_nom[$i];
				include 'inc/babacode.php';
			break;
			case 'tiny':
				echo '
				<br /><input type="radio" name="'.$tab_champs_nom[$i].'" value=0 ';
				if ($donnees[$tab_champs_nom[$i]]==0) echo 'checked ';
				echo '/>0 ';
				echo ' - <input type="radio" name="'.$tab_champs_nom[$i].'" value=1 ';
				if ($donnees[$tab_champs_nom[$i]]==1) echo 'checked ';
				echo '/>1 validé ';
			break;
			
			case 'enum':
				echo '
				<br />
				<select name="'.$tab_champs_nom[$i].'">';
				$tab_enum=explode("','",substr($tab_champs_type[$i],6,-2));
				foreach ($tab_enum as $enum){
					if ($donnees[$tab_champs_nom[$i]]==$enum) $selected=' selected="selected"'; else $selected='';
					echo '<option value="'.$enum.'"'.$selected.'>'.$enum.'</option>';
					}
				echo '</select>';
			break;
				// FIN INSPECTION TYPE CHAMP
			
			default:
			// cas spécifiques en fonction du nom du champ
			switch ($tab_champs_nom[$i]){
				case 'id':
				if ($action=='nouveau'){$class_id_display=' style="display:none"' ;}
				else {$class_id_display='';}
				
				echo '<input'.$class_id_display.' type="text" name="id" readonly value="'.$donnees['id'].'" />';
				break;
				
				case 'cours':
				$cours_default=0;
				$donnees_cours=db_select('SELECT * FROM cours WHERE date= ? ', array(date('Y-m-d')), true);
				if (!empty($donnees_cours)){
					$cours_default=$donnees_cours[0]['valeur_euro_tonne'];
					}
					echo '<input type="text" onchange="calcul_prix_'.$table.'()" id="fid_'.$i.'" name="'.$tab_champs_nom[$i].'" value="'.$cours_default.'" style="width:100px; display:inline" /> €/t';
				break;
				
				
				
				case 'taux_achat':
				case 'taux_vente':
					echo '<input type="text" onchange="calcul_prix_'.$table.'()" id="fid_'.$i.'" name="'.$tab_champs_nom[$i].'" value="'.$donnees[$tab_champs_nom[$i]].'" style="width:100px; display:inline" /> €/t';
				break;
				
				
				case 'tva':
					echo '<input type="text" onchange="calcul_prix_'.$table.'()" id="fid_'.$i.'" name="'.$tab_champs_nom[$i].'" value="'.$donnees[$tab_champs_nom[$i]].'" style="width:100px; display:inline" /> %';
				break;
				
				case 'poids':
					echo '<input type="text" onchange="calcul_prix_'.$table.'()" id="fid_'.$i.'" name="'.$tab_champs_nom[$i].'" value="'.$donnees[$tab_champs_nom[$i]].'" style="width:100px; display:inline" /> kg';
				break;
				
				case 'categorie_params':
					echo '<select name="'.$tab_champs_nom[$i].'" onchange="calcul_prix_'.$table.'();">';
					$reponse_temp=db_select('SELECT * FROM parametres WHERE categorie= ? ', array('categorie_'.$table), true);
					if (!empty($reponse_temp)){
						foreach ($reponse_temp as $donnees_temp){
							if ($donnees[$tab_champs_nom[$i]]==$donnees_temp['titre']) echo '<option selected="selected" value="'.$donnees_temp['titre'].'">'.$donnees_temp['titre'].'</option>';
							else echo '<option value="'.$donnees_temp['titre'].'">'.$donnees_temp['titre'].'</option>';
							}
						}
					echo '</select>';
				break;
				
				

				
				default:
				if ($tab_champs_type[$i]=='mediumint(9)'){
					echo '<input type="text" name="'.$tab_champs_nom[$i].'" value="'.$donnees[$tab_champs_nom[$i]].'" size="5" />';
					}
				else{
					echo '<input type="text" id="fid_'.$i.'" name="'.$tab_champs_nom[$i].'" value="'.str_replace('"','&quot;',$donnees[$tab_champs_nom[$i]]).'" />';
					}
				break;
				}
			}
			break;
		}
	echo '
		</div>
	</div>';
	}
	
?>
<input type="hidden" name="table" value="<?php echo $table;?>" />
<div class="text-center">
<?php
if ($action=='nouveau') echo '<input class="button" type="submit" name="action" value="nouveau" />';
if ($action=='modifier') echo '<input class="button" type="submit" name="action" value="modifier" />';
?>
</div>	
	</form>
	
	<div class="text-center"><a class="button alert" href="view-table.php?table=<?php echo $table;?>">Annuler</a></div>
</div>

		<div class="large-4 columns">
		<?php if ($table=='achats' OR $table=='ventes'){
		include 'blocs/memo-cuivre.php';
		}
		?>
		</div>
</div>
	




 
 
            </div>
          </div>
<script>
function choisir_select(select) {
	if(select.value == 'choix') {
		var choix = document.createElement('input');
		choix.type= 'text';
		choix.onblur= function() {
		var option = document.createElement('option');
		option.innerHTML = choix.value;
		option.value = choix.value;
		choix.parentNode.replaceChild(select, choix);
		select.insertBefore(option, select.firstChild);
		select.selectedIndex = 0;
		}
		select.parentNode.replaceChild(choix, select);
	}
	select.focus();
}

$('.fdatepicker').fdatepicker({
  language: 'fr',
  format: 'dd/mm/yyyy'
});

$('.fdatepicker').fdatepicker({
  language: 'fr',
  format: 'hh:ii:ss'
});
</script>
<?php include 'blocs/footer-admin.php';?>