<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class LogoutButton extends Component
{
  public function logout()
  {
       Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        // Redirection vers la page de connexion, en utilisant le routeur de Livewire
        return $this->redirect(route('login'), navigate: true);
  }

  public function render()
  {
    return <<<'HTML'
        <div>
            <form wire:submit="logout">
                <button type="submit" class="btn btn-default btn-flat float-end" wire:loading.attr="disabled">
                    <i class="fas fa-sign-out-alt me-2" wire:loading.remove></i>
                    <i class="fas fa-spinner fa-spin me-2" wire:loading></i>
                    <span wire:loading.remove>Déconnexion</span>
                    <span wire:loading>Déconnexion...</span>
                </button>
            </form>
        </div>
        HTML;
  }
}
