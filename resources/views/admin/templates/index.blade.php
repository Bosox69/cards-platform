@extends('layouts.admin')

@section('title', 'Gestion des modèles')

@section('content')
<div class="page-header mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h1>Gestion des modèles</h1>
        <p class="lead">Modèles de cartes de visite par client et département.</p>
    </div>
    <a href="{{ route('admin.templates.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Nouveau modèle
    </a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.templates.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="client_id" class="form-label fw-semibold small">Client</label>
                <select id="client_id" name="client_id" class="form-select" onchange="loadDepartments(this.value)">
                    <option value="">Tous les clients</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                            {{ $client->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="department_id" class="form-label fw-semibold small">Département</label>
                <select id="department_id" name="department_id" class="form-select">
                    <option value="">Tous les départements</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="fas fa-filter me-1"></i> Filtrer
                </button>
                <a href="{{ route('admin.templates.index') }}" class="btn btn-outline-secondary" title="Réinitialiser">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($templates->isEmpty())
            <div class="text-center py-5 px-3">
                <i class="fas fa-layer-group fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-3">Aucun modèle pour le moment.</p>
                <a href="{{ route('admin.templates.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Créer un modèle
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Aperçu</th>
                            <th>Nom</th>
                            <th>Client / Département</th>
                            <th class="text-center">Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($templates as $template)
                            <tr>
                                <td style="width:100px;">
                                    @if($template->background_front && Storage::disk('public')->exists($template->background_front))
                                        <img src="{{ Storage::url($template->background_front) }}" alt="Aperçu"
                                             class="rounded border" style="height:48px;width:80px;object-fit:cover;">
                                    @else
                                        <div class="rounded bg-light border d-flex align-items-center justify-content-center"
                                             style="height:48px;width:80px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $template->name }}</div>
                                    <div class="small text-muted">ID : {{ $template->id }}</div>
                                </td>
                                <td>
                                    <div>{{ $template->department->client->name ?? '—' }}</div>
                                    <div class="small text-muted">{{ $template->department->name ?? '' }}</div>
                                </td>
                                <td class="text-center">
                                    @if($template->is_active)
                                        <span class="badge bg-success-subtle text-success">Actif</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">Inactif</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.templates.edit', $template->id) }}" class="btn btn-sm btn-light border" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.templates.destroy', $template->id) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Supprimer ce modèle ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border text-danger" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    @if($templates->hasPages())
        <div class="card-footer bg-white border-top-0">
            {{ $templates->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    function loadDepartments(clientId) {
        const select = document.getElementById('department_id');
        if (!clientId) {
            select.innerHTML = '<option value="">Tous les départements</option>';
            return;
        }
        fetch(`/admin/clients/${clientId}/departments`)
            .then(r => r.json())
            .then(departments => {
                select.innerHTML = '<option value="">Tous les départements</option>';
                departments.forEach(d => {
                    const opt = document.createElement('option');
                    opt.value = d.id; opt.textContent = d.name;
                    select.appendChild(opt);
                });
            });
    }
</script>
@endpush
