<?php

namespace App\Pdf;

use TCPDF;

class EtatEmployesPdf extends TCPDF
{
    protected $etablissment;
    protected $titre;
    protected $date_edition;

    public function __construct($titre , $etablissment)
    {
        parent::__construct('P', 'mm', 'A4', true, 'UTF-8', false);
        $this->etablissment = $etablissment;
        $this->titre = $titre;
        $this->date_edition = date('d/m/Y');
        
        // Configuration du PDF
        $this->SetCreator('Laravel App');
        $this->SetAuthor('Système de Gestion');
        $this->SetTitle($this->titre);
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
        $this->Cell(0, 0, $this->titre, 0, 1, 'C');
        $this->Ln(2);
        $this->SetFont('helvetica', 'B', 12);
        $this->Cell(0, 0, "État des Employés", 0, 1, 'C');
        $this->SetXY(5,20);
        $this->SetFont('helvetica', 'B', 9);
        $this->Cell(15, 8, 'N°', 1, 0, 'C', false);
        $this->Cell(45, 8, 'Nom et Prénom', 1, 0, 'C', false);
        $this->Cell(25, 8, 'Fonction', 1, 0, 'C', false);
        $this->Cell(25, 8, 'Categorie', 1, 0, 'C', false);
        $this->Cell(20, 8, 'N° CNSS', 1, 0, 'C', false);
        $this->Cell(20, 8, 'CIN', 1, 0, 'C', false);
        $this->Cell(25, 8, 'Date Embauche', 1, 0, 'C', false);
        $this->Cell(25, 8, 'Telephone', 1, 0, 'C', false);
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

    // Méthode pour créer l'en-tête du tableau
    // public function createTableHeader()
    // {
    // }

    // Méthode pour ajouter une ligne de données

 

    // Méthode pour ajouter les signatures
    public function addSignatures($nom1 = '', $nom2 = '' ) 
    {
        $this->Ln(15);
        
        // Signatures
        $this->SetFont('helvetica', 'B', 10);
        $this->Cell(90, 6, 'Le Directeur', 0, 0, 'C');
        $this->Cell(90, 6, 'Le Responsable RH', 0, 1, 'C');
        
        $this->Ln(15);
        
        $this->SetFont('helvetica', 'U', 10);
        $this->Cell(90, 6, mb_strtoupper($nom1, 'UTF-8'), 0, 0, 'C');
        $this->Cell(90, 6, mb_strtoupper($nom2, 'UTF-8'), 0, 1, 'C');
    }
}
