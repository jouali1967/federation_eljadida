<?php

namespace App\Http\Controllers;

use App\Exports\EmployesExport;
use App\Models\Personne;
use App\Pdf\EtatEmployesPdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class EtatEmployesPdfController extends Controller
{
  public function generate(Request $request)
  {
    // Récupération des paramètres optionnels
    $etablissement = $request->query('etablissement', '');
    $nom1 = $request->query('nom1', '');
    $nom2 = $request->query('nom2', '');
    $titre = $request->query('titre', '');

    // Création du PDF
    $pdf = new EtatEmployesPdf($titre,  $etablissement);
    $pdf->AddPage();
    $pdf->SetY(28);
    // Récupération des employés
    $employes = Personne::with(['fonction', 'categorie'])
      ->where('status', true)
      ->when($etablissement, function ($query) use ($etablissement) {
        if ($etablissement === '1,2,3') {
          $query->whereIn('categ_id', [1, 2, 3]);
        } elseif ($etablissement === '4') {
          $query->where('categ_id', 4);
        }
      })
      ->orderBy('fonction_id')
      ->get();
    $datas = array_merge(
      array_fill(0, 10, ['nom' => 'test1', 'prenom' => 'hhhhh', 'fonction' => 'fffff', 'sexe' => 'F', 'date_embauche' => '868688', 'phone' => '7777', 'salaire_base' => 678]),
      array_fill(0, 10, ['nom' => 'test1', 'prenom' => 'hhhhh', 'fonction' => 'fffff', 'sexe' => 'F', 'date_embauche' => '868688', 'phone' => '7777', 'salaire_base' => 678]),
      array_fill(0, 10, ['nom' => 'test1', 'prenom' => 'hhhhh', 'fonction' => 'fffff', 'sexe' => 'F', 'date_embauche' => '868688', 'phone' => '7777', 'salaire_base' => 678]),
      array_fill(0, 10, ['nom' => 'test1', 'prenom' => 'hhhhh', 'fonction' => 'fffff', 'sexe' => 'F', 'date_embauche' => '868688', 'phone' => '7777', 'salaire_base' => 678]),
      array_fill(0, 15, ['nom' => 'test1', 'prenom' => 'hhhhh', 'fonction' => 'fffff', 'sexe' => 'F', 'date_embauche' => '868688', 'phone' => '7777', 'salaire_base' => 678]),
      array_fill(0, 20, ['nom' => 'test1', 'prenom' => 'hhhhh', 'fonction' => 'fffff', 'sexe' => 'F', 'date_embauche' => '868688', 'phone' => '7777', 'salaire_base' => 678]),
      array_fill(0, 4, ['nom' => 'test1', 'prenom' => 'hhhhh', 'fonction' => 'fffff', 'sexe' => 'F', 'date_embauche' => '868688', 'phone' => '7777', 'salaire_base' => 678]),
    );
    $totalSalaires = 0;
    $compteur = 1;
    $count_pers = count($employes);
    // Ajout des données des employés
    foreach ($employes as $employe) {
      $pdf->SetX(5);
      $pdf->SetFont('helvetica', '', 8);
      // Données
      $pdf->Cell(15, 6, $compteur, 1, 0, 'C', false);
      $pdf->Cell(45, 6, mb_strtoupper($employe->nom . ' ' . $employe->prenom, 'UTF-8'), 1, 0, 'L', false);
      $pdf->Cell(25, 6, $employe->fonction->libelle ?: '-', 1, 0, 'L', false);
      $pdf->Cell(25, 6, $employe->categorie->libelle ?: '-', 1, 0, 'L', false);
      $pdf->Cell(20, 6, $employe->num_cnss ?: '-', 1, 0, 'L', false);
      $pdf->Cell(20, 6, $employe->cin ?: '-', 1, 0, 'L', false);
      $pdf->Cell(25, 6, $employe->date_embauche ?: '-', 1, 0, 'C', false);
      $pdf->Cell(25, 6, $employe->phone ?: '-', 1, 1, 'C', false);
      // $totalSalaires += $employe->salaire_base ?: 0;
      $compteur++;
      $count_pers = $count_pers - 1;
      if ($pdf->GetY() + 45 > ($pdf->getPageHeight() - $pdf->getFooterMargin()) and $count_pers < 5) {
        $pdf->AddPage();
      }
    }
    // $pdf->SetX(5);
    // $pdf->Cell(160, 6, 'total', 1 , 0, 'R', false);
    // $pdf->Cell(25, 6, number_format($totalSalaires, 2, ',', ' ') . ' DH', 1, 1, 'R', false);

    // Ajout des signatures
    $pdf->addSignatures($nom1, $nom2);

    // Génération du PDF
    return $pdf->Output('etat_employes_' . date('Y-m-d') . '.pdf', 'I');
  }

  public function imprimer_excel(Request $request)
  {
    $etablissement = $request->query('etablissement', '');
    $nom1 = $request->query('nom1', '');
    $nom2 = $request->query('nom2', '');
    $titre = $request->query('titre', '');
    $employes = Personne::with(['fonction', 'categorie'])
      ->where('status', true)
      ->when($etablissement, function ($query) use ($etablissement) {
        if ($etablissement === '1,2,3') {
          $query->whereIn('categ_id', [1, 2, 3]);
        } elseif ($etablissement === '4') {
          $query->where('categ_id', 4);
        }
      })
      ->orderBy('fonction_id')
      ->get();
    return Excel::download(new EmployesExport($employes,$titre,$etablissement), 'employes.xlsx');

  }
}
