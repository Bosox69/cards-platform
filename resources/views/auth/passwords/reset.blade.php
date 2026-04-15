@extends('layouts.app')

@section('title', 'Réinitialiser le mot de passe')

@section('content')
<div class="auth-wrapper">
    <div class="card auth-card">
        <div class="auth-header">
            <i class="fas fa-lock fa-2x mb-2"></i>
            <h1>Nouveau mot de passe</h1>
            <p>Choisissez un nouveau mot de passe</p>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fas fa-envelope text-secondary"></i></span>
                        <input id="email" type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
                        @error('email') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">Nouveau mot de passe</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fas fa-lock text-secondary"></i></span>
                        <input id="password" type="password"
                               class="form-control @error('password') is-invalid @enderror"
                               name="password" required autocomplete="new-password" placeholder="••••••••">
                        @error('password') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password-confirm" class="form-label fw-semibold">Confirmer</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fas fa-lock text-secondary"></i></span>
                        <input id="password-confirm" type="password" class="form-control"
                               name="password_confirmation" required autocomplete="new-password" placeholder="••••••••">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2">
                    <i class="fas fa-check me-2"></i>Réinitialiser le mot de passe
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
