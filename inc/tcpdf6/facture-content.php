<?php
//ex numero facture F09MMJJ-n
$annee=date('Y');
$numero_facture='R'.$annee.'-'.$id_ventes;
$txt1='';

$txt_nous='<strong style="font-size:150%">S.C.I.C. VALOCAL</strong>
<br />12 rue Notre-Dame<br />91450<br />Soisy-sur-Seine';

$txt_eux='
<strong style="font-size:150%">'.$nom.'</strong>
<br />'.$adresse.'
<br />'.$cp.' '.$ville.'
<br />SIRET';

$txt_divers='TVA 20%';

$tva=$prix_ht*0.2;
$prix_ttc=$prix_ht+$tva;


$txt1='
<h1 style="text-align:center">
Facture
</h1>
';
//if ($type_cotisation!='mecenat') $txt1.='<br />Nom : '.$civilite.' <strong>'.$nom.' '.$prenom.'</strong>';
//else $txt1.='<br />Nom organisme : <strong>'.$organisme.'</strong>';
$txt1.='
</p>
<table style="border:solid 1px #bebebe;padding:10px;">
<tr>
	<th style="border: solid 1px #bebebe; padding:5px">Produit</th>
	<th style="border: solid 1px #bebebe; padding:5px">Quantit�</th>
	<th style="border: solid 1px #bebebe; padding:5px">Prix unitaire</th>
	<th style="border: solid 1px #bebebe; padding:5px">Total</th>
</tr>
<tr>
	<td style="border: solid 1px #bebebe; padding:5px">'.$donnees['categorie_params'].'</td>
	<td style="border: solid 1px #bebebe; padding:5px">'.$donnees['poids'].' kg</td>
	<td style="border: solid 1px #bebebe; padding:5px">'.$donnees['cours'].'</td>
	<td style="border: solid 1px #bebebe; padding:5px"><strong>'.$prix_ht.' �</strong></td>
</tr>

<tr>
	<td style="border: solid 1px #bebebe; padding:5px"></td>
	<td style="border: solid 1px #bebebe; padding:5px"></td>
	<td style="border: solid 1px #bebebe; padding:5px">TOTAL HT</td>
	<td style="border: solid 1px #bebebe; padding:5px"><strong>'.$prix_ht.' �</strong></td>
</tr>

<tr>
	<td style="border: solid 1px #bebebe; padding:5px"></td>
	<td style="border: solid 1px #bebebe; padding:5px"></td>
	<td style="border: solid 1px #bebebe; padding:5px">TVA 20%</td>
	<td style="border: solid 1px #bebebe; padding:5px"><strong>'.number_format($tva, 2).' �</strong></td>
</tr>

<tr>
	<td style="border: solid 1px #bebebe; padding:5px"></td>
	<td style="border: solid 1px #bebebe; padding:5px"></td>
	<td style="border: solid 1px #bebebe; padding:5px">TOTAL TTC</td>
	<td style="border: solid 1px #bebebe; padding:5px"><strong>'.number_format($prix_ttc,2).' �</strong></td>
</tr>
</table>


';

$txt1.='
<p>
<br /><br />
Fait � Corbeil-Essonnes le '.date_us2fr($donnees['date']).'
</p>
';



?>