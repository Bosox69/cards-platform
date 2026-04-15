@extends('layouts.app')

@section('title', __('Login'))

@section('content')
<div class="auth-wrapper">
    <div class="card auth-card">
        <div class="auth-header">
            <i class="fas fa-id-card fa-2x mb-2"></i>
            <h1>{{ __('Connexion') }}</h1>
            <p>Accédez à votre espace de commande</p>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">{{ __('Adresse email') }}</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fas fa-envelope text-secondary"></i></span>
                        <input id="email" type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
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
                               name="password" required autocomplete="current-password" placeholder="••••••••">
                        @error('password')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember"
                               {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">{{ __('Se souvenir de moi') }}</label>
                    </div>
                    @if (Route::has('password.request'))
                        <a class="small text-decoration-none" href="{{ route('password.request') }}">
                            {{ __('Mot de passe oublié ?') }}
                        </a>
                    @endif
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2">
                    <i class="fas fa-sign-in-alt me-2"></i>{{ __('Se connecter') }}
                </button>

                @if (Route::has('register'))
                    <p class="text-center text-muted mt-4 mb-0 small">
                        Pas encore de compte ?
                        <a href="{{ route('register') }}" class="text-decoration-none fw-semibold">Créer un compte</a>
                    </p>
                @endif
            </form>
        </div>
    </div>
</div>
@endsection
