<?php
echo '
		
		<p>Achat = Emettre un reçu</p>
		<p>Vente = Emettre une facture</p>';
		
		
			echo '<table>';
			
			echo '<tr>
				<td>Cours du cuivre</td>
				<td><input id="cours_theorique" readonly value="'.$cours_default.'" /></td>
				<td>&nbsp;€/t</td>
				</tr>';

			
			echo '<tr><td>Cours pratique</td><td><input id="cours_pratique" readonly value="" /></td><td>€/kg</td></tr>';
			echo '<tr><td>Prix HT</td><td><span id="apercu_ht"></span></td><td> € </td></tr>';
			echo '<tr><td>Prix TTC</td><td><span id="apercu_ttc"></span></td><td> €</td></tr>';
			
			echo '</table>';
			
			echo '<div class="button tiny" onclick="calcul_taux(\''.$table.'\'); calcul_prix_'.$table.'()">Calculer</div>';
?>


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

function calcul_taux(table){
	var cours_theorique=$('input[id="cours_theorique"]').val();
	
	var categorie=$('select[name="categorie_params"]').val();
	switch (categorie){
		<?php
		foreach ($tab_cuivres_achat as $nom_cuivre=>$pourcentage_interne){
			echo '
			case \''.$nom_cuivre.'\': var qualite = '.$pourcentage_interne.'; break;';
			}
			
		foreach ($tab_cuivres_vente as $nom_cuivre=>$pourcentage_interne){
			echo '
			case \''.$nom_cuivre.'\': var qualite = '.$pourcentage_interne.'; break;';
			}
		?>
		}

	var taux=(qualite/100)*cours_theorique;
	//alert(taux);
	switch (table){
		case 'achats' :$('input[name="taux_achat"]').val(taux); break;
		case 'ventes' :$('input[name="taux_vente"]').val(taux); break;
		
		}
	$('input[id="cours_pratique"]').val(taux);
	}

	
//ACHAT
function calcul_prix_achats(){
	var poids=$('input[name="poids"]').val();
	var taux=$('input[name="taux_achat"]').val();
	
	var prix_tonne=poids*taux;
	var prix_ht=(prix_tonne/1000).toFixed(2);
	var prix_ttc=(prix_ht*1).toFixed(2);//pas de TVA

	$('#apercu_ht').text(prix_ht);
	$('#apercu_ttc').text(prix_ttc);
	
	$('input[name="prix_ht"]').val(prix_ht);
	}
	
//VENTE
function calcul_prix_ventes(){
	var poids=$('input[name="poids"]').val();
	var taux=$('input[name="taux_vente"]').val();
	
	var prix_tonne=poids*taux;
	var prix_ht=(prix_tonne/1000).toFixed(2);
	var prix_ttc=(prix_ht*1).toFixed(2);//pas de TVA

	$('#apercu_ht').text(prix_ht);
	$('#apercu_ttc').text(prix_ttc);
	
	$('input[name="prix_ht"]').val(prix_ht);
	}


</script>