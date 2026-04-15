@extends('layouts.app')

@section('title', 'Vérification email')

@section('content')
<div class="auth-wrapper">
    <div class="card auth-card">
        <div class="auth-header">
            <i class="fas fa-envelope-circle-check fa-2x mb-2"></i>
            <h1>Vérifiez votre email</h1>
            <p>Un lien de vérification vous a été envoyé</p>
        </div>
        <div class="card-body text-center">
            @if (session('resent'))
                <div class="alert alert-success small">Un nouveau lien de vérification a été envoyé.</div>
            @endif
            <p class="text-muted">
                Avant de continuer, veuillez consulter votre boîte mail et cliquer sur le lien de vérification.
            </p>
            <p class="small text-muted">Vous n'avez rien reçu ?</p>
            <form method="POST" action="{{ route('verification.resend') }}">
                @csrf
                <button type="submit" class="btn btn-outline-primary">
                    <i class="fas fa-paper-plane me-2"></i>Renvoyer le lien
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
