<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Administration | MeubleCuisine')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}"> {{-- Assurez-vous que ce fichier existe --}}
    <style> /* Styles Admin (identiques aux versions prÃ©cÃ©dentes) */
        body { background-color: #f8f9fa; font-family: sans-serif; }
        :root { --primary-color: #007bff; /* ... */ }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 15px; }
        .admin-header { background-color: #343a40; color: white; padding: 5px 0; }
        .admin-header nav { display: flex; justify-content: space-between; align-items: center; }
        .admin-header .logo { color: white; text-decoration: none; font-weight: bold; font-size: 1.2rem; }
        .admin-header .nav-links { display: flex; align-items: center; gap: 5px; list-style: none; padding: 0; margin: 0;}
        .admin-header .nav-links a { color: #eee; padding: 8px 12px; text-decoration: none; border-radius: 0.25rem; /*...*/ }
        .admin-header .nav-links a:hover, .admin-header .nav-links a.active { color: white; background-color: rgba(255,255,255,0.1); }
        .admin-subnav { background-color: white; padding: 0; border-bottom: 1px solid #dee2e6; margin-bottom: 30px; /*...*/ }
        .admin-subnav .container { display: flex; gap: 20px; align-items: center; overflow-x: auto; }
        .admin-subnav a { text-decoration: none; color: #212529; font-weight: 500; padding: 15px 10px; /*...*/ }
        .admin-subnav a:hover, .admin-subnav a.active { color: var(--primary-color); border-bottom-color: var(--primary-color); }
        .admin-content-wrapper { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        .admin-section { background-color: white; padding: 25px; border-radius: 0.25rem; /*...*/ }
        .admin-section h1, .admin-section h2 { color: var(--primary-color); margin-top: 0; margin-bottom: 20px; /*...*/ }
        /* Styles Alert, Button, Form Control... */
        .alert { /*...*/ } .btn { /*...*/ } .form-control { /*...*/ }
    </style>
    @yield('styles')
</head>
<body>
    <!-- Header Admin -->
    <header class="admin-header">
        <div class="container">
            <nav>
                <a href="{{ Auth::guard('admin')->check() ? route('admin.dashboard') : route('admin.login') }}" class="logo">
                    MeubleCuisine - Admin
                </a>
                <div class="nav-links">
                    {{-- Afficher les liens que si l'admin est connectÃ© --}}
                    @auth('admin')
                        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
                        {{-- Ajoutez d'autres liens de haut niveau ici si nÃ©cessaire --}}

                        {{-- Lien et Formulaire de DÃ©connexion Admin --}}
                        <a href="{{ route('admin.logout') }}"
                           onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();">
                            DÃ©connexion ({{ Auth::guard('admin')->user()->prenom }}) {{-- Afficher prÃ©nom ou nom --}}
                        </a>
                        <form id="admin-logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    @else
                        {{-- Optionnel: Lien vers la connexion si pas connectÃ© --}}
                        {{-- <a href="{{ route('admin.login') }}">Connexion Admin</a> --}}
                    @endauth

                     {{-- Lien vers le site public (toujours visible) --}}
                     <a href="{{ route('home') }}" target="_blank" title="Voir le site public">Site Public →</a>
                </div>
            </nav>
        </div>
    </header>

     {{-- Afficher la sous-navigation seulement si un admin est connectÃ© --}}
     @auth('admin')
        <div class="admin-subnav">
            <div class="container">
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Vue d'ensemble</a>
                <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">Utilisateurs</a>
                <a href="{{ route('admin.listings.index') }}" class="{{ request()->routeIs('admin.listings.*') ? 'active' : '' }}">Annonces</a>
                <a href="{{ route('admin.reservations.index') }}" class="{{ request()->routeIs('admin.reservations.*') ? 'active' : '' }}">RÃ©servations</a>
                <a href="{{ route('admin.litiges.index') }}" class="{{ request()->routeIs('admin.litiges.*') ? 'active' : '' }}">Litiges</a>
                <a href="{{ route('admin.stats') }}" class="{{ request()->routeIs('admin.stats') ? 'active' : '' }}">Statistiques</a>
                <a href="{{ route('admin.settings') }}" class="{{ request()->routeIs('admin.settings*') ? 'active' : '' }}">ParamÃ¨tres</a>
            </div>
        </div>
     @endauth

    <main class="container" style="padding-top: 1rem; padding-bottom: 2rem;">
        @yield('content')
    </main>

    <footer style="text-align: center; padding: 1.5rem 0; margin-top: 2rem; border-top: 1px solid #dee2e6; background-color: #e9ecef;">
        <div class="container"> Â© <span id="current-year"></span> MeubleCuisine Admin Panel. </div>
    </footer>

    <script> document.getElementById('current-year').textContent = new Date().getFullYear(); </script>
    @yield('scripts')
</body>
</html>