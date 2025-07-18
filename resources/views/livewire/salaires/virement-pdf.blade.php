<div>
  <div class="text-center mb-3">
    <h4 class="fw-semibold text-secondary">
      Sélectionnez un mois et une année pour afficher les virements bancaires des employés.
    </h4>
    <div class="text-muted" style="font-size:0.95rem;">
      Choisissez la date de virement souhaitée, puis validez pour consulter la liste des virements correspondants.
    </div>
  </div>
  <div>
    <form wire:submit.prevent="afficherSalaires">
      <div class="row mb-1">
        <div class="col-md-3 form-group mb-2">
          <label for="date_virement_input" class="form-label mb-1">Date de virement</label>
          <div class="input-group input-group-sm">
            <span class="input-group-text bg-light">
              <i class="fas fa-calendar"></i>
            </span>
            <input type="text" wire:model='date_virement_input' id="date_virement"
              class='form-control @error("date_virement_input") is-invalid @enderror' placeholder="JJ/MM/AAAA">
          </div>
          @error('date_virement_input')
          <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-md-3 form-group mb-2">
          <label for="categ_id" class="form-label mb-1">Catégorie</label>
          <div class="input-group input-group-sm">
            <span class="input-group-text">
              <i class="fas fa-list"></i>
            </span>
            <select id="categorie"
              class="form-select form-select-sm scrollable-select @error('categ_id') is-invalid @enderror"
              wire:model.live="categ_id">
              <option value="">Sélectionnez une catégorie</option>
              @foreach ($categories as $category)
              <option value="{{ $category->id }}">{{ $category->libelle }}</option>
              @endforeach
            </select>
          </div>
          @error('categ_id')
          <div class="invalid-feedback d-block">
            {{ $message }}
          </div>
          @enderror

        </div>
      </div>
      <div class="row mb-1">
        <div class="col-md-4 form-group mb-2">
          <label for="first_sign" class="form-label mb-1">
            Premier Signataire <span class="text-danger">*</span>
          </label>
          <div class="input-group input-group-sm">
            <span class="input-group-text">
              <i class="fas fa-user"></i>
            </span>
            <input id="first_sign" type="text" class="form-control @error('first_sign') is-invalid @enderror"
              wire:model.live="first_sign" placeholder="Entrez premier signataire">
          </div>
          @error('first_sign')
          <div class="invalid-feedback d-block">
            {{ $message }}
          </div>
          @enderror
        </div>
        <div class="col-md-4 form-group mb-2">
          <label for="second_sign" class="form-label mb-1">
            Second Signataire <span class="text-danger">*</span>
          </label>
          <div class="input-group input-group-sm">
            <span class="input-group-text">
              <i class="fas fa-user"></i>
            </span>
            <input id="second_sign" type="text" class="form-control @error('second_sign') is-invalid @enderror"
              wire:model.live="second_sign" placeholder="Entrez second signataire">
          </div>
          @error('second_sign')
          <div class="invalid-feedback d-block">
            {{ $message }}
          </div>
          @enderror
        </div>
        <div class="col-md-3 d-flex align-items-end mb-2">
          <button type="submit" class="btn btn-primary w-100">
            <i class="fas fa-calculator me-2"></i>
            Afficher les Salaires
          </button>
        </div>
      </div>
    </form>
  </div>
  @if(count($salaires))
  <div id="salaire-table-section">
    @php
    $moisAnnee = '';
    if (!empty($date_virement)) {
    try {
    \Carbon\Carbon::setLocale('fr_FR');
    $dt = \Carbon\Carbon::createFromFormat('d/m/Y', $date_virement);
    $moisAnnee = $dt->translatedFormat('F Y');
    } catch (Exception $e) {
    $moisAnnee = $date_virement;
    }
    }
    @endphp
    <div class="text-center my-3">
      <h3 class="fw-bold text-primary" style="letter-spacing:1px;">
        <i class="fas fa-university me-2"></i>
        Virements des Employés
        @if($moisAnnee)
        <span class="text-dark">— {{ ucfirst($moisAnnee) }}</span>
        @endif
      </h3>
      <div class="text-muted" style="font-size:1rem;">
        Affichage des virements bancaires pour tous les employés selon le mois et l'année sélectionnés.
      </div>
    </div>

    <div class="d-flex align-items-center justify-content-between mb-1" style="gap: 8px;">
      <div style="flex:1; text-align:left;">
        <button class="btn btn-primary btn-sm no-print" wire:click.prevent="imprimerPDF"
          @ouvrir-pdf.window="window.open($event.detail, '_blank')">
          Impression (PDF)
        </button>
        <button class="btn btn-success btn-sm no-print" wire:click.prevent="imprimerExcel"
          @ouvrir-excel.window="window.open($event.detail, '_blank')">
          Impression (EXCEL)
        </button>
      </div>
      <div style="flex:1; text-align:right;">
        <input type="text" class="form-control form-control-sm" style="max-width: 220px; display:inline-block;"
          placeholder="Rechercher par nom ou prénom..." wire:model.live="search">
      </div>
    </div>

    <table class="table table-bordered table-sm mt-1">
      <thead>
        <tr>
          <th>#</th>
          <th>Nom et Prénom</th>
          <th>Salaire de Base</th>
          <th>Montant des Primes</th>
          <th>Montant des Sanctions</th>
          <th>Salaire Mensuel</th>
        </tr>
      </thead>
      <tbody>
        @php $i = ($salaires instanceof \Illuminate\Pagination\LengthAwarePaginator) ? ($salaires->currentPage() - 1) *
        $salaires->perPage() + 1 : 1; @endphp
        @foreach($salaires as $salaire)
        <tr>
          <td>{{ $i++ }}</td>
          <td>{{ $salaire->personne->nom }} {{ $salaire->personne->prenom }}</td>
          <td style="text-align: right;">{{ $salaire->salaire_base }}</td>
          <td style="text-align: right;">{{ $salaire->montant_prime }}</td>
          <td style="text-align: right;">{{ $salaire->montant_sanction }}</td>
          <td style="text-align: right;">{{ $salaire->montant_vire }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
    @if(method_exists($salaires, 'links'))
    <div>
      {{ $salaires->links() }}
    </div>
    @endif
  </div>
  @else
  <div class="alert alert-warning mt-4">Aucun résultat trouvé.</div>
  @endif
</div>
@script()
<script>
  $(document).ready(function(){
    flatpickr("#date_virement", {
      dateFormat: "d/m/Y",
      locale:"fr",
      allowInput: true,
      onChange: function(selectedDates, dateStr) {
        $wire.set('date_virement', dateStr);
      }
    });
  })

</script>
@endscript