@extends('layouts.admin')

@section('title', 'Gestion des commandes')

@section('content')
<div class="page-header mb-4">
    <h1>Gestion des commandes</h1>
    <p class="lead">Filtrer, consulter et mettre à jour le statut des commandes.</p>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.orders.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="status" class="form-label fw-semibold small">Statut</label>
                <select id="status" name="status" class="form-select">
                    <option value="">Tous les statuts</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->id }}" {{ request('status') == $status->id ? 'selected' : '' }}>
                            {{ $status->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="client" class="form-label fw-semibold small">Client</label>
                <select id="client" name="client" class="form-select">
                    <option value="">Tous les clients</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ request('client') == $client->id ? 'selected' : '' }}>
                            {{ $client->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="date_from" class="form-label fw-semibold small">Du</label>
                <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}" class="form-control">
            </div>
            <div class="col-md-2">
                <label for="date_to" class="form-label fw-semibold small">Au</label>
                <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}" class="form-control">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="fas fa-filter me-1"></i> Filtrer
                </button>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary" title="Réinitialiser">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($orders->isEmpty())
            <div class="text-center py-5 px-3">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-0">Aucune commande trouvée avec ces filtres.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Commande</th>
                            <th>Client</th>
                            <th>Date</th>
                            <th class="text-center">Articles</th>
                            <th class="text-center">Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            @php
                                $statusName = $order->orderStatus->name ?? 'Inconnu';
                                $badge = match($statusName) {
                                    'Nouvelle' => 'bg-info-subtle text-info',
                                    'En traitement' => 'bg-warning-subtle text-warning',
                                    'En production' => 'bg-primary-subtle text-primary',
                                    'Expédié' => 'bg-info-subtle text-info',
                                    'Complété' => 'bg-success-subtle text-success',
                                    'Annulé' => 'bg-danger-subtle text-danger',
                                    default => 'bg-secondary-subtle text-secondary',
                                };
                            @endphp
                            <tr>
                                <td>
                                    <div class="fw-semibold text-primary">#{{ $order->id }}</div>
                                    <div class="small text-muted">{{ $order->user->name }}</div>
                                </td>
                                <td>{{ $order->client->name }}</td>
                                <td>
                                    <div>{{ $order->created_at->format('d/m/Y') }}</div>
                                    <div class="small text-muted">{{ $order->created_at->format('H:i') }}</div>
                                </td>
                                <td class="text-center">
                                    <div>{{ $order->orderItems->count() }} modèles</div>
                                    <div class="small text-muted">{{ $order->orderItems->sum('quantity') }} ex.</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill {{ $badge }}">{{ $statusName }}</span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-light border" title="Détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-light border dropdown-toggle"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                            Statut
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                            @foreach($statuses as $status)
                                                <li>
                                                    <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="order_status_id" value="{{ $status->id }}">
                                                        <button type="submit"
                                                                class="dropdown-item {{ $order->order_status_id == $status->id ? 'active' : '' }}">
                                                            {{ $status->name }}
                                                        </button>
                                                    </form>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    @if($orders->hasPages())
        <div class="card-footer bg-white border-top-0">
            {{ $orders->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection
