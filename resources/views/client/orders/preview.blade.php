@extends('layouts.client')

@section('title', 'Prévisualisation de carte')

@section('content')
<div class="page-header mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h1>Prévisualisation</h1>
        <p class="lead">Vérifiez l'aperçu avant de valider votre commande.</p>
    </div>
    <a href="{{ route('client.orders.cart') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Retour au panier
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header py-3">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>Informations</h5>
            </div>
            <div class="card-body">
                <dl class="row mb-0 small">
                    <dt class="col-5 text-muted">Modèle</dt>
                    <dd class="col-7 fw-semibold">{{ $item['template_name'] }}</dd>

                    <dt class="col-5 text-muted">Département</dt>
                    <dd class="col-7">{{ $item['department_name'] }}</dd>

                    <dt class="col-5 text-muted">Nom</dt>
                    <dd class="col-7 fw-semibold">{{ $item['card_data']['fullName'] }}</dd>

                    <dt class="col-5 text-muted">Fonction</dt>
                    <dd class="col-7">{{ $item['card_data']['jobTitle'] }}</dd>

                    @if(!empty($item['card_data']['email']))
                        <dt class="col-5 text-muted">Email</dt>
                        <dd class="col-7">{{ $item['card_data']['email'] }}</dd>
                    @endif
                    @if(!empty($item['card_data']['phone']))
                        <dt class="col-5 text-muted">Téléphone</dt>
                        <dd class="col-7">{{ $item['card_data']['phone'] }}</dd>
                    @endif
                    @if(!empty($item['card_data']['mobile']))
                        <dt class="col-5 text-muted">Mobile</dt>
                        <dd class="col-7">{{ $item['card_data']['mobile'] }}</dd>
                    @endif
                    @if(!empty($item['card_data']['address']))
                        <dt class="col-5 text-muted">Adresse</dt>
                        <dd class="col-7">{{ $item['card_data']['address'] }}</dd>
                    @endif

                    <dt class="col-5 text-muted pt-2">Quantité</dt>
                    <dd class="col-7 pt-2">
                        <span class="badge bg-primary-subtle text-primary">{{ $item['quantity'] }} exemplaires</span>
                    </dd>

                    <dt class="col-5 text-muted">Impression</dt>
                    <dd class="col-7">
                        @if($item['is_double_sided'])
                            <span class="badge bg-success-subtle text-success">Recto-verso</span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary">Recto seul</span>
                        @endif
                    </dd>
                </dl>
            </div>
            <div class="card-footer bg-white d-flex gap-2">
                <a href="{{ route('client.orders.edit', ['itemId' => request()->itemId]) }}" class="btn btn-primary flex-grow-1">
                    <i class="fas fa-edit me-2"></i>Modifier
                </a>
                <a href="{{ route('client.orders.cart') }}" class="btn btn-outline-secondary">Panier</a>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-file-pdf me-2 text-primary"></i>Aperçu PDF</h5>
                <a href="{{ $pdfUrl }}" target="_blank" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-external-link-alt me-1"></i>Ouvrir
                </a>
            </div>
            <div class="card-body p-0">
                <div class="ratio ratio-4x3">
                    <iframe src="{{ $pdfUrl }}" class="border-0 rounded-bottom" title="Aperçu PDF"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
