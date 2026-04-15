@extends('layouts.admin')

@section('title', 'Tableau de bord administrateur')

@section('content')
<div class="page-header mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h1>Tableau de bord administrateur</h1>
        <p class="lead">Vue d'ensemble de l'activité de la plateforme.</p>
    </div>
    <div>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-primary">
            <i class="fas fa-box me-2"></i>Gérer les commandes
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    @php
        $tiles = [
            ['label' => 'Commandes totales', 'value' => $stats['total_orders'],      'color' => 'primary', 'icon' => 'fa-file-invoice'],
            ['label' => 'En attente',        'value' => $stats['pending_orders'],    'color' => 'warning', 'icon' => 'fa-clock'],
            ['label' => 'En production',     'value' => $stats['processing_orders'], 'color' => 'info',    'icon' => 'fa-industry'],
            ['label' => 'Complétées',        'value' => $stats['completed_orders'],  'color' => 'success', 'icon' => 'fa-check-circle'],
            ['label' => 'Clients',           'value' => $stats['clients_count'],     'color' => 'secondary','icon' => 'fa-building'],
            ['label' => 'Modèles',           'value' => $stats['templates_count'],   'color' => 'danger',  'icon' => 'fa-layer-group'],
        ];
    @endphp

    @foreach($tiles as $tile)
        <div class="col-sm-6 col-md-4 col-xl-2">
            <div class="card stat-card h-100" style="border-left-color: var(--bs-{{ $tile['color'] }});">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon bg-{{ $tile['color'] }} bg-opacity-10 text-{{ $tile['color'] }} me-3">
                        <i class="fas {{ $tile['icon'] }}"></i>
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="stat-label text-truncate">{{ $tile['label'] }}</div>
                        <div class="stat-value">{{ $tile['value'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-chart-pie me-2 text-primary"></i>Répartition des commandes</h5>
                <span class="text-muted small">Total : {{ $stats['total_orders'] }}</span>
            </div>
            <div class="card-body">
                @if($ordersByStatus->isEmpty() || $stats['total_orders'] == 0)
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-chart-bar fa-2x mb-2 opacity-50"></i>
                        <p class="mb-0">Aucune donnée à afficher pour le moment.</p>
                    </div>
                @else
                    @foreach($ordersByStatus as $status)
                        @php
                            $percentage = $stats['total_orders'] > 0
                                ? round(($status->orders_count / $stats['total_orders']) * 100)
                                : 0;
                            $barColor = match($status->name) {
                                'Nouvelle' => 'bg-info',
                                'En traitement' => 'bg-warning',
                                'En production' => 'bg-primary',
                                'Expédié' => 'bg-info-subtle',
                                'Complété' => 'bg-success',
                                'Annulé' => 'bg-danger',
                                default => 'bg-secondary',
                            };
                        @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-semibold small">{{ $status->name }}</span>
                                <span class="small text-muted">
                                    {{ $status->orders_count }} <span class="text-secondary">· {{ $percentage }}%</span>
                                </span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar {{ $barColor }}" role="progressbar"
                                     style="width: {{ $percentage }}%"
                                     aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header py-3">
                <h5 class="mb-0"><i class="fas fa-bolt me-2 text-warning"></i>Actions rapides</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <a href="{{ route('admin.orders.index', ['status' => 1]) }}" class="card card-hover h-100 text-decoration-none border">
                            <div class="card-body">
                                <div class="stat-icon bg-info bg-opacity-10 text-info mb-2"><i class="fas fa-inbox"></i></div>
                                <h6 class="mb-1">Nouvelles commandes</h6>
                                <p class="small text-muted mb-0">Traiter les commandes entrantes</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-6">
                        <a href="{{ route('admin.orders.index', ['status' => 2]) }}" class="card card-hover h-100 text-decoration-none border">
                            <div class="card-body">
                                <div class="stat-icon bg-warning bg-opacity-10 text-warning mb-2"><i class="fas fa-spinner"></i></div>
                                <h6 class="mb-1">En traitement</h6>
                                <p class="small text-muted mb-0">Gérer les commandes en cours</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-6">
                        <a href="{{ route('admin.templates.index') }}" class="card card-hover h-100 text-decoration-none border">
                            <div class="card-body">
                                <div class="stat-icon bg-primary bg-opacity-10 text-primary mb-2"><i class="fas fa-id-card"></i></div>
                                <h6 class="mb-1">Modèles</h6>
                                <p class="small text-muted mb-0">Gérer les modèles de cartes</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-6">
                        <a href="{{ route('admin.orders.index') }}" class="card card-hover h-100 text-decoration-none border">
                            <div class="card-body">
                                <div class="stat-icon bg-success bg-opacity-10 text-success mb-2"><i class="fas fa-list"></i></div>
                                <h6 class="mb-1">Toutes les commandes</h6>
                                <p class="small text-muted mb-0">Voir l'historique complet</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-history me-2 text-primary"></i>Commandes récentes</h5>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-primary">
            Voir toutes <i class="fas fa-arrow-right ms-1"></i>
        </a>
    </div>
    <div class="card-body p-0">
        @if($recentOrders->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>N° Commande</th>
                            <th>Client</th>
                            <th>Utilisateur</th>
                            <th>Date</th>
                            <th class="text-center">Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentOrders as $order)
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
                                <td class="fw-semibold text-primary">#{{ $order->id }}</td>
                                <td>{{ $order->client->name ?? '—' }}</td>
                                <td>{{ $order->user->name ?? '—' }}</td>
                                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td class="text-center">
                                    <span class="badge rounded-pill {{ $badge }}">{{ $statusName }}</span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-light border" title="Voir détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5 px-3">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-0">Aucune commande n'a encore été passée.</p>
            </div>
        @endif
    </div>
</div>
@endsection
