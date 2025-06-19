<?php

namespace App\Livewire\Primes;

use Carbon\Carbon;
use App\Models\Prime;
use App\Models\Salaire;
use Livewire\Component;
use Livewire\WithPagination;

class ListePrime extends Component
{
  use WithPagination;

  public $search = '';
  public $sortField = 'date_prime';
  public $sortDirection = 'desc';

  protected $listeners = ['sanctionAdded' => '$refresh'];

  public function sortBy($field)
  {
    if ($this->sortField === $field) {
      $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
    } else {
      $this->sortField = $field;
      $this->sortDirection = 'asc';
    }
  }

  public function delete($id)
  {
    $prime = Prime::find($id);
   /* $dateVirement = Carbon::createFromFormat('d/m/Y', $prime->date_prime);
    $salaire = Salaire::where('personne_id', $prime->personne_id)
      ->whereMonth('date_virement',  $dateVirement->format('m'))
      ->whereYear('date_virement', $dateVirement->year)
      ->first();
    $salaire->update([
      'montant_vire' => $salaire->montant_vire - $salaire->montant_prime,
      'montant_prime' => 0
    ]);*/
    if ($prime) {
      $prime->delete();
      session()->flash('message', 'Sanction supprimée avec succès.');
    }
  }

  public function render()
  {
    return view('livewire.primes.liste-prime', [
      'primes' => Prime::query()
        ->with('personne')
        ->when($this->search, function ($query) {
          $query->where('motif_prime', 'like', '%' . $this->search . '%')
            ->orWhere('montant', 'like', '%' . $this->search . '%')
            ->orWhereHas('personne', function ($q) {
              $q->where('nom', 'like', '%' . $this->search . '%')
                ->orWhere('prenom', 'like', '%' . $this->search . '%');
            });
        })
        ->orderBy($this->sortField, $this->sortDirection)
        ->paginate(10)
    ]);
  }
}
