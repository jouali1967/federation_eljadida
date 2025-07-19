<?php

namespace App\Livewire\Editions;

use App\Models\Category;
use App\Models\Personne;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;


class ListEmpCateg extends Component
{
  use WithPagination;
  use WithoutUrlPagination;
  public $paginationTheme = "bootstrap";
  public $categ_id = '';

  public $search = '';

  //protected $queryString = [];
  protected $rules = [
    'categ_id' => 'required|exists:categories,id',
  ];

  public function rechercher()
  {
    $this->validate();
    $this->resetPage();
  }

  public function updatedCategId()
  {
    $this->resetPage();
  }

  public function updatingSearch()
  {
    $this->resetPage();
  }

  public function imprimer()
  {
    $categorie = '';
    if ($this->categ_id) {
      $cat = Category::find($this->categ_id);
      $categorie = $cat?->libelle ?? '';
    }
    $params = route('etat-categ-pdf', [
      'categorie' => $categorie,
      'categ_id' => $this->categ_id
    ]);
    $this->dispatch('openEtatWindow', url: $params);
  }

  public function render()
  {
    $categories = Category::all();
    $employes = null;
    if ($this->categ_id) {
      $employes = Personne::with('fonction')
        ->where('categ_id', $this->categ_id)
        ->when($this->search, function ($query) {
          $query->where(function ($q) {
            $q->where('nom', 'like', '%' . $this->search . '%')
              ->orWhere('prenom', 'like', '%' . $this->search . '%');
          });
        })
        ->orderBy('nom')
        ->paginate(5);
    }
    return view('livewire.editions.list-emp-categ', compact('categories', 'employes'));
  }

  public function exporter()
  {
    $categorie = '';
    if ($this->categ_id) {
      $cat = Category::find($this->categ_id);
      $categorie = $cat?->libelle ?? '';
    }
    $params = route('categ-excel', [
      'categorie' => $categorie,
      'categ_id' => $this->categ_id
    ]);
    $this->dispatch('ouvrir-excel', $params);
  }
}
