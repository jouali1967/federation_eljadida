<?php

namespace App\Pdf;

use TCPDF;

class SalaireBanquePdf extends TCPDF
{
  protected $dateVirement;
  protected $annee;
  protected $rib;
  protected $first_signataire;
  protected $second_signataire;
  protected $categ_id;

  // Constructor to receive the data
  public function __construct($rib, $first_signataire, $second_signataire, $dateVirement,$categ_id, $orientation = 'P', $unit = 'mm', $format = 'A4', $unicode = true, $encoding = 'UTF-8', $diskcache = false)
  {
    parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache);
    $this->categ_id = $categ_id; 
    $this->rib = $rib; 
    $this->first_signataire = $first_signataire;
    $this->second_signataire = $second_signataire;
    $this->dateVirement = $dateVirement;
  }
  // Header personnalisé
  public function Header()
  {
    if ($this->getPage() === 1) {
      $this->SetFont('helvetica', 'B', 9);
      $this->SetXY(4, 6);
      $entete=$this->categ_id == 4 ? "DAR ATALIBA MY ABDELLAH":"Fédération des associations"; 
      $this->Cell(0, 0, $entete, 0, 1);
      if($this->categ_id == 1){
        $this->Cell(0, 0, 'mly abdellah', 0, 1);
      }
      $this->SetX(110);
      $this->Cell(120, 0, 'Mly Abdellah le :' . date('d/m/Y'), 0, 1);
      $this->SetX(145);
      $this->Cell(1, 0, 'A', 0, 1);
      $this->SetX(130);
      $this->Cell(60, 0, 'Monsieur,le Directeur de la BP', 0, 1);
      $this->SetX(130);
      $this->Cell(60, 0, 'Sidi Bouzid d\'El Jadida', 0, 0);
      $this->SetFont('helvetica', 'U', 10);
      $this->SetXY(4, 35);
      $this->Cell(0, 0, 'Objet :', 0, 0);
      $this->SetFont('times', '', 10);
      $this->setX(15);
      $this->Cell(0, 0, 'Demande de Virement', 0, 1);
      $this->SetFont('times', '', 10);

      $text1 = "Nous avons L'honneur de vous demander de bien vouloir virer de notre compte ";
      $text2 = $this->rib;
      $text3 = " de la fédération des";
      $textd=" de L'ASSOCIATION";
      $this->Cell($this->GetStringWidth($text1), 0, $text1, 0, 0);
      $this->SetFont('times', 'B', 10);
      $this->Cell($this->GetStringWidth($text2), 0, $text2, 0, 0);
      $this->SetFont('times', '', 10);
      if($this->categ_id == 4){
        $this->Cell(0, 0, $textd, 0, 1);
      }else{
        $this->Cell(0, 0, $text3, 0, 1);
      }
      $this->SetX(4);
      if($this->categ_id == 4){
        $this->Cell(0, 0, ' DAR TALIBA MY ABDELLAH Veuillez créditer les comptes', 0, 1);
      }else{
        $this->Cell(0, 0, 'Associations Mly Abdellah Veuillez créditer les comptes', 0, 1);
      }
      $this->SetX(4);
      $text4 = "ci-aprés,à partir de ";
      $text5 = $this->dateVirement;
      $text6 = " et jusqu'a nouvel ordre.";
      $this->Cell($this->GetStringWidth($text4), 0, $text4, 0, 0);
      $this->SetFont('times', 'B', 10);
      $this->Cell($this->GetStringWidth($text5), 0, $text5, 0, 0);
      $this->SetFont('times', '', 10);
      $this->Cell(0, 0, $text6, 0, 1);
      $this->Ln();
      $this->SetX(4);
      $this->SetFont('times', 'B', 9);
      $this->SetX(6);
      $this->MultiCell(15, 7, "N°", 1, 'C', 0, 0, null, null, true, 0, false, true, 6, 'M');
      $this->MultiCell(85,  7, "Nom et Prénom", 1, 'C', 0, 0, null, null, true, 0, false, true, 6, 'M');
      $this->MultiCell(50, 7, "Numero de Compte", 1, 'C', 0, 0, null, null, true, 0, false, true, 6, 'M');
      $this->MultiCell(50, 7, "Montant", 1, 'C', 0, 0, null, null, true, 0, false, true, 6, 'M');
      $this->ln();
    }
  }

  // Footer personnalisé
  public function Footer()
  {
    $this->setY(-19);
    $this->setFontSize(9);
    $this->cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 0, 'C');
  }
}
