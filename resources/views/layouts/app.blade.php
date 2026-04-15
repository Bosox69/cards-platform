<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Cartes de visite'))</title>

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    @viteReactRefresh
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
<div id="app" class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-md app-navbar sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                <i class="fas fa-id-card me-2"></i>
                <span>{{ config('app.name', 'Cartes de visite') }}</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                    aria-controls="mainNav" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto"></ul>

                <ul class="navbar-nav ms-auto align-items-md-center">
                    @guest
                        @if (Route::has('login'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">
                                    <i class="fas fa-sign-in-alt me-1"></i>{{ __('Login') }}
                                </a>
                            </li>
                        @endif
                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="btn btn-primary ms-md-2" href="{{ route('register') }}">
                                    {{ __('Register') }}
                                </a>
                            </li>
                        @endif
                    @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle d-flex align-items-center"
                               href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" v-pre>
                                <i class="fas fa-user-circle fs-5 me-2"></i>{{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="navbarDropdown">
                                <li>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2 text-secondary"></i>{{ __('Logout') }}
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <main class="flex-grow-1 py-4">
        @yield('content')
    </main>

    <footer class="app-footer">
        <div class="container text-center">
            &copy; {{ date('Y') }} {{ config('app.name', 'Cartes de visite') }}. Tous droits réservés.
        </div>
    </footer>
</div>
</body>
</html>
