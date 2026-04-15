@extends('layouts.app')

@section('title', 'Accueil')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body text-center py-5">
                    @if (session('status'))
                        <div class="alert alert-success">{{ session('status') }}</div>
                    @endif
                    <div class="mb-3">
                        <i class="fas fa-check-circle text-success" style="font-size:3rem;"></i>
                    </div>
                    <h3 class="fw-bold mb-2">Bienvenue, {{ Auth::user()->name }}</h3>
                    <p class="text-muted mb-4">Vous êtes connecté. Accédez à votre espace pour gérer vos commandes.</p>
                    <a href="{{ Auth::user()->isAdmin() ? route('admin.dashboard') : route('client.dashboard') }}"
                       class="btn btn-primary btn-lg">
                        <i class="fas fa-tachometer-alt me-2"></i>Accéder à mon tableau de bord
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
