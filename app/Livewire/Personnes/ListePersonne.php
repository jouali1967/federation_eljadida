<?php

namespace App\Livewire\Personnes;

use Livewire\Component;
use App\Models\Category;
use App\Models\Fonction;
use App\Models\Personne;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;

class ListePersonne extends Component
{
  use WithPagination;
  use WithoutUrlPagination;
  public $paginationTheme = "bootstrap";
  public $search = '';
  public $sortField = 'nom';
  public $sortDirection = 'asc';
  public $categ_id = '';
  public $fonction_id = '';
  public $status = '';

  protected $listeners = ['personne-created' => '$refresh'];

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
  }

  public function updatedCateg($value)
  {
    $this->resetPage();
  }

  public function render()
  {
    $fonctions = Fonction::all();
    $categories = Category::all();
    $personnes = Personne::query()
      ->when($this->categ_id, function ($query) {
        $query->where('categ_id', $this->categ_id);
      })
      ->when($this->fonction_id, function ($query) {
        $query->where('fonction_id', $this->fonction_id);
      })
      ->when($this->status !== '', function ($query) {
        $query->where('status', (bool) $this->status);
      })

      ->where(function ($query) {
        $query->where('nom', 'like', '%' . $this->search . '%')
          ->orWhere('prenom', 'like', '%' . $this->search . '%');
      })
      ->orderBy($this->sortField, $this->sortDirection)
      ->paginate(10);
    return view('livewire.personnes.liste-personne', compact('personnes', 'fonctions', 'categories'));
  }

  public function delete($id)
  {
    try {
      $personne = Personne::findOrFail($id);
      $personne->delete();

      $this->dispatch('personne-deleted', [
        'title' => 'Succès!',
        'message' => 'La personne a été supprimée avec succès.',
        'type' => 'success'
      ]);
    } catch (\Exception $e) {
      $this->dispatch('personne-deleted', [
        'title' => 'Erreur!',
        'message' => 'Une erreur est survenue lors de la suppression.',
        'type' => 'error'
      ]);
    }
  }
}
