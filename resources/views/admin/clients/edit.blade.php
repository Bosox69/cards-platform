@extends('layouts.admin')

@section('title', 'Modifier ' . $client->name)

@section('content')
<div class="page-header mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h1>Modifier {{ $client->name }}</h1>
        <p class="lead">Mettez à jour les informations du client.</p>
    </div>
    <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Retour à la liste
    </a>
</div>

<form action="{{ route('admin.clients.update', $client) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    @include('admin.clients._form', ['submitLabel' => 'Enregistrer les modifications'])
</form>
@endsection
