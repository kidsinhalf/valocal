<?php
$json_a=json_decode($json_string,true);
$message_succes=$donnees_form['message_success'];
$id_email_envoyer=$donnees_form['id_lettres_type'];
//print_r($json_a);

$tab_alertes=array(); // Alertes de niveau 1 (Erreurs de saisies)
$formulaire_success=false; // Alertes niveau 2 (base SQL)

//change le nom du id du formulaire #form_name est ajaxable mais pas #form_namenon
$ajaxable='non'; 
if ($id_formulaire==11 OR $id_formulaire==13 OR $id_formulaire==16){
	$ajaxable='';
}

/* Traitements spéciaux (remplir un champ par exemple) */
// Si formulaire de modification user
if ($id_formulaire == 14){
	if (isset($_GET['action'])){
		$tab_deja=array();
		$tab_deja_options=array();
		$tab_id_tarifs_deja=array();
			
		switch ($_GET['action']){
			//création
			case 'creer':
				$id_formulaire=secure_id($_GET['id_formulaire']);
				if ($id_formulaire>0){
        	
					$reponse_form=$db->query('SELECT * FROM formulaires_dyn WHERE id='.$id_formulaire);
					$donnees_form=$reponse_form->fetch_array(MYSQLI_ASSOC);
					$reponse_form->close();
				
					$json_string=$donnees_form['texte'];
					echo '<h1>'.$donnees_form['titre'].'</h1>';
				}
			break;
			
			//edition fiche
			case 'edit':
				if (verif_droit($_SESSION['id_utilisateur'], $_GET['nom_table'], $_GET['id_table'])){
					$reponse_req=$db->query('SELECT * FROM '.secure_table($_GET['nom_table']).' WHERE id='.secure_id($_GET['id_table']));
					$tab_deja=$reponse_req->fetch_array(MYSQLI_ASSOC);
					$reponse_req->close();
				}else{
					echo '<div class="msg-box alerte_erreur">Modification non autorisé</div>';
				}
			break;
		}
	}
}

