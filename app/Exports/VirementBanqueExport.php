<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class VirementBanqueExport implements FromArray, WithHeadings, WithEvents
{
  protected $salaires;
  protected $federation;
  protected $date_virement;
  protected $categ_id;

  public function __construct($salaires, $federation, $date_virement, $categ_id)
  {
    $this->salaires = $salaires;
    $this->federation = $federation;
    $this->date_virement = $date_virement;
    $this->categ_id = $categ_id;
  }

  public function array(): array
  {
    $rows = [];
    $total = 0;
    $i = 1;
    foreach ($this->salaires as $salaire) {
      $rows[] = [
        $i++,
        $salaire->personne->nom . ' ' . $salaire->personne->prenom,
        $salaire->personne->num_compte,
        number_format($salaire->montant_vire, 2, '.', ' ')
      ];
      $total += $salaire->montant_vire;
    }
    // Ajoute la ligne Total à la fin
    $rows[] = ['', '', 'Total', number_format($total, 2, '.', ' ')];
    return $rows;
  }

  public function headings(): array
  {
    if ($this->categ_id == 4) {
      return [
        ['DAR ATALIBA MY ABDELLAH'],
        ['', '', '        mly abdellah le:' . now()->format('d/m/Y')],
        ['', '', '                                             A'],
        ['', '', '                          Monsieur,le Directeur de la BP'],
        ['', '', "                          Sidi Bouzid d'El Jadida"],
        [],
        ["Objet : Demande de Virement"],
        ["        Nous avons L'honneur de vous demander de bien vouloir virer de notre compte " . $this->federation->num_rib . " de L'ASSOCIATION"],
        ["DAR TALIBA MY ABDELLAH Veuillez créditer les comptes"],
        ["        ci-aprés, à partir de " . $this->date_virement . " et jusqu'a nouvel ordre."],
        [],
        ['N°', 'Nom et Prénom', 'Numero de Compte', 'Montant'],
      ];
    } else {
      return [
        ['Fédération des associations'],
        ['Mly Abdellah', '', '        Mly Abdellah le:' . now()->format('d/m/Y')],
        ['', '', '                                             A'],
        ['', '', '                          Monsieur,le Directeur de la BP'],
        ['', '', "                          Sidi Bouzid d'El Jadida"],
        [],
        ["Objet : Demande de Virement"],
        ["        Nous avons L'honneur de vous demander de bien vouloir virer de notre compte " . $this->federation->num_rib . " de l'association"],
        ["Autre Association Veuillez créditer les comptes"],
        ["        ci-aprés, à partir de " . $this->date_virement . " et jusqu'a nouvel ordre."],
        [],
        ['N°', 'Nom et Prénom', 'Numero de Compte', 'Montant'],
      ];
    }
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

        // Style titres
        //$event->sheet->getStyle('A10:D10')->getFont()->setBold(true);

        // Largeurs
        $event->sheet->getColumnDimension('A')->setWidth(6);   // N°
        $event->sheet->getColumnDimension('B')->setWidth(55);  // Nom et prénom
        $event->sheet->getColumnDimension('C')->setWidth(35);  // Numéro de compte
        $event->sheet->getColumnDimension('D')->setWidth(20);  // Montant

        // Calcul dynamique de la position du tableau
        $headerCount = 0;
        foreach ($this->headings() as $heading) {
          $headerCount++;
          if ($heading === ['N°', 'Nom et Prénom', 'Numero de Compte', 'Montant']) {
            break;
          }
        }
        $tableStart = $headerCount; // La ligne où commence le tableau (en-tête)
        $tableEnd = $tableStart + count($this->salaires); // La dernière ligne de données
        $totalRow = $tableEnd + 1; // La ligne du total

        // Encadrer le tableau principal (en-tête + données + total)
        $event->sheet->getStyle('A' . $tableStart . ':D' . $totalRow)
          ->applyFromArray([
            'borders' => [
              'outline' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                'color' => ['argb' => 'FF000000'],
              ],
              'inside' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['argb' => 'FF000000'],
              ],
            ],
          ]);

        // Encadrer chaque cellule de la ligne d'en-tête du tableau
        for ($col = 'A'; $col <= 'D'; $col++) {
          $event->sheet->getStyle($col . $tableStart)
            ->applyFromArray([
              'borders' => [
                'top' => [
                  'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                  'color' => ['argb' => 'FF000000'],
                ],
                'left' => [
                  'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                  'color' => ['argb' => 'FF000000'],
                ],
                'right' => [
                  'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                  'color' => ['argb' => 'FF000000'],
                ],
                'bottom' => [
                  'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                  'color' => ['argb' => 'FF000000'],
                ],
              ],
            ]);
        }

        // Encadrer la ligne Total (toutes les bordures)
        $event->sheet->getStyle('A' . $totalRow . ':D' . $totalRow)
          ->applyFromArray([
            'borders' => [
              'top' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                'color' => ['argb' => 'FF000000'],
              ],
              'bottom' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                'color' => ['argb' => 'FF000000'],
              ],
              'left' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                'color' => ['argb' => 'FF000000'],
              ],
              'right' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                'color' => ['argb' => 'FF000000'],
              ],
            ],
            'font' => [
              'bold' => true,
            ],
          ]);
        // Encadrer la cellule du montant total (D colonne)
        $cellTotal = 'D' . $totalRow;
        $event->sheet->getStyle($cellTotal)
          ->applyFromArray([
            'borders' => [
              'top' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                'color' => ['argb' => 'FF000000'],
              ],
              'bottom' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                'color' => ['argb' => 'FF000000'],
              ],
              'left' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                'color' => ['argb' => 'FF000000'],
              ],
              'right' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                'color' => ['argb' => 'FF000000'],
              ],
            ],
            'font' => [
              'bold' => true,
            ],
          ]);
        // Alignement Montant à droite (sauf l'en-tête)
        $event->sheet->getStyle('D' . ($tableStart + 1) . ':D' . $totalRow)
          ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        // Alignement du mot Total à droite (cellule C)
        $cellTotalLabel = 'C' . $totalRow;
        $event->sheet->getStyle($cellTotalLabel)
          ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        // Centrer les libellés du tableau (ligne d'en-tête)
        $event->sheet->getStyle('A' . $tableStart . ':D' . $tableStart)
          ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        // Encadrer la ligne d'en-tête du tableau (haut, gauche, droite)
        $event->sheet->getStyle('A' . $tableStart . ':D' . $tableStart)
          ->applyFromArray([
            'borders' => [
              'top' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                'color' => ['argb' => 'FF000000'],
              ],
              'left' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                'color' => ['argb' => 'FF000000'],
              ],
              'right' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                'color' => ['argb' => 'FF000000'],
              ],
            ],
          ]);
        // Alignement N° à centre (sauf l'en-tête)
        $event->sheet->getStyle('A' . ($tableStart + 1) . ':A' . $totalRow)
          ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
      },
    ];
  }
}
