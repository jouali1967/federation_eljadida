<?php

namespace App\Livewire\Augmentations;

use Livewire\Component;
use App\Models\Personne;
use App\Models\Augmentation;
use Livewire\Attributes\Rule;

class GlobaleAugmentation extends Component
{
  #[Rule('required|date_format:d/m/Y', message: 'La date de inscription est requise')]
  public $date_aug;

  #[Rule('required|integer', message: 'Le n° CNSS est obligatoire et doit être un nombre entier.')]
  public $valeur;
  #[Rule('nullable', message: 'le motif est obligatoire.')]
  public $motif;
  #[Rule('required|in:fixe,pourcentage', message: 'le type est obligatoire.')]
  public $type = 'fixe';

  public function save()
  {
    $this->validate();
    $personnes = Personne::where('status', true)
      ->get();
    foreach ($personnes as $personne) {
      $ancien = $personne->salaire_base;
      $nouveau = $this->type === 'fixe'
        ? $ancien + $this->valeur
        : $ancien * (1 + $this->valeur / 100);
      Augmentation::create([
        'personne_id' => $personne->id,
        'type' => $this->type,
        'valeur' => $this->valeur,
        'ancien_salaire' => $ancien,
        'nouveau_salaire' => $nouveau,
        'date_aug' => $this->date_aug,
        'motif' => $this->motif,
      ]);

      $personne->update(['salaire_base' => $nouveau]);
    }
    session()->flash('success', '✅ Augmentations enregistrées avec succès !');
    $this->reset();
  }
  public function render()
  {
    return view('livewire.augmentations.globale-augmentation');
  }
}
