<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') | Administration</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    @viteReactRefresh
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    @stack('styles')
</head>
<body>
<div id="app" class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg app-navbar sticky-top">
        <div class="container-fluid px-lg-4">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('admin.dashboard') }}">
                <i class="fas fa-shield-halved me-2"></i>
                <span>Administration</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav"
                    aria-controls="adminNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="adminNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-tachometer-alt me-1"></i> Tableau de bord
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">
                            <i class="fas fa-box me-1"></i> Commandes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.templates.*') ? 'active' : '' }}" href="{{ route('admin.templates.index') }}">
                            <i class="fas fa-layer-group me-1"></i> Templates
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.clients.*') ? 'active' : '' }}" href="{{ route('admin.clients.index') }}">
                            <i class="fas fa-building me-1"></i> Clients
                        </a>
                    </li>
                </ul>

                <ul class="navbar-nav align-items-lg-center">
                    <li class="nav-item me-lg-2">
                        <a class="nav-link" href="{{ route('client.dashboard') }}" title="Vue client">
                            <i class="fas fa-user me-1"></i> Espace client
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-shield fs-5 me-2"></i>
                            <span>{{ Auth::user()->name }}</span>
                            <span class="badge bg-primary-subtle text-primary ms-2">Admin</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt me-2 text-secondary"></i> Déconnexion
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="flex-grow-1 py-4">
        <div class="container-fluid px-lg-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <footer class="app-footer">
        <div class="container-fluid px-lg-4 text-center">
            &copy; {{ date('Y') }} Administration — Plateforme de commande de cartes de visite.
        </div>
    </footer>
</div>

@stack('scripts')
</body>
</html>
