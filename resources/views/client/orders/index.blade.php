@extends('layouts.client')

@section('title', 'Mes commandes')

@section('content')
<div class="page-header mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h1>Mes commandes</h1>
        <p class="lead">Historique de vos commandes et suivi des statuts.</p>
    </div>
    <a href="{{ route('client.orders.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Nouvelle commande
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($orders->isEmpty())
            <div class="text-center py-5 px-3">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-3">Vous n'avez pas encore passé de commande.</p>
                <a href="{{ route('client.orders.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Passer ma première commande
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Date</th>
                            <th class="text-center">Articles</th>
                            <th class="text-center">Cartes</th>
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
                                <td class="fw-semibold text-primary">#{{ $order->id }}</td>
                                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td class="text-center">{{ $order->orderItems->count() }}</td>
                                <td class="text-center">{{ $order->orderItems->sum('quantity') }} ex.</td>
                                <td class="text-center">
                                    <span class="badge rounded-pill {{ $badge }}">{{ $statusName }}</span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('client.orders.show', $order) }}" class="btn btn-sm btn-light border" title="Détails">
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
    @if($orders->hasPages())
        <div class="card-footer bg-white border-top-0">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection
