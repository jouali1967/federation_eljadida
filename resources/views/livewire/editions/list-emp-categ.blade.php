<div class="mt-2">
  <div class="card">
    <div class="card-header py-2">
      <h6 class="card-title mb-0">
        <i class="fas fa-chart-line me-1"></i>Liste des employés par categorie
      </h6>
    </div>
    <div class="card-body p-2">
      <form wire:submit.prevent="rechercher">
        <div class="row g-2 align-items-end">
          <div class="col-md-3">
            <div class="form-group mb-0">
              <label for="categ_id" class="form-label small">Categorie</label>
              <select wire:model="categ_id" id="annee_scolaire" class="form-select form-select-sm">
                <option value="">Sélectionnez categorie</option>
                @foreach($categories as $categorie)
                <option value="{{ $categorie->id }}">{{ $categorie->libelle }}</option>
                @endforeach
              </select>
              @error('categ_id') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>
          </div>
          <div class="col-md-2">
            <button type="submit" class="btn btn-primary btn-sm w-100">
              <i class="fas fa-search"></i> Rechercher
            </button>
          </div>
        </div>
      </form>

    </div>
    @if($employes)
    <div class="d-flex justify-content-between align-items-center mt-3 mb-2">
      <div class="w-50">
        <input type="text" class="form-control form-control-sm" placeholder="Rechercher..." wire:model.live="search">
      </div>
      <div>
        <button class="btn btn-success btn-sm" wire:click="imprimer"><i class="fas fa-print"></i> Imprimer</button>
      </div>
    </div>
    <div class="table-responsive">
      <table class="table table-bordered table-striped table-sm">
        <thead class="table-primary">
          <tr>
            <th>#</th>
            <th>Photo</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Fonction</th>
            <th>Date d'embauche</th>
            <th>N° CNSS</th>
            <th>Salaire de base</th>
          </tr>
        </thead>
        <tbody>
          @php $i = ($employes instanceof \Illuminate\Pagination\LengthAwarePaginator) ?
          ($employes->currentPage() - 1) *
          $employes->perPage() + 1 : 1; @endphp
          @forelse($employes as $employe)
          <tr>
            <td>{{ $i++ }}</td>
            <td>
              @if($employe->photo_emp)
              <img src="{{ asset('uploads/' . $employe->photo_emp) }}" alt="Photo" class="rounded-circle"
                style="width:40px; height:40px; object-fit:cover;">
              @else
              <span class="text-muted">-</span>
              @endif
            </td>
            <td>{{ $employe->nom }}</td>
            <td>{{ $employe->prenom }}</td>
            <td>{{ $employe->fonction ? $employe->fonction->libelle : '-' }}</td>
            <td>{{ $employe->date_embauche }}</td>
            <td>{{ $employe->num_cnss }}</td>
            <td>{{ number_format($employe->salaire_base, 2, ',', ' ') }}</td>
          </tr>
          @empty
          <tr>
            <td colspan="6" class="text-center">Aucun employé trouvé pour cette catégorie.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
      <div class="mt-2">
        {{ $employes->links() }}
      </div>
    </div>
    @endif
  </div>
</div>
@script()
<script>
  $(document).ready(function(){
    window.addEventListener('openEtatWindow', event => {
      // Access the URL from the event detail
      const url = event.detail.url;
      if (url) {
        window.open(url, '_blank');
      } else {
        // Fallback or error handling if URL is not provided, though it should be
        console.error('PDF URL not provided in event detail.');
      }
    });
  })
</script>
@endscript