/***************************************** GESTION DES ERREURS *****************************************/
if (isset($_POST['formdyn']) AND !empty($_POST['formdyn'])){

	/********************* 1er Niveau ******************/
	/* Erreurs de saisie, format de mail, etc...       */
	
	$sql=''; 
	$tab_champs_ok=array();
	
	foreach ($json_a as $num_question=>$tab_question){
		
		foreach ($tab_question as $type_question=>$question){
			if (!empty($question['name'])){
				$name=$question['name']; 
			}else{
				$name='';
			}
			if (isset($question['verify']) AND !empty($question['verify'])){
				$verify=$question['verify']; 
			}else{
				$verify='';
			}
			if (empty($name)){
				$name='name_'.$num_question;
			}
			
			switch ($type_question){
				//HTML libre
				case 'html':
				break;
				
				//HTML libre
				case 'submit':
				break;
					
				case 'text':
					$tab_champs_ok[]=$name;
					$tab_sql_insert[]='"'.clean_html($_POST[$name]).'"';
					$tab_sql_update[]=$name.'="'.clean_html($_POST[$name]).'"';
				break;
					
				//special requetes SQL
				case 'action':

				//print_r($question);
				foreach ($question as $action_name=>$action_value){
					switch ($action_name){

						case 'table': $table=$action_value;
						break;
						
						case 'mode':
						if ($action_value=='session'){
							if (isset($_SESSION[$question['id']])) $id_sql=$_SESSION[$question['id']];
							}
						if ($action_value=='get'){
							if (isset($_GET[$question['id']])) $id_sql=secure_id($_GET[$question['id']]);
							}
						break;
						}
					}
				break;
				
				//zones de formulaire
				case 'checkbox':
				$tab_champs_ok[]=$name;
				if (!empty($_POST[$name])) $post_name=$_POST[$name]; else $post_name=array();
				$tab_sql_insert[]='"'.implode(',',$post_name).'"';
				$tab_sql_update[]=$name.'="'.implode(',',$post_name).'"';
				break;
				
				case 'meta_options':
				break;
				case 'meta_labels':
				break;
				
				case 'tarifs':
				break;
				case 'photos':
				break;
				case 'recommandations':
				break;
				
				case 'password':
				$tab_champs_ok[]=$name;
				$tab_sql_insert[]='"'.str_replace('"','\"',md5($_POST[$name])).'"';
				$tab_sql_update[]=$name.'="'.str_replace('"','\"',md5($_POST[$name])).'"';
				break;
				
				case 'date':
				$tab_champs_ok[]=$name;
				$tab_sql_insert[]='"'.datefr2us($_POST[$name]).'"';
				$tab_sql_update[]=$name.'="'.datefr2us($_POST[$name]).'"';
				break;
				
				default:
				/* Si formulaire d'inscription et que le champ est pseudo
				 * On le remplit automatiquement par le Prénom et l'initial du nom */
				if($id_formulaire==12 AND $name=="pseudo"){
					$pseudo=$_POST['prenom'].' '.substr($_POST['nom'], 0, 1).'.';
					$tab_champs_ok[]=$name;
					$tab_sql_insert[]='"'.$pseudo.'"';
				}else{
					$tab_champs_ok[]=$name;
					if (!isset($_POST[$name])) $value_post=''; else $value_post=$_POST[$name];
					$tab_sql_insert[]='"'.str_replace('"','\"',$value_post).'"';
					$tab_sql_update[]=$name.'="'.str_replace('"','\"',$value_post).'"';
				}
				break;
			}

			
		if (!empty($verify)){
			switch($verify){
				case 'empty':
				if (empty($_POST[$name])) $tab_alertes[$name]=$langue['form_err_champ_vide'].' '.$name;
				break;
				
				case 'email':
				if (!preg_match("!^[A-Za-z0-9._-]+@[A-Za-z0-9._-]{2,}\.[a-z]{2,4}$!", $_POST[$name])) $tab_alertes[$name]=$langue['form_email_err_format'];
				break;
				}
			}
		}
	}
	
	/********************* 2eme Niveau ******************/
	/* Tests SQL										*/
	
	if (empty($tab_alertes)){
		
		$formulaire_success = true;
		
		/* Formulaire d'inscription */
		if($id_formulaire == 12){
			
			/* On teste si l'email existe déjà */
			$email = $_POST['email'];
			$sql=$db->query('SELECT id FROM utilisateurs WHERE email = "'.$email.'"');
			if ($sql->num_rows>0){
				$formulaire_success = false;
				$message_succes = $langue['email_err_doublon'];
			}
			$sql->close();
		}
		
		
	/************************* FIN GESTION DES ERREURS ***********************/
		if ($formulaire_success){
				
			// Si la table est dans creations_fiches et qu'on est dans le formulaire 11, 
			// On update creations_fiches et on n'envoie pas de mail.
			if($id_formulaire==11){
				if($fiche_en_attente){
					$id_sql=$tab_deja['id'];
					$id_email_envoyer=0;
				}
			}
			
			if (isset($id_sql)){
				if ($id_sql>0){
					$action='update';
					$sql_action='UPDATE'; 
					$sql_action_suite='SET'; 
					$sql_action_fin='';
					$sql_where=' WHERE id='.$id_sql;
				}
			}else{
				$action='insert';
				$sql_action='INSERT INTO'; 
				$sql_action_suite='('.implode(', ',$tab_champs_ok).') VALUES ('; $sql_action_fin=')';
				$sql_where='';
			}
			
			//REQUETE INSERT OU UPDATE FINAL
			$sql=$sql_action.' '.$table.' '.$sql_action_suite.' '.implode(', ', ${'tab_sql_'.$action}).' '.$sql_action_fin.' '.$sql_where;
			echo $sql;
			$db->query($sql);
			
			if ($db->affected_rows<0){
				echo '<div class="debug debug'.$db->affected_rows.'">SQL : '.$sql.' '.$db->affected_rows.'</div>';
			}
			if ($action=='insert') $id_insert_dyn=$db->insert_id;
			
			//Ajout des droits d'édition (ajout/edition item) si identifié
			if ($action=='insert' AND $db->affected_rows==1 AND isset($_SESSION['id_utilisateur'])){
				$id_sql=$db->insert_id;
				
				// Generation d'une alerte back-office
				$alerte=new Alerte($_SESSION['id_utilisateur'], $table, $id_sql, $donnees_form['titre']);
				
				if($id_formulaire==11){
					$alerte->action="appropriation";
					$alerte->nom_table=secure_table($_GET['nom_table']);
				}
				
				if ($id_formulaire==11){
					$alerte->id_table=secure_id($_GET['id_table']);
					$alerte->set_niveau(1, 1);
					$alerte->id_insert=$id_sql;
					$alerte->table_insert=$table;
				}else{
					$alerte->set_niveau(1, 2);
				}
				$alerte->enregistre();
				$alerte->__destruct();
				
				$sql_droits=('INSERT INTO droits (id_utilisateurs,nom_table,id_table) VALUES ('.$_SESSION['id_utilisateur'].',"'.$table.'",'.$id_sql.')');
				$db->query($sql_droits);
				if ($db->affected_rows<0){
					echo '<div class="debug debug'.$db->affected_rows.'">DROITS : '.$sql_droits.' '.$db->affected_rows.'</div>';
				}
				else{
					//update niveau client
					$sql_droits=('UPDATE '.$table.' SET niveau_client=1 WHERE id='.$id_sql.' AND niveau_client<1');
					$db->query($sql_droits);
					if ($db->affected_rows<0){
						echo '<div class="debug debug'.$db->affected_rows.'">NIVEAU CLIENT : '.$sql_droits.' '.$db->affected_rows.'</div>';
						}
					}
			}
		
			//concerne uniquement hebergements et loisirs
			if ($table=='loisirs' OR $table=='hebergements'){
			//Préparation des requetes
			//META OPTIONS
			$tab_sql_options=array();
			if (!empty($_POST['meta_options'])){
				foreach ($_POST['meta_options'] as $meta_options=>$val){
						$tab_sql_options[]='INSERT INTO meta_'.$table.' (id_parametre, id_'.$table.') VALUES ('.$val.', #id_sql#)';
				}
			}
				
			//LABELS
			if (!empty($_POST['meta_labels'])){
				foreach ($_POST['meta_labels'] as $labels=>$val){
						$tab_sql_options[]='INSERT INTO meta_'.$table.' (id_parametre, id_'.$table.') VALUES ('.$val.', #id_sql#)';
				}
			}
				
			//TARIFS
			$tab_sql_tarifs=array();
			if (!empty($_POST['tarifs_titre'])){
				foreach ($_POST['tarifs_titre'] as $cle=>$value){
					if (in_array($cle, $tab_id_tarifs_deja)){  
						$tab_sql_tarifs[]='UPDATE tarifs_'.$table.' SET titre="'.$_POST['tarifs_titre'][$cle].'", date_debut="'.datefr2us($_POST['tarifs_date_debut'][$cle]).'", date_fin="'.datefr2us($_POST['tarifs_date_fin'][$cle]).'", prix_nuit="'.price($_POST['tarifs_prix_nuit'][$cle]).'", prix_semaine="'.price($_POST['tarifs_prix_semaine'][$cle]).'" WHERE id='.$cle;
					}else{
						if (!empty($_POST['tarifs_titre'][$cle]) AND !empty($_POST['tarifs_date_debut'][$cle]) AND !empty($_POST['tarifs_date_debut'][$cle])){
							$tab_sql_tarifs[]='INSERT INTO tarifs_'.$table.' (id_'.$table.', titre, date_debut, date_fin, prix_nuit, prix_semaine) VALUES (#id_sql#, "'.($_POST['tarifs_titre'][$cle]).'", "'.datefr2us($_POST['tarifs_date_debut'][$cle]).'", "'.datefr2us($_POST['tarifs_date_fin'][$cle]).'","'.price($_POST['tarifs_prix_nuit'][$cle]).'", "'.price($_POST['tarifs_prix_semaine'][$cle]).'")';
						}
					}
				}
			}

			//MySQL
			//Update options et labels			
			$tab_sql_options = str_replace("#id_sql#", $id_sql, $tab_sql_options);
			$tab_only_id_types=array_keys($tab_id_types);
			$sql_delete='DELETE FROM meta_'.$table.' WHERE id_'.$table.'='.$id_sql.' AND id_parametre NOT IN ('.implode(',', $tab_only_id_types).')';
			//echo $sql_delete;
			$db->query($sql_delete);
			foreach ($tab_sql_options as $sql_options){
				$db->query($sql_options);
				echo '<div class="debug debug'.$db->affected_rows.'">'.$sql_options.' '.$db->affected_rows.'</div>';
			}
			
			//Update tarifs
			$tab_sql_tarifs = str_replace("#id_sql#", $id_sql, $tab_sql_tarifs);
			foreach ($tab_sql_tarifs as $sql_tarifs){
				$db->query($sql_tarifs);
				echo '<div class="debug debug'.$db->affected_rows.'">'.$sql_tarifs.' '.$db->affected_rows.'</div>';
			}
			}
			
			
			//Envoi mail
			if (isset($id_email_envoyer) AND $id_email_envoyer>0 AND (isset($_POST['email']) OR isset($_POST['responsable_email']))){
				$destinataire="";
				if($id_email_envoyer==5){
					$expediteur=$_POST['email'];
					$qry=$db->query('SELECT email, responsable_email, titre FROM '.secure_table($_POST['nom_table']).' WHERE id='.secure_id($_POST['id_table']));
					$responsable=$qry->fetch_array(MYSQLI_ASSOC);
					
					if ($responsable['email']!=""){
						$destinataire=$responsable['email'];
					}else{
						$destinataire=$responsable['responsable_email'];
					}	
					
					// Envoi d'une lettre type par mail au prestataire
					if(envoyer_lettre_type($destinataire, $expediteur, 6)){
						
						//Envoi d'une lettre type par mail au l'utilisateur
						$success=envoyer_lettre_type($destinataire, $expediteur, 5);
					}
				}else{
					if (isset($_POST['email'])){
						$destinataire=$_POST['email'];
					}
					if ($destinataire=="" AND isset($_POST['responsable_email'])){
						$destinataire=$_POST['responsable_email'];
					}
					$expediteur="";
					$success=envoyer_lettre_type($destinataire, $expediteur, $id_email_envoyer, $id_insert_dyn);
				}
					
				if(!$success){
					echo '<div class="msg-box alerte_erreur">'.$langue["email_err_envoi"].' '.$id_email_envoyer.'</div>';
				}
			}
		}
		
		/* Si messages d'erreurs, on l'affiche */
		if (!empty($message_succes)){
			echo '<div class="msg-box alerte_valide">'.$message_succes.'</div>';
		}
		
		if (isset($_POST['ajax'])){
			echo '<button class="bouton fermer-fenetre">Fermer cette fenêtre</button>';
		}

	// $tab_alertes contient des erreurs
	}else{
		//affiche les erreurs au début
		//foreach ($tab_alertes as $alerte) echo '<div class="msg-box alerte_erreur">'.$alerte.'</div>';
		$formulaire_success=false;
	}
}
//traitement des formulaires specifiques en cas de succes
if ($formulaire_success){
	switch ($id_formulaire){
		case 11://demande modifier ma fiche
		break;
	}
}
/*
Fin traitement formulaire
*/

