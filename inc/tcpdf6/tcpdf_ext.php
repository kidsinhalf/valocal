<?php
// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {
    //Page header
    public function Header() {
    $this->Image('../img/logo.png',17,0,45);
    //Police Arial gras 15
    $this->SetFont('Arial','I',15);
    //Décalage à droite
    //$this->Cell(80);
    //Titre
    //$this->Cell(0,10,'Contrat de formation professionnelle',0,0,'C');
    //Saut de ligne
    //$this->Ln(20);
    }
    
    // Page footer
    public function Footer() {
        //Positionnement à 1,5 cm du bas
    //if ($this->PageNo()>1) $this->Image('images/signature.jpg',190,280,8);
	$this->SetY(-25);
    //Police Arial italique 8
    $this->SetFont('Arial','I',7);
	//pied de page
    $this->Cell(0,10,'SCIC Valocal - 12 rue Notre-Dame, 91450 Soisy-sur-Seine',0,0,'C');
    $this->Ln(3);
	$this->Cell(0,10,'Courriel: contact@valocal.fr - Site Internet : www.valocal.fr - Téléphone : en cours',0,0,'C');
    
	
    //Numéro de page
    $this->Cell(0,10,'Page '.$this->PageNo().'/1',0,0,'C');
    }
	
	
		public function ColoredTable($header,$data) {
		// Colors, line width and bold font
		$this->SetFillColor(255, 0, 0);
		$this->SetTextColor(255);
		$this->SetDrawColor(128, 0, 0);
		$this->SetLineWidth(0.3);
		$this->SetFont('', 'B');
		// Header
		$w = array(40, 35, 40, 45);
		for($i = 0; $i < count($header); $i++)
		$this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
		$this->Ln();
		// Color and font restoration
		$this->SetFillColor(224, 235, 255);
		$this->SetTextColor(0);
		$this->SetFont('');
		// Data
		$fill = 0;
		foreach($data as $row) {
			$this->Cell($w[0], 6, $row, 'LR', 0, 'L', $fill);
			$this->Cell($w[1], 6, $row, 'LR', 0, 'L', $fill);
			$this->Ln();
			$fill=!$fill;
		}
		$this->Cell(array_sum($w), 0, '', 'T');
	}
}
?>