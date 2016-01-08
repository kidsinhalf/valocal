<?php
$id_inscriptions=$_GET['id'];
if ((isset($_SESSION['utilisateur']) AND isset($_SESSION['utilisateur']['id']) AND $_SESSION['utilisateur']['id']>0) OR (isset($_GET['hash']) AND $_GET['hash']=='to6fG9i7u6x44')){
	if (isset($_GET['hash']) AND $_GET['hash']=='to6fG9i7u6x44'){
		$reponse=mysql_query('SELECT * FROM inscriptions WHERE id='.$id_inscriptions);
		}
	else{
		$reponse=mysql_query('SELECT * FROM inscriptions WHERE id='.$id_inscriptions.' AND id_personnes='.$_SESSION['utilisateur']['id']);
		}

if (mysql_num_rows($reponse)){
$donnees=mysql_fetch_array($reponse);
$id_personnes=$donnees['id_personnes'];
if ($donnees['id_activites']>0){
	$table_prestactivites='activites';
	$id_prestactivites=$donnees['id_activites'];
	}
if ($donnees['id_prestations']>0){
	$table_prestactivites='prestations';
	$id_prestactivites=$donnees['id_prestations'];
	}

$reponse_personnes=mysql_query('SELECT * FROM personnes WHERE id='.$id_personnes);
$donnees_personnes=mysql_fetch_array($reponse_personnes);

$reponse_activites=mysql_query('SELECT * FROM '.$table_prestactivites.' WHERE id='.$id_prestactivites);
$donnees_prestactivites=mysql_fetch_array($reponse_activites);

$tab_participants=array();
$reponse_partcipants=mysql_query('SELECT * FROM inscriptions_invites WHERE id_inscriptions='.$id_inscriptions);
while ($donnees_participants=mysql_fetch_assoc($reponse_partcipants)){
	$tab_participants[]=array('nom'=>$donnees_participants['nom'], 'prenom'=>$donnees_participants['prenom']);
	}
	
$reponse_recettes=mysql_query('SELECT * FROM recettes WHERE id_inscriptions='.$id_inscriptions);
$sum_recettes=0;
while ($donnees_recettes=mysql_fetch_assoc($reponse_recettes)){
	if ($donnees_recettes['date']!='0000-00-00') $sum_recettes+=$donnees_recettes['montant'];
	$last_date=$donnees_recettes['date'];
	}




list($annee,$mois,$jour)=explode('-',$donnees['date']);

include 'tcpdf/content-inscription-facture.php';

require_once('config/lang/fr.php');
require_once('tcpdf.php');
require_once('tcpdf_ext.php');
// Colored table


// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, false, 'ISO-8859-1', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Association La Tracce');
$pdf->SetTitle('Facture Inscription La Trace');
$pdf->SetSubject('Facture La Trace');
$pdf->SetKeywords('La  Trace, livre');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); 

//set some language-dependent strings
$pdf->setLanguageArray($l); 

// ---------------------------------------------------------

$pdf->SetFont('times', 'B', 17);
// add a page
$pdf->AddPage();
$pdf->SetTextColor(190);
$pdf->SetY(5);
//$pdf->Write(10, 'Facture Inscription LT_'.$donnees['id'],'',0, 'R',true);
/*écriture "ACQUITTEE"
if ($donnees['paiement']==1){
$pdf->SetFont('times', '', 49);
$pdf->SetY(10);
$pdf->SetTextColor(250,0,0);
$pdf->Write(10, 'ACQUITTE','',0, 'R',true);
}
*/
$pdf->SetFont('times', '', 10);
$pdf->SetY(30);
$pdf->SetTextColor(0);



//trois colonnes
$pdf->writeHTML($txt1);



// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('Facture-LaTrace-'.$annee.'.pdf', 'I');

}
else echo 'echec identification';
}
else echo 'identification obligatoire';
?>