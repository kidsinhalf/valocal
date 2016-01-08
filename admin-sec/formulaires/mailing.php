<script type="text/javascript" src="js/ajax.js"></script>

<form method="post">

<div class="label_champ"><label>titre</label></div>
<input type="text" name="titre" value="<?php echo $donnees['titre'];?>" size="70" />

<div class="label_champ"><label>texte</label></div>
<textarea name="texte" cols="130" rows="10">
<?php echo $donnees['texte'];?>
</textarea>

<div class="label_champ"><label>Filtres</label></div>
<?php
$departements_sel='';
$tab_filtres=array("annee","categorie","valide","renouvellement","type_adherent","type_adhesion","symbole_derniere_adhesion","annee_derniere_adhesion");
if (!empty($donnees['emails_clause_where'])){
	$femails_clause_where=str_replace('WHERE ', '', $donnees['emails_clause_where']);
	$tab_femails_clause_where=explode(' AND ', $femails_clause_where);
	foreach ($tab_femails_clause_where as $tre){
		//echo $tre;
		//cas département
		if (strpos($tre,'cp')>0){
			$departements=str_replace('SUBSTRING(cp,1,2) IN (','',$tre);
			$departements=str_replace(')','',$departements);
			$departements_sele=$departements;
			
			//echo $departements;
			}
		else{
		
		$tab_tre=explode('=',trim($tre));
		if (strpos($tre,'annee_derniere_adhesion')>0){
			$symbole_derniere_adhesion=substr($tab_tre[0], -1);
			if ($symbole_derniere_adhesion !=">" AND $symbole_derniere_adhesion !="<" AND $symbole_derniere_adhesion !="!") $symbole_derniere_adhesion="=";
			//echo $tab_tre[0].' SYM:'.$symbole_derniere_adhesion.'<br />';
			$annee_derniere_adhesion=str_replace('"','',$tab_tre[1]);
			}
		else $tre_arguments[$tab_tre[0]]=str_replace('"','',$tab_tre[1]);
		}
		}
		if (!empty($annee_derniere_adhesion) AND !empty($symbole_derniere_adhesion)) $tre_arguments['annee_derniere_adhesion']=$symbole_derniere_adhesion.'"'.$annee_derniere_adhesion.'"';
		//print_r($tre_arguments);
	}
foreach ($tab_filtres as $filtre){
	if ($filtre=="symbole_derniere_adhesion") echo '<br />';
	echo '
	<label for="fid_'.$filtre.'">'.$filtre.'</label>
	<select id="fid_'.$filtre.'" name="'.$filtre.'" onchange=\'filtre2emails("annee","categorie","valide","type_adherent","type_adhesion","renouvellement","annee_derniere_adhesion","symbole_derniere_adhesion","departements", "liste_mails")\'>
	<option>-</option>';
	if ($filtre=="annee_derniere_adhesion" OR $filtre=="symbole_derniere_adhesion" OR $filtre=='type_adherent'){
		
		if ($filtre=="symbole_derniere_adhesion"){
			if (isset($symbole_derniere_adhesion) AND $symbole_derniere_adhesion=="=") $cl_sele=' selected="selected"'; else $cl_sele='';
			echo '<option'.$cl_sele.' value="=">=</option>';
			
			if (isset($symbole_derniere_adhesion) AND $symbole_derniere_adhesion=="<") $cl_sele=' selected="selected"'; else $cl_sele='';
			echo '<option'.$cl_sele.' value="<="><=</option>';
			if (isset($symbole_derniere_adhesion) AND $symbole_derniere_adhesion==">") $cl_sele=' selected="selected"'; else $cl_sele='';
			echo '<option'.$cl_sele.' value=">=">>=</option>';
			if (isset($symbole_derniere_adhesion) AND $symbole_derniere_adhesion=="!") $cl_sele=' selected="selected"'; else $cl_sele='';
			echo '<option'.$cl_sele.' value="!=">!=</option>';
			echo '</select>';
			}
		
		if ($filtre=="annee_derniere_adhesion"){
			$reponse_temp=mysql_query('SELECT DISTINCT annee FROM adhesions ORDER BY annee');
			while ($donnees_temp=mysql_fetch_assoc($reponse_temp)){
			if (isset($annee_derniere_adhesion) AND $annee_derniere_adhesion==$donnees_temp['annee']) $cl_sele=' selected="selected"'; else $cl_sele='';
			echo '
			<option'.$cl_sele.' value="'.$donnees_temp['annee'].'">'.$donnees_temp['annee'].'</option>';
			}
		echo '
		</select> - ';
			}
			
			if ($filtre=="type_adherent"){
			
			echo '<option value="adherent">adhérent</option>
			<option value="donateur">donateur</option>
			<option value="personne morale">personne morale</option>'
			;
			
		echo '
		</select> - ';
			}
		}
	else{
		$reponse_temp=mysql_query('SELECT DISTINCT '.$filtre.' FROM adhesions ORDER BY '.$filtre);
		while ($donnees_temp=mysql_fetch_assoc($reponse_temp)){
			if (isset($tre_arguments[$filtre]) AND $tre_arguments[$filtre]==$donnees_temp[$filtre]) $cl_sele=' selected="selected"'; else $cl_sele='';
			echo '
			<option'.$cl_sele.' value="'.$donnees_temp[$filtre].'">'.$donnees_temp[$filtre].'</option>';
			}
		echo '
		</select> - ';
		}
	}
	echo '<br /><label for="fid_departements">Départements cibles (séparez par des virgules)</label><br />ex: 92,65,77
	<input type="text" id="fid_departements" name="departements" value="'.$departements_sele.'" onchange=\'filtre2emails("annee","categorie","valide","type_adherent","renouvellement","annee_derniere_adhesion","symbole_derniere_adhesion","departements","liste_mails")\' />';
	


?>



<div class="label_champ"><label>destinataires</label></div>
<div id="liste_mails" style="width:300px; height:200px; background:#fafafa;border:dotted 1px #bebebe;overflow:scroll;font-size:10px;">
vide
</div>

<div style="border: dotted 1px #bebebe;width:300px;">
	<div class="gras fb bl">Aperçu filtre requête</div>
<textarea name="emails_clause_where" cols="50" disabled><?php echo ($donnees['emails_clause_where']);?></textarea>
</div>
<br />
<div style="border: dotted 1px #bebebe;width:300px;">
	<div class="gras fb bl">Mode test</div>
	
<input type="radio" id="fmode_testn" name="mode_test" value="0" <?php if ($donnees['mode_test']==0) echo 'checked="checked" ';?>/><label for="fmode_testn">non</label> - 
<input type="radio" id="fmode_test" name="mode_test" value="1" <?php if ($donnees['mode_test']==1) echo 'checked="checked" ';?>/><label for="fmode_test">oui</label>
</div>
<input type="hidden" name="id_lettre_type" value="<?php echo $id_lettre_type;?>" />
<br />
<input type="submit" name="sauvegarder" value="sauvegarder" />
<?php
//echo '<input type="submit" name="envoyer" value="envoyer" />';
?>
</form>