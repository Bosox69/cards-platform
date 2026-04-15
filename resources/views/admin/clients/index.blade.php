@extends('layouts.admin')

@section('title', 'Gestion des clients')

@section('content')
<div class="page-header mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h1>Gestion des clients</h1>
        <p class="lead">Liste des clients, départements et commandes.</p>
    </div>
    <a href="{{ route('admin.clients.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Nouveau client
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($clients->isEmpty())
            <div class="text-center py-5 px-3">
                <i class="fas fa-building fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-3">Aucun client enregistré pour le moment.</p>
                <a href="{{ route('admin.clients.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Créer le premier client
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Logo</th>
                            <th>Nom</th>
                            <th>Contact</th>
                            <th>Ville</th>
                            <th class="text-center">Départements</th>
                            <th class="text-center">Commandes</th>
                            <th class="text-center">Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clients as $client)
                            <tr>
                                <td style="width: 60px;">
                                    @if($client->logo && Storage::disk('public')->exists($client->logo))
                                        <img src="{{ Storage::url($client->logo) }}" alt="{{ $client->name }}"
                                             class="rounded border" style="width:40px;height:40px;object-fit:contain;">
                                    @else
                                        <div class="rounded bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center"
                                             style="width:40px;height:40px;">
                                            <i class="fas fa-building"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $client->name }}</div>
                                    @if($client->email)
                                        <div class="small text-muted">{{ $client->email }}</div>
                                    @endif
                                </td>
                                <td>{{ $client->contact_person ?? '—' }}</td>
                                <td>{{ $client->city ?? '—' }}</td>
                                <td class="text-center">
                                    <span class="badge bg-primary-subtle text-primary">{{ $client->departments_count }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info-subtle text-info">{{ $client->orders_count }}</span>
                                </td>
                                <td class="text-center">
                                    @if($client->is_active)
                                        <span class="badge bg-success-subtle text-success">Actif</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">Inactif</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.clients.edit', $client) }}" class="btn btn-sm btn-light border" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.clients.destroy', $client) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Supprimer le client {{ $client->name }} ?');">
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
    @if($clients->hasPages())
        <div class="card-footer bg-white border-top-0">
            {{ $clients->links() }}
        </div>
    @endif
</div>
@endsection
