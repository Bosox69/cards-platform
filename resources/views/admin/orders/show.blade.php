@extends('layouts.admin')

@section('title', 'Commande #' . $order->id)

@section('content')
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

<div class="page-header mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h1>Commande #{{ $order->id }}</h1>
        <p class="lead">
            <span class="badge rounded-pill {{ $badge }}">{{ $statusName }}</span>
            <span class="text-muted ms-2">Passée le {{ $order->created_at->format('d/m/Y à H:i') }}</span>
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour
        </a>
        <div class="btn-group">
            <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-exchange-alt me-2"></i>Changer le statut
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
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header py-3"><h6 class="mb-0"><i class="fas fa-building me-2 text-primary"></i>Client</h6></div>
            <div class="card-body">
                <p class="mb-1"><span class="text-muted small">Société :</span> <span class="fw-semibold">{{ $order->client->name }}</span></p>
                <p class="mb-1"><span class="text-muted small">Utilisateur :</span> {{ $order->user->name }}</p>
                <p class="mb-0"><span class="text-muted small">Email :</span> {{ $order->user->email }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header py-3"><h6 class="mb-0"><i class="fas fa-clipboard-list me-2 text-primary"></i>Détails</h6></div>
            <div class="card-body">
                <p class="mb-1"><span class="text-muted small">Date :</span> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                <p class="mb-1"><span class="text-muted small">Articles :</span> <span class="fw-semibold">{{ $order->orderItems->count() }}</span></p>
                <p class="mb-0"><span class="text-muted small">Total cartes :</span> <span class="fw-semibold">{{ $order->orderItems->sum('quantity') }} exemplaires</span></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header py-3"><h6 class="mb-0"><i class="fas fa-history me-2 text-primary"></i>Historique</h6></div>
            <div class="card-body p-0">
                @if($order->statusHistory->isEmpty())
                    <p class="text-muted small mb-0 p-3">Aucun changement enregistré.</p>
                @else
                    <ul class="list-group list-group-flush small">
                        @foreach($order->statusHistory as $history)
                            <li class="list-group-item">
                                <div class="fw-semibold">{{ $history->status->name ?? 'Statut' }}</div>
                                <div class="text-muted">{{ $history->created_at->format('d/m/Y H:i') }}</div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header py-3"><h5 class="mb-0"><i class="fas fa-id-card me-2 text-primary"></i>Articles</h5></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Modèle / Département</th>
                        <th>Informations personnalisées</th>
                        <th class="text-center">Quantité</th>
                        <th class="text-center">Type</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->orderItems as $item)
                        @php $cardData = json_decode(optional($item->cardData)->data ?? '{}', true); @endphp
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $item->template->name ?? '—' }}</div>
                                <div class="small text-muted">{{ $item->template->department->name ?? '' }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $cardData['fullName'] ?? 'N/A' }}</div>
                                <div class="small text-muted">{{ $cardData['jobTitle'] ?? '' }}</div>
                                @if(!empty($cardData['email']))  <div class="small"><i class="fas fa-envelope me-1 text-secondary"></i>{{ $cardData['email'] }}</div> @endif
                                @if(!empty($cardData['phone']))  <div class="small"><i class="fas fa-phone me-1 text-secondary"></i>{{ $cardData['phone'] }}</div> @endif
                                @if(!empty($cardData['mobile'])) <div class="small"><i class="fas fa-mobile-alt me-1 text-secondary"></i>{{ $cardData['mobile'] }}</div> @endif
                            </td>
                            <td class="text-center"><span class="badge bg-primary-subtle text-primary">{{ $item->quantity }} ex.</span></td>
                            <td class="text-center">
                                @if($item->is_double_sided)
                                    <span class="badge bg-success-subtle text-success">Recto-verso</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">Recto seul</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@if($order->comment)
    <div class="card">
        <div class="card-header py-3"><h6 class="mb-0"><i class="fas fa-comment-dots me-2 text-primary"></i>Commentaires du client</h6></div>
        <div class="card-body">
            <p class="mb-0">{{ $order->comment }}</p>
        </div>
    </div>
@endif
@endsection
