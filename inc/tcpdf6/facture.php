<?php
$id=intval($_GET['id']);
$reponse=db_select('SELECT * FROM ventes WHERE id=?', array($id));
if (!empty($reponse)){
$donnees=array_map('utf8_decode',$reponse[0]);
$id_ventes=$donnees['id'];
$id_personnes=$donnees['client'];
$prix_ht=$donnees['prix_ht'];
$date=date_us2fr($donnees['date']);

$reponse_personnes=db_select('SELECT * FROM clients WHERE id=?', array($id_personnes), true);
if (!empty($reponse_personnes)){
	//ici pb encodage UTF8
	$donnees_personnes=array_map('utf8_decode', $reponse_personnes[0]);
	//$donnees_personnes=$reponse_personnes[0];//Si pas de pb encodage...
	
	$nom=$donnees_personnes['nom'];
	$prenom=$donnees_personnes['prenom'];
	$adresse=$donnees_personnes['adresse'];
	$cp=$donnees_personnes['cp'];
	$ville=$donnees_personnes['ville'];
	}



include 'facture-content.php';

//require_once('config/lang/fr.php');
require_once('tcpdf.php');
require_once('tcpdf_ext.php');
// Colored table


// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, false, 'ISO-8859-1', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('SCIC VALOCAL');
$pdf->SetTitle('Facture SCIC VALOCAL');
$pdf->SetSubject('Facture SCIC VALOCAL');
$pdf->SetKeywords('Facture SCIC VALOCAL');

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
//$pdf->setLanguageArray($l); 

// ---------------------------------------------------------

$pdf->SetFont('times', 'B', 17);
// add a page
$pdf->AddPage();
$pdf->SetTextColor(190);
$pdf->SetY(5);
$pdf->Write(10, 'Facture '.$id_ventes.' ','',0, 'R',true);
/*écriture "ACQUITTEE"
if ($donnees['paiement']==1){
$pdf->SetFont('times', '', 49);
$pdf->SetY(10);
$pdf->SetTextColor(250,0,0);
$pdf->Write(10, 'ACQUITTE','',0, 'R',true);
}
*/
$pdf->SetFont('times', '', 9);
$pdf->SetY(30);
$pdf->SetTextColor(0);



// set columns width
$first_column_width = 100;
$second_column_width = 60;

// get current vertical position
$current_y_position = $pdf->getY();

//trois colonnes
$pdf->writeHTMLCell($first_column_width, '', '', $current_y_position, $txt_nous, 1, 0, 0, true);
$pdf->writeHTMLCell($second_column_width, '', 133, '', $txt_eux, 1, 0, 0, true);
$pdf->writeHTMLCell($second_column_width, '', 133, 60, $txt_divers, 1, 1, 0, 'C', true);
$pdf->writeHTML($txt1);




// ---------------------------------------------------------

//Close and output PDF document
//$pdf->Output('C:\wamp\www\valocal\valocal\admin-sec\pdf\factures\facture_'.$id_ventes.'.pdf', 'F'); //F for saving output to file
$pdf->Output('/home/clients/fdd1a6019f1e8376cbae15369dc54de1/web/applis/admin-sec/pdf/recus/recu_'.$id_ventes.'.pdf', 'F'); //F for saving output to file
$pdf->Output('Facture-VALOCAL-'.$id_ventes.'.pdf', 'I');

}
?>