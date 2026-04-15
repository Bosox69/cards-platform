@extends('layouts.app')

@section('title', __('Mot de passe oublié'))

@section('content')
<div class="auth-wrapper">
    <div class="card auth-card">
        <div class="auth-header">
            <i class="fas fa-key fa-2x mb-2"></i>
            <h1>Mot de passe oublié</h1>
            <p>Recevez un lien de réinitialisation par email</p>
        </div>
        <div class="card-body">
            @if (session('status'))
                <div class="alert alert-success small">{{ session('status') }}</div>
            @endif
            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Adresse email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fas fa-envelope text-secondary"></i></span>
                        <input id="email" type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                               placeholder="nom@exemple.com">
                        @error('email') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2">
                    <i class="fas fa-paper-plane me-2"></i>Envoyer le lien
                </button>
                <p class="text-center text-muted mt-4 mb-0 small">
                    <a href="{{ route('login') }}" class="text-decoration-none fw-semibold">← Retour à la connexion</a>
                </p>
            </form>
        </div>
    </div>
</div>
@endsection
