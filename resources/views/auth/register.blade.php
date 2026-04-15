@extends('layouts.app')

@section('title', __('Register'))

@section('content')
<div class="auth-wrapper">
    <div class="card auth-card">
        <div class="auth-header">
            <i class="fas fa-user-plus fa-2x mb-2"></i>
            <h1>{{ __('Créer un compte') }}</h1>
            <p>Rejoignez notre plateforme de commande</p>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">{{ __('Nom complet') }}</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fas fa-user text-secondary"></i></span>
                        <input id="name" type="text"
                               class="form-control @error('name') is-invalid @enderror"
                               name="name" value="{{ old('name') }}" required autocomplete="name" autofocus
                               placeholder="Votre nom">
                        @error('name')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">{{ __('Adresse email') }}</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fas fa-envelope text-secondary"></i></span>
                        <input id="email" type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               name="email" value="{{ old('email') }}" required autocomplete="email"
                               placeholder="nom@exemple.com">
                        @error('email')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">{{ __('Mot de passe') }}</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fas fa-lock text-secondary"></i></span>
                        <input id="password" type="password"
                               class="form-control @error('password') is-invalid @enderror"
                               name="password" required autocomplete="new-password" placeholder="••••••••">
                        @error('password')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password-confirm" class="form-label fw-semibold">{{ __('Confirmer le mot de passe') }}</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fas fa-lock text-secondary"></i></span>
                        <input id="password-confirm" type="password" class="form-control"
                               name="password_confirmation" required autocomplete="new-password" placeholder="••••••••">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2">
                    <i class="fas fa-user-plus me-2"></i>{{ __('Créer mon compte') }}
                </button>

                <p class="text-center text-muted mt-4 mb-0 small">
                    Déjà un compte ?
                    <a href="{{ route('login') }}" class="text-decoration-none fw-semibold">Se connecter</a>
                </p>
            </form>
        </div>
    </div>
</div>
@endsection
