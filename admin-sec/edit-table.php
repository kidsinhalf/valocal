<?php
session_start();
include '../inc/baseconnect.php';
include '../inc/fonctions.php';
$tab_alerte=array();
include 'inc/recuperation-get.php';
if (!empty($_POST)) include 'inc/recuperation-post.php';

/* Options fondamentales */
$table=$_GET['table'];
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
if ($_GET['action']!='nouveau'){
$sql='SELECT * FROM '.$table.' WHERE '.$sql_cle_primaire;
$donnees_all=db_select($sql);
$donnees=$donnees_all[0];
var_dump($donnees);
}

//CAS NOUVEAU
if ($_GET['action']=='nouveau'){
$donnees=array();
//initialisation avec valeurs par défaut
for ($j=0;$j<sizeof($tab_champs_nom);$j++){
	switch($tab_champs_comments[$j]){
		case 'aujourdhui':$donnees[$j]=date('Y-m-d');
		break;
		default:$donnees[$j]=$tab_champs_valeur_defaut[$j];
		}
	}
}


	//paramleters get
	//gestion des parametres précédents: segment1
	$parameters_get='';
	if (strpos($_SERVER['HTTP_REFERER'],'&')>0){
		$pos=strpos($_SERVER['HTTP_REFERER'],'&');
		$parameters_get=substr($_SERVER['HTTP_REFERER'],$pos);
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
			<h1>Modification</h1>
            <?php if (!empty($tab_alerte)) foreach ($tab_alerte as $alerte) echo $alerte; //Le tableau d'alertes?>
       
<!-- Code à modifier-->

	<form name="main_form" action="view-table.php?table=<?php echo $table.$parameters_get;?>" method="post" enctype="multipart/form-data">
	
	
<?php	
for ($i=0;$i<sizeof($tab_champs_nom);$i++){
	echo '
	<div>
	<div class="label_champ"><label>'.$tab_champs_nom[$i].'</label></div>';
	//echo '<em>'.$tab_champs_comments[$i].'</em>';
		
		switch (substr($tab_champs_comments[$i],0,4)){
			case 'join':
				$tab_jointure=explode(',',substr($tab_champs_comments[$i],5));
				$sql_join='SELECT '.$tab_jointure[1].', '.$tab_jointure[2].' FROM '.$tab_jointure[0];
				echo '<select name="'.$tab_champs_nom[$i].'">';
				echo '<option value="0">aucun</option>';
				$reponse_temp=db_select($sql_join,'',false, 'num');
				foreach ($reponse_temp as $donnees_temp){
					if ($donnees[$i]==$donnees_temp[0]) echo '<option selected="selected" value="'.$donnees_temp[0].'">'.$donnees_temp[0].' - '.$donnees_temp[1].'</option>';
					else echo '<option value="'.$donnees_temp[0].'">'.$donnees_temp[0].' - '.$donnees_temp[1].'</option>';
					}
				echo '</select>';
				//echo $sql_join;
			break;
			
			case 'dero':
			$sql_join='SELECT DISTINCT '.$tab_champs_nom[$i].' FROM '.$table.' ORDER BY '.$tab_champs_nom[$i];
				echo $sql_join;
				echo '<select name="'.$tab_champs_nom[$i].'" onchange="choisir_select(this);">
							<option value="">-</option>';
				$reponse_temp=db_select($sql_join,'',false, 'num');
				foreach ($reponse_temp as $donnees_temp){
					if ($donnees[$i]==$donnees_temp[0]) echo '<option selected="selected" value="'.$donnees_temp[0].'">'.$donnees_temp[0].'</option>';
					else echo '<option value="'.$donnees_temp[0].'">'.$donnees_temp[0].'</option>';
					}
					
				echo '<option value="choix">Choisir</option>
				</select>';
			
			break;
			
			default:		

			switch (substr($tab_champs_type[$i],0,4)){
			case 'date':
			echo '
			<input type="text" name="'.$tab_champs_nom[$i].'" value="'.$donnees[$i].'" class="date_text" />
			<img src="css/items/agenda_associe.png" onclick="displayCalendar(document.forms[\'form_principal\'].'.$tab_champs_nom[$i].',\'yyyy-mm-dd\',this)">';
			break;
			case 'text':
				$nom_texte=$tab_champs_nom[$i];
				include 'inc/babacode.php';
			break;
			case 'tiny':
				echo '
				<br /><input type="radio" name="'.$tab_champs_nom[$i].'" value=0 ';
				if ($donnees[$i]==0) echo 'checked ';
				echo '/>0 ';
				echo ' - <input type="radio" name="'.$tab_champs_nom[$i].'" value=1 ';
				if ($donnees[$i]==1) echo 'checked ';
				echo '/>1 validé ';
			break;
			
			case 'enum':
				echo '
				<br />
				<select name="'.$tab_champs_nom[$i].'">';
				$tab_enum=explode("','",substr($tab_champs_type[$i],6,-2));
				foreach ($tab_enum as $enum){
					if ($donnees[$i]==$enum) $selected=' selected="selected"'; else $selected='';
					echo '<option value="'.$enum.'"'.$selected.'>'.$enum.'</option>';
					}
				echo '</select>';
			break;
			
			
			default:// cas spécifiques en fonction du nom du champ
			switch ($tab_champs_nom[$i]){
				case 'id':
				echo '<input type="hidden" name="id" value="'.$donnees['id'].'" /> '.$donnees['id'].'<br />';
				break;
				
				case 'planning':
				include 'inc/planning.php';
				break;
				
				

				
				default:
				if ($tab_champs_type[$i]=='mediumint(9)')
					echo '<input type="text" name="'.$tab_champs_nom[$i].'" value="'.$donnees[$tab_champs_nom[$i]].'" size="5" id="form_principal_'.$tab_champs_nom[$i].'" />';
				else echo '
				<input type="text" name="'.$tab_champs_nom[$i].'" value="'.str_replace('"','&quot;',$donnees[$tab_champs_nom[$i]]).'" size="120" id="form_principal_'.$tab_champs_nom[$i].'" />';
				break;
				}
			}
		}
	
		
	echo '
	</div>';
	}
	
?>
<input type="hidden" name="table" value="<?php echo $table;?>" />
<?php


if ($_GET['action']=='nouveau') echo '<input type="submit" name="action" value="nouveau" />';
if ($_GET['action']=='modifier') echo '<input type="submit" name="action" value="modifier" />';
?>
	
	</form>
	

<script>
function choisir_select(select) {
	if(select.value == 'choix') {
		var choix = document.createElement('input');
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
</script>



 
 
            </div>
          </div>
<?php include 'blocs/footer-admin.php';?>