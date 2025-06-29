<div class="card">
  <div class="card-header bg-primary text-white">
    <h3 class="card-title mb-0">Créer Augmentation</h3>
  </div>

  <div class="card-body">
    @if (session()->has('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    <form wire:submit="save">
      <div class="row mb-3">
        <div class="col-5">
          <label class="form-label">Type d'augmentation</label>
          <div class="input-group">
            <select class="form-select" wire:model="type">
              <option value="fixe">Montant fixe (DH)</option>
              <option value="pourcentage">Pourcentage (%)</option>
            </select>
          </div>
          @error('type')
          <div class="invalid-feedback d-block">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-6">
          <label class="form-label">Date d'augmentation</label>
          <div class="input-group">
            <input id="date_aug" wire:model="date_aug"
              class="form-control @error('date_aug') is-invalid @enderror" placeholder="JJ/MM/AAAA">
            <span class="input-group-text">
              <i class="fas fa-calendar"></i>
            </span>
          </div>
          @error('date_aug')
          <div class="invalid-feedback d-block">{{ $message }}</div>
          @enderror
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-6">
          <label class="form-label">Valeur</label>
          <input type="number" step="0.01" wire:model="valeur" class="form-control" placeholder="Ex: 500 ou 10">
          @error('valeur') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
        <div class="col-6">
          <label class="form-label">Motif (optionnel)</label>
          <textarea class="form-control" wire:model="motif" rows="2"></textarea>
        </div>
      </div>
      <div class="d-flex justify-content-end">
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-save me-2"></i>Enregistrer
        </button>
      </div>
  </div>
  </form>
</div>

@script()
<script>
  $(document).ready(function(){
    flatpickr("#date_aug", {
      dateFormat: "d/m/Y",
      locale: 'fr',
      onChange: function(selectedDates, dateStr) {
        $wire.set('date_aug', dateStr);
      }
    });
  })
</script>
@endscript