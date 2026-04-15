@extends('layouts.admin')

@section('title', 'Nouveau client')

@section('content')
<div class="page-header mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h1>Nouveau client</h1>
        <p class="lead">Ajoutez un nouveau client à la plateforme.</p>
    </div>
    <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Retour à la liste
    </a>
</div>

<form action="{{ route('admin.clients.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @include('admin.clients._form', ['submitLabel' => 'Créer le client'])
</form>
@endsection
