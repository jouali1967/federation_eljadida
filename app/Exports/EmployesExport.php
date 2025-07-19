<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class EmployesExport implements FromArray, WithHeadings, WithEvents
{
  protected $titre;
  protected $etablissement;
  protected $employes;

  public function __construct($employes, $titre, $etablissement)
  {
    $this->employes = $employes;
    $this->titre = $titre;
    $this->etablissement = $etablissement;
  }

  public function array(): array
  {
    $rows = [];
    $i = 1;
    foreach ($this->employes as $employe) {
      $rows[] = [
        $i++,
        $employe->nom . ' ' . $employe->prenom,
        $employe->fonction->libelle,
        $employe->categorie->libelle,
        $employe->num_cnss,
        $employe->cin,
        $employe->date_embauche,
        $employe->phone,
      ];
    }
    // Ajoute la ligne Total à la fin
    return $rows;
  }

  public function headings(): array
  {
    return [
      [$this->titre],
      ['État des Employés'],
      [],
      ['N°', 'Nom et Prénom', 'Fonction', 'Categorie', 'N° CNSS', 'CIN', 'Date Embauche', 'Telephone'],
    ];
  }

  public function registerEvents(): array
  {
    return [
      AfterSheet::class => function (AfterSheet $event) {
        // Setup A4 portrait
        $event->sheet->getPageSetup()->setFitToWidth(1);
        $event->sheet->getPageSetup()->setFitToHeight(0);
        $event->sheet->getPageSetup()->setOrientation(
          \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT
        );
        $event->sheet->getPageSetup()->setPaperSize(
          \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4
        );

        // Largeurs
        $event->sheet->getColumnDimension('A')->setWidth(6);   // N°
        $event->sheet->getColumnDimension('B')->setWidth(35);  // Nom et prénom
        $event->sheet->getColumnDimension('C')->setWidth(20);  // Numéro de compte
        $event->sheet->getColumnDimension('D')->setWidth(20);  // Montant
        $event->sheet->getColumnDimension('E')->setWidth(15);  // Montant
        $event->sheet->getColumnDimension('F')->setWidth(15);  // Montant
        $event->sheet->getColumnDimension('G')->setWidth(width: 15);  // Montant
        $event->sheet->getColumnDimension('H')->setWidth(15);  // Montant

        // Calcul dynamique de la position du tableau
        $headerCount = 0;
        foreach ($this->headings() as $heading) {
          $headerCount++;
          if ($heading === ['N°', 'Nom et Prénom', 'Fonction', 'Categorie', 'N° CNSS', 'CIN', 'Date Embauche', 'Telephone']) {
            break;
          }
        }
        $tableStart = $headerCount; // La ligne où commence le tableau (en-tête)
        $tableEnd = $tableStart + count($this->employes); // La dernière ligne de données
        $totalRow = $tableEnd + 1; // La ligne du total

        // Centrer les deux titres sur 8 colonnes (A à H)
        $event->sheet->mergeCells('A1:H1');
        $event->sheet->mergeCells('A2:H2');
        $event->sheet->getStyle('A1:H2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $event->sheet->getStyle('A1:H2')->getFont()->setBold(true);

        // Centrer les libellés du tableau (ligne d'en-tête)
        $event->sheet->getStyle('A' . $tableStart . ':H' . $tableStart)
          ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Alignement N° à centre (sauf l'en-tête)
        $event->sheet->getStyle('A' . ($tableStart + 1) . ':A' . $totalRow)
          ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Alignement à gauche pour toutes les autres cellules (B à H)
        $event->sheet->getStyle('B' . ($tableStart + 1) . ':H' . $tableEnd)
          ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

        // Bordures sur tout le tableau
        $event->sheet->getStyle('A' . $tableStart . ':H' . $tableEnd)
          ->applyFromArray([
            'borders' => [
              'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['argb' => 'FF000000'],
              ],
            ],
          ]);
      },
    ];
  }
}
