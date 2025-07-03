<?php

namespace App\Pdf;

use TCPDF;

class EtatCateg extends TCPDF
{
    protected $categorie;

    public function __construct($categorie)
    {
        parent::__construct('P', 'mm', 'A4', true, 'UTF-8', false);
        $this->categorie = $categorie;
        // Configuration du PDF
        $this->SetCreator('Laravel App');
        $this->SetAuthor('Système de Gestion');
        $this->SetTitle($this->categorie);
        $this->SetSubject('État des employés');
        $this->SetKeywords('employés, état, PDF');
        
        // Marges
        $this->SetMargins(15, 20, 15);
        $this->SetHeaderMargin(5);
        $this->SetFooterMargin(10);
        $this->SetAutoPageBreak(true, 25);
    }

    // Header personnalisé
    public function Header()
    {
        // Logo ou en-tête entreprise
       if($this->page == 1) {
        $this->SetFont('helvetica', 'B', 14);
        $this->Cell(0, 0, 'Liste des employés categorie: '.$this->categorie, 0, 1, 'C');
        $this->SetXY(6,14);
        $this->SetFont('helvetica', 'B', 9);
        $this->Cell(15, 6, 'N°', 1, 0, 'C', false);
        $this->Cell(45, 6, 'Nom et Prénom', 1, 0, 'C', false);
        $this->Cell(25, 6, 'Fonction', 1, 0, 'C', false);
        $this->Cell(20, 6, 'N° CNSS', 1, 0, 'C', false);
        $this->Cell(20, 6, 'CIN', 1, 0, 'C', false);
        $this->Cell(25, 6, 'Date Embauche', 1, 0, 'C', false);
        $this->Cell(25, 6, 'Telephone', 1, 0, 'C', false);
     } 
        
    }

    // Footer personnalisé
    public function Footer()
    {
        $this->SetY(-15);
        // Numéro de page
        $this->SetFont('helvetica', 'I', 8);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . ' / ' . $this->getAliasNbPages(), 0, 0, 'C');
        
        // Date et heure de génération
        $this->SetX(15);
        $this->Cell(0, 10, 'Généré le ' . date('d/m/Y à H:i'), 0, 0, 'L');
    }

}