/*	if ($_POST["ajax"]=="1") {
		if ($formulaire_success==false){

		}else
		{
			echo 'valid=Valide ajax';
		}

		
		return;
	}
	*/

/*
Affichage du formulaire
*/
if ($formulaire_success==false OR empty($formulaire_success)){
	echo '
		<form name="nom_form" method="POST" class="formdyn" id="nom_form'.$ajaxable.'" action="'.$_SERVER["REQUEST_URI"].'">';
	$i=0;
	foreach ($json_a as $num_question=>$tab_question){
	
		foreach ($tab_question as $type_question=>$question){
			if (!empty($question['name'])){
				$name=$question['name'];
			}else{
				$name='';
			}
			
			if (empty($name)){
				$name='name_'.$num_question;
			}
			
			if (!empty($question['label'])){
				$label=$question['label']; 
			}else{
				$label='';
			}
			
			if (!empty($question['commentaires'])){
				$commentaire=$question['commentaires']; 
			}else{
				$commentaire='';
			}
			
			if (!empty($question['class'])){
				$class=$question['class']; 
			}else{
				$class='';
			}
			
			if (!empty($question['default'])){
				$default=$question['default'];
			}else{
				$default='';
			}
			
			if(isset($question['limit'])){
				$limit=$question['limit'];
			}else{
				$limit=0;
			}
			
			//LES CHAMPS PAR DEFAUT
			//permet  de récupérer les champs GET et SESSION
			if (isset($default[0]) AND $default[0]=='#'){
			 	$default=$_GET[substr($question['default'],1)];
			}else{
				if (isset($default[0]) AND $default[0]=='%'){//SESSION
					$defaulta=substr($question['default'],1);
					$default=$_SESSION[$defaulta];
				}else{
					if (isset($default[0]) AND $default[0]=='_'){
						if ($default=='_aujourdhui'){
							$default=date('Y-m-d');
						}
						if ($default=='_maintenant'){
							$default=date('H:i:s');
						}
					}
				}
			}
			
			//dans espace perso permet de récupérer les valeurs du select
			if (isset($tab_deja[$name])){
				$default=$tab_deja[$name];
			}
			//cas particulier des données pré-remplies dans "modifier ma fiche"
			
			//si envoi formulaire, on réaffciihe les valeurs envoyées
			if (isset($_POST[$name])){
				$default=$_POST[$name];
			}
			//recuperation des données dans le cas d'un UPDATE
			
			switch ($type_question){
			case 'html':
				echo $question['content'];
			break;
			
			case 'hidden':
				echo '<input type="hidden" name="'.$name.'" value="'.$default.'" />';
			break;
			
			case 'submit':
				echo '<div class="formdyn_submit"><input type="hidden" name="formdyn" value="bref" /><input type="submit" class="bouton" value="'.$question['content'].'" /></div>';
			break;
			
			case 'date':
				if (!empty($class)){
					$classforce=$class.' '; 
				}else{
					$classforce='';
				}
				$class=' class="'.$classforce.'date_picker" onclick="displayCalendar(document.getElementById(\'quf_'.$num_question.'\'),\'dd/mm/yyyy\',this)" ';
				
				if (empty($default)){
					$default=date('Y-m-d');
				}
				echo '
					<div class="fdynel">
					<label for="quf_'.$num_question.'">'.$label.''.$commentaire.'</label>
					<input type="text"'.$class.' name="'.$name.'" id="quf_'.$num_question.'" value="'.dateus2fr($default).'" />';
					
				echo '</div>';
			
			break;
			
			case 'text':
				if (!empty($class)){
					$class=' class="'.$class.'"'; 
				}else{
					$class='';
				}
				echo '
					<div class="fdynel">
					<label for="quf_'.$num_question.'">'.$label.''.$commentaire.'</label>
					<input type="text"'.$class.' name="'.$name.'" id="quf_'.$num_question.'" value="'.$default.'" />';
					if (!empty($commentaire))echo '<span class="commentaire_form">'.$commentaire.'</span>';
				echo '</div>';
			break;
			
			case 'password':
				if (!empty($class)){
					$class=' class="'.$class.'"';
				}else{
					$class='';
				}
				echo '
					<div class="fdynel">
					<label for="quf_'.$num_question.'">'.$label.''.$commentaire.'</label>
					<input'.$class.' type="password" name="'.$name.'" id="quf_'.$num_question.'" value="'.$default.'" />';
				
				echo '</div>';
			break;
			
			case 'textarea':
				echo '
					<div class="fdynel">
					<label for="quf_'.$num_question.'">'.$label.''.$commentaire.'</label>';
				if($limit==0){
					echo '<textarea name="'.$name.'" id="quf_'.$num_question.'">'.$default.'</textarea>';
				}else{
					echo '<textarea name="'.$name.'" id="quf_'.$num_question.'" class="limited">'.$default.'</textarea>';
					echo '<div class="limit-count '.$limit.'">'.$limit.'</div>';
				}
				
				echo '</div>';
			break;
			
			case 'radio':
				$tab_options_dispo=$question['valeurs'];
				echo '
					<div class="fdynel">
					<label for="quf_'.$num_question.'">'.$label.''.$commentaire.'</label>';
					
				$j=0;
				foreach ($tab_options_dispo as $cle=>$value){
					$j++;
					if ($value==$default){
						$checked='checked ';
					}else{
						$checked='';
					}
					echo '
						<div class="fdynel">
							<label for="quf_'.$i.'_'.$j.'">'.$cle.'</label>
							<input type="radio" name="'.$name.'" value="'.$value.'" id="quf_'.$i.'_'.$j.'" '.$checked.'/>
						</div>';
				}
					
				echo '</div>';
			break;
			
			case 'select':
				if (isset($question['valeurs'])){
					$tab_options_dispo=$question['valeurs'];
				}
				if (empty($tab_options_dispo)){
					$sql_temp=$question['valeurs_from'];
					$reponse_temp=$db->query($sql_temp);
					while ($donnees_temp=$reponse_temp->fetch_array(MYSQLI_BOTH)) $tab_options_dispo[$donnees_temp[1]]=$donnees_temp[0];
				}
				
				echo '
					<div class="fdynel">
					<label for="quf_'.$num_question.'">'.$label.''.$commentaire.'</label>';
					
				$j=0;
				echo '<select name="'.$name.'" id="quf_'.$num_question.'">';
				foreach ($tab_options_dispo as $cle=>$value){
					$j++;
					if ($value==$default){
						$selected=' selected="selected" ';
					}else{
						$selected='';
					}
					echo '
						<option value="'.$value.'"'.$selected.'>'.$cle.'</option>';
				}
				echo '</select>';
				echo '</div>';
			break;
			
			
			case 'checkbox':
				$tab_options_dispo=$question['valeurs'];
				echo '
					<div class="fdynel">
					<label for="quf_'.$num_question.'">'.langue($label,0).''.$commentaire.'</label>';
					
				$j=0;
				foreach ($tab_options_dispo as $cle=>$value){
					$j++;
					if ($value==1) $print_checked=' checked'; else $print_checked=' ';
					echo '
						<div style="display:inline-block">
						<input type="checkbox" name="'.$name.'[]" value="'.$value.'"'.$print_checked.' id="quf_'.$i.'_'.$j.'" />
						<label class="inl" for="quf_'.$i.'_'.$j.'">'.$cle.'</label>
						</div>';
				}
				
				echo '</div>';
			break;
			
			case 'meta_options':
				$tab_options_dispo=$question['valeurs'];
				echo '
					<div class="fdynel">
					<div class="form_header">'.$label.''.$commentaire.'</div>';
				$j=0;
			
				foreach ($tab_options_dispo as $value){
					if (in_array($value, $tab_deja_options)){
						$checked=' checked'; 
					}else{
						$checked=false;
					}
					$j++;
					echo '
						<div style="display:inline-block">
						<input type="checkbox"'.$checked.' name="meta_options[]" value="'.$value.'" id="quf_'.$i.'_'.$j.'" />
						<label class="inl" for="quf_'.$i.'_'.$j.'">'.$trabduction_options[$value]['valeur'].'</label>
						</div>';
				}
				echo '</div>';
				
			break;
			
			case 'meta_labels':
				$tab_labels_dispo=$question['valeurs'];
				echo '
					<div class="fdynel">
					<div class="form_header">'.$label.''.$commentaire.'</div>';
				
				$j=0;
			
				foreach ($tab_labels_dispo as $value){
					if (in_array($value, $tab_deja_options)){
						$checked=' checked';
					}else{
						$checked=false;
					}
					$j++;
					echo '
						<div style="display:inline-block">
						<input type="checkbox"'.$checked.' name="meta_labels[]" value="'.$value.'" id="quf_'.$i.'_'.$j.'" />
						<label class="inl" for="quf_'.$i.'_'.$j.'">'.$trabduction_labels[$value].' <img src="../img/labels/'.($value-100).'.jpg" alt="'.$trabduction_labels[$value].'" height="40" /></label>
						</div>';
				}
				echo '</div>';
				
			break;
			
			case 'tarifs':
				include 'formulaires/tarifs.php';
			break;
			
			case 'date_maj':
				echo '<input type="hidden" name="date_maj" id="date_maj" value="'.date("Y-m-d").'" />';
			break;
			
			case 'photos':
				$niveau_dossier='';
				$gestion="fiche";
				
				if($id_formulaire==14){
					$gestion="avatar";
					$nom_table="";
					$id_table=0;
					echo '<div class="include-upload-photos">';
						include 'blocs/upload-photo.php';
					echo '</div>';
				}else{
					if(isset($_GET['id_table'])){
						$id_table=secure_id($_GET['id_table']);
						$nom_table=secure_table($_GET['nom_table']);
						echo '<div class="include-upload-photos">';
							include 'blocs/upload-photo.php';
						echo '</div>';
					}
				}
			break;
			
			case 'recommandations':
				if(isset($_GET['id_table'])){
					$id_table=secure_id($_GET['id_table']);
					$nom_table=secure_table($_GET['nom_table']);
					include 'formulaires/recommandations.php';
				}
			break;
			
		}
		if (!empty($tab_alertes[$name])){
			echo '<div class="msg-box alerte_erreur">'.$tab_alertes[$name].'</div>';
		}
	}
	
	$i++;
	}
	if (!empty($tab_alertes)){
		echo '<div class="msg-box alerte_erreur">'. $langue['form_err_general'] .'</div>';
	}
	echo '</form>';
}
?>
