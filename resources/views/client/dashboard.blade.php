@extends('layouts.client')

@section('title', 'Tableau de bord')

@section('content')
<div class="page-header mb-4">
    <h1>Bonjour, {{ Auth::user()->name }} <span class="text-muted fw-normal">👋</span></h1>
    <p class="lead">Bienvenue sur votre espace de commande de cartes de visite.</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card h-100" style="border-left-color: var(--bs-primary);">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                    <i class="fas fa-file-invoice"></i>
                </div>
                <div>
                    <div class="stat-label">Total des commandes</div>
                    <div class="stat-value">{{ $stats['total'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card h-100" style="border-left-color: var(--bs-warning);">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning me-3">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <div class="stat-label">En cours</div>
                    <div class="stat-value">{{ $stats['pending'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card h-100" style="border-left-color: var(--bs-success);">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-success bg-opacity-10 text-success me-3">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <div class="stat-label">Complétées</div>
                    <div class="stat-value">{{ $stats['completed'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card h-100" style="border-left-color: var(--bs-info);">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-info bg-opacity-10 text-info me-3">
                    <i class="fas fa-id-card"></i>
                </div>
                <div>
                    <div class="stat-label">Cartes commandées</div>
                    <div class="stat-value">{{ $stats['total_cards'] }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0"><i class="fas fa-history me-2 text-primary"></i>Commandes récentes</h5>
                <a href="{{ route('client.orders.index') }}" class="btn btn-sm btn-outline-primary">
                    Voir tout <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                @if($recentOrders->isEmpty())
                    <div class="text-center py-5 px-3">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted mb-3">Vous n'avez pas encore passé de commande.</p>
                        <a href="{{ route('client.orders.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Créer ma première commande
                        </a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Référence</th>
                                    <th>Date</th>
                                    <th class="text-center">Articles</th>
                                    <th class="text-center">Statut</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $order)
                                    <tr>
                                        <td class="fw-semibold text-primary">#{{ $order->reference ?? $order->id }}</td>
                                        <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                        <td class="text-center">{{ $order->orderItems->count() }}</td>
                                        <td class="text-center">
                                            @php
                                                $status = $order->orderStatus->name ?? 'Inconnu';
                                                $badge = match($status) {
                                                    'Nouvelle' => 'bg-info-subtle text-info',
                                                    'En traitement' => 'bg-warning-subtle text-warning',
                                                    'Complété' => 'bg-success-subtle text-success',
                                                    default => 'bg-secondary-subtle text-secondary',
                                                };
                                            @endphp
                                            <span class="badge rounded-pill {{ $badge }}">{{ $status }}</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('client.orders.show', $order) }}" class="btn btn-sm btn-light border" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('client.orders.repeat', $order) }}" class="btn btn-sm btn-light border" title="Renouveler">
                                                <i class="fas fa-redo"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header py-3">
                <h5 class="mb-0"><i class="fas fa-bolt me-2 text-warning"></i>Actions rapides</h5>
            </div>
            <div class="card-body d-grid gap-2">
                <a href="{{ route('client.orders.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Nouvelle commande
                </a>
                <a href="{{ route('client.orders.cart') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-shopping-cart me-2"></i>Voir mon panier
                </a>
            </div>
        </div>

        @if($departments->isNotEmpty())
            <div class="card">
                <div class="card-header py-3">
                    <h5 class="mb-0"><i class="fas fa-building me-2 text-primary"></i>Mes départements</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush dept-list">
                        @foreach($departments as $department)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="fw-medium">{{ $department->name }}</span>
                                <a href="{{ route('client.orders.create', ['department' => $department->id]) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    Commander <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
