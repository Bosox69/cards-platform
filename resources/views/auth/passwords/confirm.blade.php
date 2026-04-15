@extends('layouts.app')

@section('title', 'Confirmer le mot de passe')

@section('content')
<div class="auth-wrapper">
    <div class="card auth-card">
        <div class="auth-header">
            <i class="fas fa-shield-halved fa-2x mb-2"></i>
            <h1>Confirmation</h1>
            <p>Confirmez votre mot de passe pour continuer</p>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('password.confirm') }}">
                @csrf
                <div class="mb-4">
                    <label for="password" class="form-label fw-semibold">Mot de passe</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fas fa-lock text-secondary"></i></span>
                        <input id="password" type="password"
                               class="form-control @error('password') is-invalid @enderror"
                               name="password" required autocomplete="current-password" placeholder="••••••••">
                        @error('password') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2">Confirmer</button>
                @if (Route::has('password.request'))
                    <p class="text-center mt-4 mb-0 small">
                        <a class="text-decoration-none" href="{{ route('password.request') }}">Mot de passe oublié ?</a>
                    </p>
                @endif
            </form>
        </div>
    </div>
</div>
@endsection
