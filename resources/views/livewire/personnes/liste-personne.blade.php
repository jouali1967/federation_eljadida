<div class="container mt-1">
  {{-- <div class="row justify-content-center"> --}}
    {{-- <div class="col-md-10"> --}}
      <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Liste des personnes</h5>
          <a href="{{ route('personnes.create') }}" class="btn btn-light btn-sm">
            <i class="fas fa-plus me-1"></i> Nouvelle personne
          </a>
        </div>

        <div class="card-body">
          <div class="mb-3">
            <div class="input-group" style="max-width: 900px;">
              <span class="input-group-text"><i class="fas fa-search"></i></span>
              <input type="text" class="form-control" wire:model.live="search" placeholder="Rechercher...">
              <select id="categorie" class="form-select form-select-sm ms-2" wire:model.live="categ_id">
                <option value="">Sélectionnez une catégorie</option>
                @foreach ($categories as $categorie)
                <option value="{{ $categorie->id }}">{{ $categorie->libelle }}</option>
                @endforeach
              </select>
              <select id="fonction" class="form-select form-select-sm ms-2" wire:model.live="fonction_id">
                <option value="">Sélectionnez une fonction</option>
                @foreach ($fonctions as $fonction)
                <option value="{{ $fonction->id }}">{{ $fonction->libelle }}</option>
                @endforeach
              </select>
              <select id="status" class="form-select form-select-sm ms-2" wire:model.live="status">
                <option value="">Sélectionnez status</option>
                <option value="1">Actif</option>
                <option value="0">Inactif</option>
              </select>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table table-hover table-bordered table-sm table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Photo</th>
                  <th wire:click="sortBy('nom')" style="cursor: pointer;">
                    Nom
                    @if($sortField === 'nom')
                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                    @else
                    <i class="fas fa-sort ms-1 text-muted"></i>
                    @endif
                  </th>
                  <th wire:click="sortBy('prenom')" style="cursor: pointer;">
                    Prénom
                    @if($sortField === 'prenom')
                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                    @else
                    <i class="fas fa-sort ms-1 text-muted"></i>
                    @endif
                  </th>
                  <th>Fonction</th>
                  <th>Categorie</th>
                  <th wire:click="sortBy('date_embauche')" style="cursor: pointer;">
                    Date d'embauche
                    @if($sortField === 'date_embauche')
                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                    @else
                    <i class="fas fa-sort ms-1 text-muted"></i>
                    @endif
                  </th>
                  <th>Status</th>
                  <th wire:click="sortBy('date_nais')" style="cursor: pointer;">
                    Date de Naissance
                    @if($sortField === 'date_nais')
                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                    @else
                    <i class="fas fa-sort ms-1 text-muted"></i>
                    @endif
                  </th>
                  <th class="text-center">Actions</th>
                </tr>
              </thead>
              <tbody>
                @php $i = ($personnes instanceof \Illuminate\Pagination\LengthAwarePaginator) ?
                ($personnes->currentPage() - 1) *
                $personnes->perPage() + 1 : 1; @endphp
                @forelse($personnes as $personne)
                <tr>
                  <td>{{ $i++ }}</td>
                  <td>
                    @if($personne->photo_emp)
                    <img src="{{ asset('storage/' . $personne->photo_emp) }}" alt="Photo" class="rounded-circle"
                      style="width:40px; height:40px; object-fit:cover;">
                    @else
                    <span class="text-muted">-</span>
                    @endif
                  </td>
                  <td>{{ $personne->nom }}</td>
                  <td>{{ $personne->prenom }}</td>
                  <td>{{ $personne->fonction->libelle }}</td>
                  <td>{{ $personne->categorie->libelle }}</td>
                  <td>{{ $personne->date_embauche }}</td>
                  <td>
                    @if($personne->status)
                    <span class="badge bg-success">Actif</span>
                    @else
                    <span class="badge bg-danger">Inactif</span>
                    @endif
                  </td>
                  <td>{{ $personne->date_nais }}</td>
                  <td class="text-center">
                    <a href="{{ route('personnes.edit', $personne->id) }}" class="btn btn-sm btn-info me-1"
                      title="Modifier">
                      <i class="fas fa-edit"></i>
                    </a>
                    <button wire:click="delete({{ $personne->id }})" class="btn btn-sm btn-danger" title="Supprimer"
                      onclick="confirm('Êtes-vous sûr de vouloir supprimer cette personne ?') || event.stopImmediatePropagation()">
                      <i class="fas fa-trash"></i>
                    </button>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="5" class="text-center">Aucune personne trouvée</td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <div class="mt-3">
            {{ $personnes->links() }}
          </div>
        </div>
      </div>
      {{--
    </div> --}}
    {{-- </div> --}}
</div>

@script()
<script>
  // Gestion des notifications de suppression
    Livewire.on('personne-deleted', (data) => {
        Swal.fire({
            title: data.title,
            text: data.message,
            icon: data.type,
            confirmButtonText: 'OK'
        });
    });
</script>
@endscript