<?php

namespace App\Http\Controllers;

use App\Exports\CategExport;
use App\Pdf\EtatCateg;
use App\Models\Personne;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class EtatCategController extends Controller
{
  public function generate(request $request)
  {
    $categorie = $request->query('categorie', '');
    $categ_id = $request->query('categ_id', '');
    $employes = Personne::with('fonction')
      ->where('categ_id', $categ_id)
      ->orderBy('nom')
      ->get();
    $pdf = new EtatCateg($categorie);
    $pdf->AddPage();
    $pdf->SetY(20);
    $compteur = 1;
    $count_pers = count($employes);
    // Ajout des données des employés
    foreach ($employes as $employe) {
      $pdf->SetX(6);
      $pdf->SetFont('helvetica', '', 8);
      // Données
      $pdf->Cell(15, 6, $compteur, 1, 0, 'C', false);
      $pdf->Cell(45, 6, mb_strtoupper($employe->nom . ' ' . $employe->prenom, 'UTF-8'), 1, 0, 'L', false);
      $pdf->Cell(25, 6, $employe->fonction->libelle ?: '-', 1, 0, 'L', false);
      $pdf->Cell(20, 6, $employe->num_cnss ?: '-', 1, 0, 'L', false);
      $pdf->Cell(20, 6, $employe->cin ?: '-', 1, 0, 'L', false);
      $pdf->Cell(25, 6, $employe->date_embauche ?: '-', 1, 0, 'C', false);
      $pdf->Cell(25, 6, $employe->phone ?: '-', 1, 1, 'C', false);
      $compteur++;
      $count_pers = $count_pers - 1;
      if ($pdf->GetY() + 45 > ($pdf->getPageHeight() - $pdf->getFooterMargin()) and $count_pers < 5) {
        $pdf->AddPage();
      }
    }
    // Génération du PDF
    return $pdf->Output('etat_employes_' . date('Y-m-d') . '.pdf', 'I');
  }

  public function exporter_excel(Request $request){
    $categorie = $request->query('categorie', '');
    $categ_id = $request->query('categ_id', '');
    $employes = Personne::with('fonction')
      ->where('categ_id', $categ_id)
      ->orderBy('nom')
      ->get();
    return Excel::download(new CategExport($employes,$categorie), 'employes.xlsx');

    
  }
}
