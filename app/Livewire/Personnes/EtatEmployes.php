<?php

namespace App\Livewire\Personnes;

use App\Models\Personne;
use Livewire\Component;
use Livewire\WithPagination;

class EtatEmployes extends Component
{
  use WithPagination;

  public $nom1 = '';
  public $nom2 = '';
  public $etablissement = '';
  public $titre = '';
  public $search = '';
  public $sortField = 'nom';
  public $sortDirection = 'asc';
  // Rafraîchir le composant lors du changement d'établissement
  public function updatedEtablissement()
  {
    $this->resetPage();
  }

  protected $paginationTheme = 'bootstrap';

  public function updatingSearch()
  {
    $this->resetPage();
  }

  public function sortBy($field)
  {
    if ($this->sortField === $field) {
      $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
    } else {
      $this->sortDirection = 'asc';
    }
    $this->sortField = $field;
    $this->resetPage();
  }

  public function generatePdf()
  {
    if ($this->etablissement === '4') {
      $this->titre = 'DAR ATALIBA Mly Abdellah';
    } else {
      $this->titre = 'Fédération des Associations Mly Abdellah';
    }
    $params = route('etat.employes.pdf', [
      'nom1' => $this->nom1,
      'nom2' => $this->nom2,
      'titre' => $this->titre,
      'etablissement' => $this->etablissement,
    ]);
    $this->dispatch('openEtatWindow', url: $params);

    // return redirect()->route('etat.employes.pdf', $params);
  }

  public function downloadPdf()
  {
    $params = [
      'nom1' => $this->nom1,
      'nom2' => $this->nom2,
      'titre' => $this->titre,
      'etablissement' => $this->etablissement,
    ];

    return redirect()->route('etat.employes.download', $params);
  }

  public function render()
  {
    /*$employes = Personne::query()
        ->with(['fonction','categorie','inscriptions'])
        ->where('status', true)    
        ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nom', 'like', '%' . $this->search . '%')
                      ->orWhere('prenom', 'like', '%' . $this->search . '%')
                      ->orWhere('phone', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);*/
    $employes = Personne::query()
      ->with(['fonction', 'categorie'])
      ->where('status', true)
      ->when($this->etablissement, function ($query) {
        if ($this->etablissement === '1,2,3') {
          $query->whereIn('categ_id', [1, 2, 3]);
        } elseif ($this->etablissement === '4') {
          $query->where('categ_id', 4);
        }
      })
      ->when($this->search, function ($query) {
        $query->where(function ($q) {
          $q->where('nom', 'like', '%' . $this->search . '%')
            ->orWhere('prenom', 'like', '%' . $this->search . '%')
            ->orWhere('phone', 'like', '%' . $this->search . '%');
        });
      })
      ->orderBy('fonction_id', 'asc')
      ->paginate(10);

    $totalEmployes = Personne::count();
    $totalSalaires = Personne::sum('salaire_base') ?: 0;
    $moyenneSalaire = $totalEmployes > 0 ? $totalSalaires / $totalEmployes : 0;

    return view('livewire.personnes.etat-employes', [
      'employes' => $employes,
      'totalEmployes' => $totalEmployes,
      'totalSalaires' => $totalSalaires,
      'moyenneSalaire' => $moyenneSalaire
    ]);
  }
  public function generateExcel()
  {
    if ($this->etablissement === '4') {
      $this->titre = 'DAR ATALIBA Mly Abdellah';
    } else {
      $this->titre = 'Fédération des Associations Mly Abdellah';
    }

    $params = route('etat.categ.excel', [
      'nom1' => $this->nom1,
      'nom2' => $this->nom2,
      'titre' => $this->titre,
      'etablissement' => $this->etablissement,
    ]);

    $this->dispatch('ouvrir-excel', $params);
  }
}
