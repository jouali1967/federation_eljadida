<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class LogoutComponent extends Component
{
  public function logout()
  {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();

    $this->redirect('/federation/public/login', navigate: true);
  }

  public function render()
  {
    return <<<'HTML'
        <div>
            <button 
                wire:click="logout" 
                wire:loading.attr="disabled"
                class="btn btn-default btn-flat float-end"
            >
                <span wire:loading.remove>
                    <i class="fas fa-sign-out-alt me-2"></i>
                    Déconnexion
                </span>
                <span wire:loading>
                    <i class="fas fa-spinner fa-spin me-2"></i>
                    Déconnexion...
                </span>
            </button>
        </div>
        HTML;
  }
}
