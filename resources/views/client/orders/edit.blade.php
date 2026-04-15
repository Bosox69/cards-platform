@extends('layouts.client')

@section('title', 'Modifier une carte')

@section('content')
<div class="page-header mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h1>Modifier une carte</h1>
        <p class="lead">Mettez à jour les informations avant validation.</p>
    </div>
    <a href="{{ route('client.orders.cart') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Retour au panier
    </a>
</div>

<div class="card">
    <div class="card-header py-3">
        <h5 class="mb-0"><i class="fas fa-paint-brush me-2 text-primary"></i>Personnalisation</h5>
    </div>
    <div class="card-body">
        <div id="card-customizer"
             data-department-id="{{ $department->id }}"
             data-template-id="{{ $template->id }}"
             data-item-id="{{ $itemId }}"
             data-edit-mode="true"
             data-card-data="{{ json_encode($item['card_data']) }}"
             data-is-double-sided="{{ $item['is_double_sided'] ? 'true' : 'false' }}"
             data-quantity="{{ $item['quantity'] }}">
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
                <p class="text-muted mt-3 mb-0">Chargement du personnalisateur...</p>
            </div>
        </div>
    </div>
</div>
@endsection
