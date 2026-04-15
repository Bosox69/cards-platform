@extends('layouts.admin')

@section('title', 'Nouveau modèle')

@section('content')
<div class="page-header mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h1>Nouveau modèle de carte</h1>
        <p class="lead">Créez un nouveau modèle pour un département client.</p>
    </div>
    <a href="{{ route('admin.templates.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Retour à la liste
    </a>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Veuillez corriger les erreurs suivantes :</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.templates.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header py-3">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>Informations de base</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Nom du modèle</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                               class="form-control @error('name') is-invalid @enderror">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="client_id" class="form-label fw-semibold">Client</label>
                        <select id="client_id" class="form-select" onchange="loadDepartments(this.value)">
                            <option value="">Sélectionnez un client</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                    {{ $client->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="department_id" class="form-label fw-semibold">Département</label>
                        <select id="department_id" name="department_id" required
                                class="form-select @error('department_id') is-invalid @enderror">
                            <option value="">Sélectionnez d'abord un client</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">Description</label>
                        <textarea id="description" name="description" rows="3" class="form-control">{{ old('description') }}</textarea>
                    </div>

                    <div class="form-check form-switch">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                               {{ old('is_active', '1') ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="is_active">Modèle actif</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header py-3">
                    <h5 class="mb-0"><i class="fas fa-image me-2 text-primary"></i>Images et mise en page</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="background_front" class="form-label fw-semibold">Arrière-plan recto</label>
                        <input type="file" id="background_front" name="background_front" accept="image/jpeg,image/png"
                               class="form-control @error('background_front') is-invalid @enderror">
                        <div class="form-text">JPG ou PNG, 2 Mo max.</div>
                        @error('background_front') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="background_back" class="form-label fw-semibold">Arrière-plan verso</label>
                        <input type="file" id="background_back" name="background_back" accept="image/jpeg,image/png"
                               class="form-control @error('background_back') is-invalid @enderror">
                        @error('background_back') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="back_content" class="form-label fw-semibold">Contenu du verso</label>
                        <textarea id="back_content" name="back_content" rows="3" class="form-control">{{ old('back_content') }}</textarea>
                    </div>

                    <h6 class="fw-semibold mt-4 mb-3 text-secondary text-uppercase small">
                        <i class="fas fa-arrows-alt me-1"></i>Positionnement des éléments
                    </h6>

                    <div class="row g-2 mb-2">
                        <div class="col-md-4">
                            <label for="logo_x" class="form-label small">Logo X</label>
                            <input type="number" step="0.1" id="logo_x" name="logo_x" value="{{ old('logo_x') }}" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="logo_y" class="form-label small">Logo Y</label>
                            <input type="number" step="0.1" id="logo_y" name="logo_y" value="{{ old('logo_y') }}" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="logo_width" class="form-label small">Largeur logo</label>
                            <input type="number" step="0.1" id="logo_width" name="logo_width" value="{{ old('logo_width') }}" class="form-control">
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-6">
                            <label for="text_start_x" class="form-label small">Début X du texte</label>
                            <input type="number" step="0.1" id="text_start_x" name="text_start_x" value="{{ old('text_start_x') }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="text_start_y" class="form-label small">Début Y du texte</label>
                            <input type="number" step="0.1" id="text_start_y" name="text_start_y" value="{{ old('text_start_y') }}" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-end gap-2">
        <a href="{{ route('admin.templates.index') }}" class="btn btn-outline-secondary">Annuler</a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-2"></i>Créer le modèle
        </button>
    </div>
</form>
@endsection

@push('scripts')
<script>
    function loadDepartments(clientId) {
        const select = document.getElementById('department_id');
        if (!clientId) {
            select.innerHTML = '<option value="">Sélectionnez d\'abord un client</option>';
            return;
        }

        fetch(`/admin/clients/${clientId}/departments`)
            .then(response => response.json())
            .then(departments => {
                select.innerHTML = '<option value="">Sélectionnez un département</option>';
                departments.forEach(dept => {
                    const opt = document.createElement('option');
                    opt.value = dept.id;
                    opt.textContent = dept.name;
                    select.appendChild(opt);
                });
            })
            .catch(error => console.error('Erreur lors du chargement des départements:', error));
    }
</script>
@endpush
