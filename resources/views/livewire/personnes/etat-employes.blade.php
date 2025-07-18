<div>
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header bg-primary text-white">
            <h4 class="card-title mb-0">
              <i class="fas fa-users me-2"></i>État des Employés
            </h4>
          </div>
          <div class="card-body">
            <!-- Formulaire de configuration PDF -->
            <div class="row mb-1">
              <div class="col-md-8">
                <div class="card border-info">
                  <div class="card-header bg-info text-white">
                    <h6 class="mb-0">Configuration du PDF</h6>
                  </div>
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="mb-1">
                          <label for="nom1" class="form-label">Nom du Directeur</label>
                          <input type="text" class="form-control" id="nom1" wire:model="nom1"
                            placeholder="Nom du directeur">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label for="nom2" class="form-label">Nom du Responsable RH</label>
                          <input type="text" class="form-control" id="nom2" wire:model="nom2"
                            placeholder="Nom du responsable RH">
                        </div>
                      </div>
                    </div>
                    <div class="row align-items-end mt-2">
                      <div class="col-md-4">
                        <label for="etablissement" class="form-label">Établissement</label>
                        <select class="form-select" id="etablissement" wire:model.live="etablissement">
                          <option value="">Sélectionner...</option>
                          <option value="1,2,3">FAMA</option>
                          <option value="4">DARATALIBA</option>
                        </select>
                      </div>
                      <div class="col-md-4 d-flex justify-content-end">
                        <button type="button" class="btn btn-success w-100" wire:click="generatePdf">
                          <i class="fas fa-eye me-1"></i>Aperçu PDF
                        </button>
                      </div>
                      <div class="col-md-4 d-flex justify-content-end">
                        <button type="button" class="btn btn-success w-100" wire:click="generateExcel"
                        @ouvrir-excel.window="window.open($event.detail, '_blank')">
                          <i class="fas fa-eye me-1"></i>Aperçu Excel
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card border-success">
                  <div class="card-header bg-success text-white">
                    <h6 class="mb-0">Statistiques</h6>
                  </div>
                  <div class="card-body">
                    <div class="row text-center">
                      <div class="col-4">
                        <div class="border-end">
                          <h4 class="text-primary mb-1">{{ $totalEmployes }}</h4>
                          <small class="text-muted">Employés</small>
                        </div>
                      </div>
                      <div class="col-4">
                        <div class="border-end">
                          <h4 class="text-success mb-1">{{ number_format($totalSalaires, 0, ',', ' ') }}</h4>
                          <small class="text-muted">Total Salaires (DH)</small>
                        </div>
                      </div>
                      <div class="col-4">
                        <h4 class="text-info mb-1">{{ number_format($moyenneSalaire, 0, ',', ' ') }}</h4>
                        <small class="text-muted">Salaire Moyen (DH)</small>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Barre de recherche et tri -->
            <div class="row mb-3">
              <div class="col-md-6">
                <div class="input-group">
                  <span class="input-group-text">
                    <i class="fas fa-search"></i>
                  </span>
                  <input type="text" class="form-control" placeholder="Rechercher un employé..."
                    wire:model.live="search">
                </div>
              </div>
              <div class="col-md-6 text-end">
                <small class="text-muted">{{ $employes->total() }} employé(s) trouvé(s)</small>
              </div>
            </div>

            <!-- Tableau des employés -->
            <div class="table-responsive">
              <table class="table table-striped table-hover">
                <thead class="table-dark">
                  <tr>
                    <th scope="col" style="width: 5%">#</th>
                    <th scope="col" style="width: 20%">Photo</th>
                    <th scope="col" style="width: 25%" wire:click="sortBy('nom')" style="cursor: pointer;">
                      Nom et Prénom
                      @if($sortField === 'nom')
                      <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                      @endif
                    </th>
                    <th scope="col" style="width: 15%" wire:click="sortBy('fonction')" style="cursor: pointer;">
                      Fonction
                      @if($sortField === 'fonction')
                      <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                      @endif
                    </th>
                    <th>Categorie</th>
                    <th>N° CNSS</th>
                    {{-- <th scope="col" style="width: 10%">Sexe</th> --}}
                    <th scope="col" style="width: 15%" wire:click="sortBy('date_embauche')" style="cursor: pointer;">
                      Date Embauche
                      @if($sortField === 'date_embauche')
                      <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                      @endif
                    </th>
                    {{-- <th scope="col" style="width: 15%">Téléphone</th> --}}
                    <th scope="col" style="width: 15%" wire:click="sortBy('salaire_base')" style="cursor: pointer;">
                      Salaire Base
                      @if($sortField === 'salaire_base')
                      <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                      @endif
                    </th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($employes as $index => $employe)
                  <tr>
                    <td>{{ $employes->firstItem() + $index }}</td>
                    <td>
                      @if($employe->photo_emp)
                      <img src="{{ asset('uploads/' . basename($employe->photo_emp)) }}" alt="Photo"
                        class="rounded-circle" style="width:40px; height:40px; object-fit:cover;">
                      @else
                      <span class="text-muted">-</span>
                      @endif

                    </td>
                    <td>
                      <strong>{{ strtoupper($employe->nom) }} {{ ucfirst(strtolower($employe->prenom)) }}</strong>
                    </td>
                    <td>
                      <span class="badge bg-secondary">{{ $employe->fonction->libelle ?: 'Non définie' }}</span>
                    </td>
                    <td>
                      <span class="badge bg-secondary">{{ $employe->categorie->libelle ?: 'Non définie' }}</span>
                    </td>
                    <td>
                      <span class="badge bg-secondary">
                        {{-- {{ optional($employe->inscriptions->first())->num_cnss ?: 'Non définie' }} --}}
                        {{ $employe->num_cnss ?: 'Non définie' }}
                      </span>
                    </td>
                    {{-- <td>
                      @if($employe->sexe === 'M')
                      <i class="fas fa-mars text-primary"></i> Homme
                      @elseif($employe->sexe === 'F')
                      <i class="fas fa-venus text-danger"></i> Femme
                      @else
                      <span class="text-muted">-</span>
                      @endif
                    </td> --}}
                    <td>
                      @if($employe->date_embauche)
                      <small class="text-muted">{{ $employe->date_embauche }}</small>
                      @else
                      <span class="text-muted">-</span>
                      @endif
                    </td>
                    {{-- <td>
                      @if($employe->phone)
                      <a href="tel:{{ $employe->phone }}" class="text-decoration-none">
                        <i class="fas fa-phone text-success me-1"></i>{{ $employe->phone }}
                      </a>
                      @else
                      <span class="text-muted">-</span>
                      @endif
                    </td> --}}
                    <td>
                      @if($employe->salaire_base)
                      <strong class="text-success">{{ number_format($employe->salaire_base, 0, ',', ' ') }} DH</strong>
                      @else
                      <span class="text-muted">Non défini</span>
                      @endif
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="7" class="text-center py-4">
                      <div class="text-muted">
                        <i class="fas fa-users fa-3x mb-3"></i>
                        <p>Aucun employé trouvé</p>
                        @if($search)
                        <small>Essayez de modifier votre recherche</small>
                        @endif
                      </div>
                    </td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
              {{ $employes->links() }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@push('styles')
<style>
  .table th[wire\:click] {
    cursor: pointer;
    user-select: none;
  }

  .table th[wire\:click]:hover {
    background-color: rgba(0, 0, 0, 0.1);
  }

  .card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
  }

  .badge {
    font-size: 0.75em;
  }
</style>
@endpush
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
        // Optionally, open the static route as a fallback if that makes sense
        // window.open('{{ route('generate.pdf') }}', '_blank'); 
      }
    });
  })
</script>
@endscript