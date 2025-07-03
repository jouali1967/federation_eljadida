<?php

use App\Http\Controllers\EtatCategController;
use App\Http\Controllers\VirementBanquePdfController;
use App\Livewire\Augmentations\GlobaleAugmentation;
use App\Livewire\Editions\ListEmpCateg;
use App\Livewire\HomePage;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Cnss\CnssCreate;
use App\Livewire\Primes\EditPrime;
use App\Livewire\Primes\ListePrime;
use App\Livewire\Primes\CreatePrime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\RoleManagement;
use App\Livewire\Admin\UserManagement;
use App\Livewire\Enfants\EnfantCreate;
use App\Livewire\Personnes\MontantDec;
use App\Livewire\Salaires\VirementPdf;
use App\Livewire\Personnes\EditPersonne;
use App\Livewire\Personnes\EtatEmployes;
use App\Livewire\Sanctions\EditSanction;
use App\Livewire\Sanctions\ListSanction;
use App\Livewire\Personnes\ListePersonne;
use App\Livewire\Salaires\GestionSalaire;
use App\Livewire\Personnes\CreatePersonne;
use App\Livewire\Sanctions\CreateSanction;
use App\Livewire\Personnes\EtatEmployesPdf;
use App\Livewire\Salaires\SalaireImpression;
use App\Livewire\Personnes\ListePersonnesPdf;
use App\Livewire\Sanctions\ListeSanctionsPdf;
use App\Http\Controllers\PdfPersonneController;
use App\Livewire\Augmentations\ListAugmentation;
use App\Livewire\Declarations\PersonnesDeclares;
use App\Livewire\Augmentations\CreateAugmentation;

use App\Http\Controllers\EtatDeclaresPdfController;
use App\Http\Controllers\EtatEmployesPdfController;
use App\Http\Controllers\SalaireImpressionPdfController;

Route::get('/', function () {
  return redirect('/login');;
});

Auth::routes();

Route::middleware(['auth'])->group(function () {
  Route::get('/', HomePage::class)->name('home.page');
  //Routes pour les employers
  Route::get('/personnes', ListePersonne::class)->name('personnes.index')->middleware('permission:personnes.index');
  Route::get('/personnes/create', CreatePersonne::class)->name('personnes.create')->middleware('permission:personnes.create');
  Route::get('/personnes/{id}/edit', EditPersonne::class)->name('personnes.edit')->middleware('permission:personnes.edit');
  Route::get('/declarations/create', MontantDec::class)->name('declarations.create')->middleware('permission:declarations.create');

  // Routes pour les primes
  Route::get('/primes/create', CreatePrime::class)->name('primes.create')->middleware('permission:primes.create');
  Route::get('/primes/{prime}/edit', EditPrime::class)->name('primes.edit')->middleware('permission:primes.edit');
  Route::get('/primes', ListePrime::class)->name('primes.index')->middleware('permission:primes.index');
  //Routes pour sanctions
  Route::get('/sanctions/create', CreateSanction::class)->name('sanctions.create')->middleware('permission:sanctions.create');
  Route::get('/sanctions', ListSanction::class)->name('sanctions.index')->middleware('permission:sanctions.index');
  Route::get('/sanctions/{sanction}/edit', EditSanction::class)->name('sanctions.edit')->middleware('permission:sanctions.edit');
  Route::get('/sanctions/pdf', ListeSanctionsPdf::class)->name('sanctions.pdf')->middleware('permission:sanctions.pdf');
  // Routes pour les salaires
  Route::get('/salaires/gestion', GestionSalaire::class)->name('salaires.gestion')->middleware('permission:salaires.gestion');
  Route::get('/salaires/impression', SalaireImpression::class)->name('salaires.impression')->middleware('permission:salaires.impression');
  Route::get('/salaires/impression/pdf', [SalaireImpressionPdfController::class, 'export'])->name('salaires.impression.pdf')->middleware('permission:salaires.impression.pdf');
  Route::get('/salaires/virement', VirementPdf::class)->name('salaires.virement')->middleware('permission:salaires.virement');
  Route::get('/salaires/virement/pdf', [VirementBanquePdfController::class, 'export'])->name('salaires.virement.pdf')->middleware('permission:salaires.virement.pdf');

  //cnss
  Route::get('/cnss/create', CnssCreate::class)->name('cnss.create')->middleware('permission:cnss.create');
  //enfants
  Route::get('/enfants/create', EnfantCreate::class)->name('enfants.create')->middleware('permission:enfants.create');
  //contributions
  Route::get('/augmentations', ListAugmentation::class)->name('augmentations.index')->middleware('permission:augmentations.index');
  Route::get('/augmentations/create', CreateAugmentation::class)->name('augmentations.create')->middleware('permission:augmentations.create');
  Route::get('/augmentations/globale', GlobaleAugmentation::class)->name('augmentations.globale')->middleware('permission:augmentations.globale');
  //editions
  Route::get('/generate-pdf', [PdfPersonneController::class, 'generate'])->name('generate.pdf')->middleware('permission:generate.pdf');
  Route::get('/personnes/pdf', ListePersonnesPdf::class)->name('editions.pdf')->middleware('permission:editions.pdf');
  Route::get('/etat-declares/pdf', [EtatDeclaresPdfController::class, 'imprimer'])->name('etat_declares.pdf')->middleware('permission:etat_declares.pdf');
  Route::get('/etat-employes/declares', PersonnesDeclares::class)->name('editions.declares')->middleware('permission:editions.declares');

  // État des employés
  Route::get('/etat-employes', EtatEmployes::class)->name('editions.employes')->middleware('permission:editions.employes');
  Route::get('/etat-employes/pdf', [EtatEmployesPdfController::class, 'generate'])->name('etat.employes.pdf')->middleware('permission:etat.employes.pdf');
  Route::get('/etat-employes/download', [EtatEmployesPdfController::class, 'download'])->name('etat.employes.download')->middleware('permission:etat.employes.download');
  //etat employes par categ
    Route::get('/etats/categ', ListEmpCateg::class)->name('editions.etat.categ')->middleware('permission:editions.etat.categ');
    Route::get('/etat-categ/pdf', [EtatCategController::class, 'generate'])->name('etat-categ-pdf')->middleware('permission:etat-categ-pdf');

});
Route::middleware(['auth', 'role:admin'])->group(function () {
  Route::get('/admin/dashboard', Dashboard::class)->name('admin.dashboard');
  Route::get('/admin/users', UserManagement::class)->name('admin.users');
  Route::get('/admin/roles', RoleManagement::class)->name('admin.roles');
});
