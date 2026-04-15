<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Cartes de visite') }}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @viteReactRefresh
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <style>
        .hero-gradient { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #0ea5e9 100%); color: #fff; }
        .hero-gradient .display-4 { font-weight: 800; letter-spacing: -.5px; }
        .feature-icon { width:56px;height:56px;border-radius:14px;display:inline-flex;align-items:center;justify-content:center;font-size:1.5rem; }
    </style>
</head>
<body>
<div id="app" class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-md app-navbar">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                <i class="fas fa-id-card me-2"></i>
                <span>{{ config('app.name', 'Cartes de visite') }}</span>
            </a>
            <div class="ms-auto d-flex gap-2">
                @auth
                    <a href="{{ url('/home') }}" class="btn btn-primary">Mon espace</a>
                @else
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="btn btn-outline-primary">Connexion</a>
                    @endif
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-primary">Créer un compte</a>
                    @endif
                @endauth
            </div>
        </div>
    </nav>

    <section class="hero-gradient py-5">
        <div class="container py-5">
            <div class="row align-items-center g-5">
                <div class="col-lg-7">
                    <span class="badge bg-white bg-opacity-25 text-white mb-3 px-3 py-2 rounded-pill">
                        <i class="fas fa-bolt me-1"></i> Plateforme de commande professionnelle
                    </span>
                    <h1 class="display-4 mb-3">Vos cartes de visite, personnalisées en quelques clics.</h1>
                    <p class="lead mb-4 opacity-90">
                        Commandez, personnalisez et suivez vos cartes de visite d'entreprise depuis un espace unique,
                        pensé pour les équipes commerciales, RH et direction.
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        @guest
                            <a href="{{ route('register') }}" class="btn btn-light btn-lg fw-semibold">
                                <i class="fas fa-rocket me-2"></i>Commencer
                            </a>
                            <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg">Se connecter</a>
                        @else
                            <a href="{{ url('/home') }}" class="btn btn-light btn-lg fw-semibold">
                                Accéder à mon espace <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        @endguest
                    </div>
                </div>
                <div class="col-lg-5 text-center">
                    <div class="position-relative d-inline-block">
                        <div class="bg-white text-dark rounded-3 shadow-lg p-4 text-start" style="width:320px;transform:rotate(-4deg);">
                            <div class="fw-bold">Jean Dupont</div>
                            <div class="text-muted small mb-3">Directeur commercial</div>
                            <div class="small"><i class="fas fa-envelope me-2 text-primary"></i>j.dupont@entreprise.fr</div>
                            <div class="small"><i class="fas fa-phone me-2 text-primary"></i>+33 1 23 45 67 89</div>
                        </div>
                        <div class="bg-white text-dark rounded-3 shadow-lg p-4 text-start position-absolute"
                             style="width:320px;transform:rotate(6deg);top:40px;left:40px;">
                            <div class="fw-bold">Sophie Martin</div>
                            <div class="text-muted small mb-3">Responsable RH</div>
                            <div class="small"><i class="fas fa-envelope me-2 text-primary"></i>s.martin@entreprise.fr</div>
                            <div class="small"><i class="fas fa-phone me-2 text-primary"></i>+33 1 23 45 67 90</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container py-4">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Une plateforme complète</h2>
                <p class="text-muted">Tout ce qu'il faut pour gérer vos cartes de visite, en toute simplicité.</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 card-hover border">
                        <div class="card-body p-4">
                            <div class="feature-icon bg-primary bg-opacity-10 text-primary mb-3"><i class="fas fa-paint-brush"></i></div>
                            <h5 class="fw-bold">Personnalisation avancée</h5>
                            <p class="text-muted mb-0">Choisissez un modèle, adaptez le texte et visualisez votre carte en temps réel.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 card-hover border">
                        <div class="card-body p-4">
                            <div class="feature-icon bg-success bg-opacity-10 text-success mb-3"><i class="fas fa-truck-fast"></i></div>
                            <h5 class="fw-bold">Suivi en temps réel</h5>
                            <p class="text-muted mb-0">Suivez chaque commande de la validation à la livraison avec des notifications automatiques.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 card-hover border">
                        <div class="card-body p-4">
                            <div class="feature-icon bg-warning bg-opacity-10 text-warning mb-3"><i class="fas fa-users"></i></div>
                            <h5 class="fw-bold">Gestion multi-départements</h5>
                            <p class="text-muted mb-0">Des modèles dédiés pour chaque département : commercial, direction, RH...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="app-footer">
        <div class="container text-center">
            &copy; {{ date('Y') }} {{ config('app.name', 'Cartes de visite') }}. Tous droits réservés.
        </div>
    </footer>
</div>
</body>
</html>
