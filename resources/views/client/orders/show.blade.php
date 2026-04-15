@extends('layouts.client')

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
        <a href="{{ route('client.orders.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Mes commandes
        </a>
        <a href="{{ route('client.orders.repeat', $order) }}" class="btn btn-primary">
            <i class="fas fa-redo me-2"></i>Renouveler
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header py-3"><h6 class="mb-0"><i class="fas fa-clipboard-list me-2 text-primary"></i>Détails</h6></div>
            <div class="card-body">
                <p class="mb-1"><span class="text-muted small">Date :</span> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                <p class="mb-1"><span class="text-muted small">Articles :</span> <span class="fw-semibold">{{ $order->orderItems->count() }}</span></p>
                <p class="mb-0"><span class="text-muted small">Total cartes :</span> <span class="fw-semibold">{{ $order->orderItems->sum('quantity') }} exemplaires</span></p>
            </div>
        </div>
    </div>
    @if($order->comment)
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header py-3"><h6 class="mb-0"><i class="fas fa-comment-dots me-2 text-primary"></i>Vos commentaires</h6></div>
                <div class="card-body">
                    <p class="mb-0">{{ $order->comment }}</p>
                </div>
            </div>
        </div>
    @endif
</div>

<div class="card">
    <div class="card-header py-3"><h5 class="mb-0"><i class="fas fa-id-card me-2 text-primary"></i>Articles commandés</h5></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Modèle</th>
                        <th>Informations</th>
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
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary-subtle text-primary">{{ $item->quantity }} ex.</span>
                            </td>
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
@endsection
