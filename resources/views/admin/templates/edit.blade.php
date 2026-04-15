@extends('layouts.admin')

@section('title', 'Modifier ' . $template->name)

@section('content')
<div class="page-header mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h1>Modifier le modèle</h1>
        <p class="lead">{{ $template->name }}</p>
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

<form action="{{ route('admin.templates.update', $template->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header py-3">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>Informations de base</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Nom du modèle</label>
                        <input type="text" id="name" name="name" required
                               value="{{ old('name', $template->name) }}"
                               class="form-control @error('name') is-invalid @enderror">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="client_id" class="form-label fw-semibold">Client</label>
                        <select id="client_id" class="form-select" onchange="loadDepartments(this.value)">
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ $template->department->client_id == $client->id ? 'selected' : '' }}>
                                    {{ $client->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="department_id" class="form-label fw-semibold">Département</label>
                        <select id="department_id" name="department_id" required class="form-select @error('department_id') is-invalid @enderror">
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ $template->department_id == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">Description</label>
                        <textarea id="description" name="description" rows="3" class="form-control">{{ old('description', $template->description) }}</textarea>
                    </div>

                    <div class="form-check form-switch">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                               {{ $template->is_active ? 'checked' : '' }}>
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
                        @if($template->background_front && Storage::disk('public')->exists($template->background_front))
                            <div class="mb-2">
                                <img src="{{ Storage::url($template->background_front) }}" alt="Recto actuel"
                                     class="border rounded" style="max-height:90px;">
                            </div>
                        @endif
                        <input type="file" id="background_front" name="background_front" accept="image/jpeg,image/png"
                               class="form-control @error('background_front') is-invalid @enderror">
                        @error('background_front') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="background_back" class="form-label fw-semibold">Arrière-plan verso</label>
                        @if($template->background_back && Storage::disk('public')->exists($template->background_back))
                            <div class="mb-2">
                                <img src="{{ Storage::url($template->background_back) }}" alt="Verso actuel"
                                     class="border rounded" style="max-height:90px;">
                            </div>
                        @endif
                        <input type="file" id="background_back" name="background_back" accept="image/jpeg,image/png"
                               class="form-control @error('background_back') is-invalid @enderror">
                        @error('background_back') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="back_content" class="form-label fw-semibold">Contenu du verso</label>
                        <textarea id="back_content" name="back_content" rows="3" class="form-control">{{ old('back_content', $template->back_content) }}</textarea>
                    </div>

                    <h6 class="fw-semibold mt-4 mb-3 text-secondary text-uppercase small">
                        <i class="fas fa-arrows-alt me-1"></i>Positionnement des éléments
                    </h6>

                    <div class="row g-2 mb-2">
                        <div class="col-md-4">
                            <label for="logo_x" class="form-label small">Logo X</label>
                            <input type="number" step="0.1" id="logo_x" name="logo_x" value="{{ old('logo_x', $template->logo_x) }}" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="logo_y" class="form-label small">Logo Y</label>
                            <input type="number" step="0.1" id="logo_y" name="logo_y" value="{{ old('logo_y', $template->logo_y) }}" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="logo_width" class="form-label small">Largeur logo</label>
                            <input type="number" step="0.1" id="logo_width" name="logo_width" value="{{ old('logo_width', $template->logo_width) }}" class="form-control">
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-6">
                            <label for="text_start_x" class="form-label small">Début X du texte</label>
                            <input type="number" step="0.1" id="text_start_x" name="text_start_x" value="{{ old('text_start_x', $template->text_start_x) }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="text_start_y" class="form-label small">Début Y du texte</label>
                            <input type="number" step="0.1" id="text_start_y" name="text_start_y" value="{{ old('text_start_y', $template->text_start_y) }}" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-end gap-2">
        <a href="{{ route('admin.templates.index') }}" class="btn btn-outline-secondary">Annuler</a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-2"></i>Enregistrer les modifications
        </button>
    </div>
</form>
@endsection

@push('scripts')
<script>
    function loadDepartments(clientId) {
        const select = document.getElementById('department_id');
        if (!clientId) {
            select.innerHTML = '<option value="">Sélectionnez un département</option>';
            return;
        }
        fetch(`/admin/clients/${clientId}/departments`)
            .then(r => r.json())
            .then(departments => {
                select.innerHTML = '';
                departments.forEach(d => {
                    const opt = document.createElement('option');
                    opt.value = d.id; opt.textContent = d.name;
                    select.appendChild(opt);
                });
            });
    }
</script>
@endpush
