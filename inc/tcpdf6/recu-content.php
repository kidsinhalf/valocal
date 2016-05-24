<?php
//ex numero facture F09MMJJ-n
$annee=date('Y');
$numero_recu='R'.$annee.'-'.$id_achats;
$txt1='';

$txt_nous='<strong style="font-size:150%">S.C.I.C. VALOCAL</strong>
<br />12 rue Notre-Dame<br />91450<br />Soisy-sur-Seine';

$txt_eux='
<h2>Vendeur</h2>
<strong style="font-size:150%">'.$nom.'</strong>
<br />'.$adresse.'
<br />'.$cp.' '.$ville.'
<br />SIRET';

$txt_divers='Autoliquidation de TVA';

$txt_tva='TVA Due par l\'Acquéreur. Article 283.2 sexies du C.G.I.';

$txt_divers.='<br />'.$txt_tva;

$txt1='
<h1 style="text-align:center">
Reçu de vente
</h1>
';
//if ($type_cotisation!='mecenat') $txt1.='<br />Nom : '.$civilite.' <strong>'.$nom.' '.$prenom.'</strong>';
//else $txt1.='<br />Nom organisme : <strong>'.$organisme.'</strong>';
$txt1.='
</p>
<table style="border:solid 1px #bebebe;padding:10px;">
<tr>
	<th style="border: solid 1px #bebebe; padding:5px">Produit</th>
	<th style="border: solid 1px #bebebe; padding:5px">Quantité</th>
	<th style="border: solid 1px #bebebe; padding:5px">Prix unitaire</th>
	<th style="border: solid 1px #bebebe; padding:5px">Total</th>
</tr>
<tr>
	<td style="border: solid 1px #bebebe; padding:5px">'.$donnees['categorie_params'].'</td>
	<td style="border: solid 1px #bebebe; padding:5px">'.$donnees['poids'].' kg</td>
	<td style="border: solid 1px #bebebe; padding:5px">'.($donnees['taux_achat']/1000).' €/kg</td>
	<td style="border: solid 1px #bebebe; padding:5px"><strong>'.$prix_ht.' €</strong></td>
</tr>
</table>';

$txt1.='
<p>
<br /><br />
Fait à Corbeil-Essonnes le '.date_us2fr($donnees['date']).'
</p>
';



?>