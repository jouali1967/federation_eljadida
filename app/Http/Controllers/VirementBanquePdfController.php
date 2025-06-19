<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Salaire;
use Illuminate\Http\Request;
use App\Pdf\SalaireBanquePdf;
use Elibyy\TCPDF\Facades\TCPDF;

class VirementBanquePdfController extends Controller
{
  public function export(Request $request)
  {
    // $mois = $request->input('mois');
    // $annee = $request->input('annee');
    $dateVirement = $request->query('date_virement');
    $date_vir= Carbon::createFromFormat('d/m/Y', $dateVirement);

    $rib = $request->input('rib');
    $first_signataire = $request->input('first_signataire');
    $second_signataire = $request->input('second_signataire');
    $salaires = Salaire::with('personne')
      ->whereMonth('date_virement', $date_vir->format('m'))
      ->whereYear('date_virement', $date_vir->year)
      ->get();

    $pdf = new SalaireBanquePdf($rib, $first_signataire,$second_signataire,$dateVirement, $orientation = 'P', $unit = 'mm', $format = 'A4', $unicode = true, $encoding = 'UTF-8', $diskcache = false);
    $pdf->SetMargins(10, 6, 10);
    $pdf->SetAutoPageBreak(true, 15);
    $pdf->AddPage('P');
    $pdf->SetY(64);
    
    $compt = 1;
    $total=0;
    $count_pers=count($salaires);
    $pdf->SetFont('helvetica', '', 8);
    foreach ($salaires as $salaire) {
        $pdf->SetFont('times', '', 8);
        $pdf->SetX(6);
        $pdf->Cell(15, 5, $compt, 1,  0,'C');
        $pdf->Cell(85, 5, $salaire->personne->full_name, 1,  0);
        $pdf->Cell(50, 5, $salaire->personne->num_compte, 1, 0,'R');
        $pdf->Cell(50, 5, $salaire->personne->salaire_base, 1, 0,'R');
        $pdf->Ln();
        $total += $salaire->personne->salaire_base;
        $compt++;
        $count_pers=$count_pers-1;
        if ($pdf->GetY() + 30 > ($pdf->getPageHeight() - $pdf->getFooterMargin()) and $count_pers<5) {
            $pdf->AddPage();
        }

    }
    $pdf->setX(6);
    $pdf->SetFont('times', 'B', 10);
    $pdf->Cell(150, 6, 'Total', 1, 0,'R');
    $pdf->Cell(50, 6, number_format($total, 2, '.', ' '), 1,  1,'R');
    $expression="Veuillez agréer,Monsieur le Directeur,l'expression de nos salutations distinguées.";
    $pdf->Cell(200, 0, $expression, 0,  0,'C');
    
    $pdf->SetY($pdf->getY()+30);
    $pdf->Cell(20, 0, 'Signé', 0, 0,'');
    $pdf->SetX(160);
    $pdf->Cell(20, 0, 'Signé', 0, 1,'');
    $pdf->setY($pdf->getY()+4);
    $pdf->Cell(60,0,mb_strtoupper($first_signataire, 'UTF-8'),0,0);
    $pdf->SetX(160);
    $pdf->Cell(60,0,mb_strtoupper($second_signataire, 'UTF-8'),0,0);


    $pdf->Output('etat_salaires.pdf');
  }
}
