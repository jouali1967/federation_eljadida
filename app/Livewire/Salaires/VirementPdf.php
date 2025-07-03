<?php

namespace App\Livewire\Salaires;

use App\Models\Category;
use App\Models\Federation;
use Livewire\Component;
use App\Models\Salaire;
use Illuminate\Support\Carbon;
use Livewire\WithPagination;

class VirementPdf extends Component
{
  use WithPagination;
  public $date_virement;
  public $date_virement_input;
  public $dateVirement;
  public $search = '';
  public $categ_id;
  public $first_sign;
  public $second_sign;
  protected $paginationTheme = 'bootstrap';
  protected $rules = [
    'date_virement' => 'required|date_format:d/m/Y',
  ];

  protected $messages = [
    'date_virement.required' => 'La date virement est obligatoire',
    'date_virement.date_format' => 'La date virement doit être au format JJ/MM/AAAA',
  ];

  public function afficherSalaires()
  {
    $this->validate([
      'date_virement_input' => 'required|date_format:d/m/Y',
    ]);
    $this->date_virement = $this->date_virement_input;
  }

  public function updatingSearch()
  {
    $this->resetPage();
  }

  public function imprimerPDF()
  {
   $information = Federation::where('categorie', $this->categ_id)->first();
    $this->dispatch('ouvrir-pdf', route('salaires.virement.pdf', [
      'date_virement' => $this->date_virement,
      'rib' => $information->num_rib,
      'first_sign' => $this->first_sign,
      'second_sign' => $this->second_sign,
      'categ_id' => $this->categ_id,
    ]));
  }

  public function updatedDateVirementInput()
  {
    $this->date_virement = null;
  }

  public function render()
  {
    $categories = Category::all();
    $salaires = [];
    if ($this->date_virement) {
      $categ_id=$this->categ_id;
      $date_vir = Carbon::createFromFormat('d/m/Y', $this->date_virement);
      $salaires = Salaire::with('personne')
        ->whereMonth('date_virement', $date_vir->format('m'))
        ->whereYear('date_virement', $date_vir->year)
        ->whereHas('personne', function ($q) use($categ_id) {
          $q->where('categ_id', $categ_id); // ✅ filtre sur la catégorie de la personne
        })
        ->when($this->search, function ($query) {
          $query->whereHas('personne', function ($q) {
            $q->where('nom', 'like', '%' . $this->search . '%')
              ->orWhere('prenom', 'like', '%' . $this->search . '%');
          });
        })
        ->orderByDesc('id')
        ->paginate(10);
    }

    return view('livewire.salaires.virement-pdf', compact('salaires', 'categories'));
  }
}
