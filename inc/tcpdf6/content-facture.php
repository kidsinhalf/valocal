<?php
//ex numero facture F09MMJJ-n


$tva=0;
$montant_ht=$donnees['montant_attendu'];
$montant_tva=$tva*$montant_ht;
$montant_ttc=$tva+$montant_ht;


$txt1='
<p style="text-align:left">
Association La Trace
<br />Gîte des Ecouges
<br />38470 SAINT-GERVAIS
<br />04 76 64 73 45<br />
<br />N°SIRET: 328 932 157 000 30<br />N° URSSAF 380 144 106 187 49';
if (!empty($donnees_prestactivites['agrement']) AND $donnees_prestactivites['agrement']!=NULL) $txt1.='<br />N° Agrement '.$donnees_prestactivites['agrement'];
$txt1.='
</p>';

//$num_facture=$rang_facture.' '.$mois.substr($annee,2,2);
$num_facture=$donnees['numero_facture'];

if ($donnees_prestactivites['nb_jours']>1) $pluriel='s';else $pluriel='';

$txt1.='

<p style="text-align:right">
<strong>'.$donnees_personnes['nom'].' '.$donnees_personnes['prenom'].'</strong>
<br />'.$donnees_personnes['adresse'].'
<br />'.$donnees_personnes['cp'].' '.$donnees_personnes['ville'].'
</p>


<h1>Facture '.$num_facture.'</h1>

<h3>'.$donnees_prestactivites['titre'].'</h3>
<p>'.utf8_decode(convertir_2_dates($donnees_prestactivites['date_debut'] , $donnees_prestactivites['date_fin'])).'</p>
<p>Soit '.$donnees_prestactivites['nb_jours'].' journée'.$pluriel.'.</p>

<p>Pour :</p>';
foreach ($tab_participants as $participant){
	$txt1.= '<p>- '.$participant['prenom'].' '.$participant['nom'].'</p>';
	}

	if (!empty($donnees_prestactivites['complement_texte_facture'])) $txt1.='<div>'.$donnees_prestactivites['complement_texte_facture'].'</div>';

$txt1.='
<br /><strong>Montant Total</strong>: <strong>'.number_format($montant_ttc,2).' €</strong> (dont 0,00 € de TVA)
<br />Non assujetti à la TVA
<br />
<br />';

if ($sum_recettes==$montant_ttc) $txt1.='Réglée à ce jour.<br /><br />Montant payé le '.utf8_decode(convertir_date($last_date));
else{
	$txt1.='En attente du règlement';
	if ($sum_recettes>0) $txt1.='<br />Déjà Payé : '.number_format($sum_recettes,2).' €';
	}

$txt1.='<br />';
/*
$txt1.='
<br />Montant HT ('.$tva.'%): '.number_format($montant_ht,2).' €
<br />Montant TVA : '.number_format($montant_tva,2).' €';
*/
$txt1.='
Facture certifiée conforme.<br />
</p>
<p>
Karine Garcin, Directrice de l\'association
</p>



<div>Fait à Saint-Gervais le '.utf8_decode(convertir_date($donnees['date'])).'</div>
<img src="images/signature-Karine2.jpg" width="230" />

<br /><br/>

<p style="font-size:15px">
Tout retard de paiement entraîne une indemnité de compensation des frais de recouvrement de 40 euros HT ainsi que des pénalités de retard au taux de 10,75 % par an. 
<br />Articles L 441-3 et L 441-6 du Code du Commerce.
</p>
';



?>