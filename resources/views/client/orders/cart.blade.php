@extends('layouts.client')

@section('title', 'Mon panier')

@section('content')
<div class="page-header mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h1>Mon panier</h1>
        <p class="lead">Validez vos articles avant de passer commande.</p>
    </div>
    <a href="{{ route('client.orders.create') }}" class="btn btn-outline-primary">
        <i class="fas fa-plus me-2"></i>Ajouter des cartes
    </a>
</div>

@if(empty($cart))
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
            <h5 class="mb-2">Votre panier est vide</h5>
            <p class="text-muted mb-4">Ajoutez des cartes pour commencer une commande.</p>
            <a href="{{ route('client.orders.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nouvelle commande
            </a>
        </div>
    </div>
@else
    <div class="card mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Modèle</th>
                            <th>Département</th>
                            <th>Nom</th>
                            <th class="text-center">Quantité</th>
                            <th class="text-center">Impression</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cart as $itemId => $item)
                            <tr>
                                <td class="fw-semibold">{{ $item['template_name'] }}</td>
                                <td>{{ $item['department_name'] }}</td>
                                <td>
                                    <div>{{ $item['card_data']['fullName'] }}</div>
                                    <div class="small text-muted">{{ $item['card_data']['jobTitle'] }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary-subtle text-primary">{{ $item['quantity'] }} ex.</span>
                                </td>
                                <td class="text-center">
                                    @if($item['is_double_sided'])
                                        <span class="badge bg-success-subtle text-success">Recto-verso</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">Recto seul</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('client.orders.preview', ['itemId' => $itemId]) }}"
                                       class="btn btn-sm btn-light border" title="Prévisualiser">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('client.orders.edit', ['itemId' => $itemId]) }}"
                                       class="btn btn-sm btn-light border" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('client.orders.remove-from-cart') }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Supprimer cet article du panier ?');">
                                        @csrf
                                        <input type="hidden" name="itemId" value="{{ $itemId }}">
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
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header py-3">
                    <h5 class="mb-0"><i class="fas fa-comment-dots me-2 text-primary"></i>Finaliser la commande</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('client.orders.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="comment" class="form-label fw-semibold">Commentaires (optionnel)</label>
                            <textarea id="comment" name="comment" rows="3" class="form-control"
                                      placeholder="Informations complémentaires, demandes particulières..."></textarea>
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('client.orders.create') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Ajouter d'autres cartes
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check me-2"></i>Valider la commande
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header py-3">
                    <h5 class="mb-0"><i class="fas fa-receipt me-2 text-primary"></i>Récapitulatif</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>Articles</span>
                            <span class="fw-semibold">{{ count($cart) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>Total cartes</span>
                            <span class="fw-semibold">
                                {{ array_sum(array_map(fn($i) => $i['quantity'], $cart)) }} ex.
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
