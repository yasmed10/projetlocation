<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Administration | MeubleCuisine')</title>

    <!-- Styles globaux -->
    {{-- Assurez-vous que ce fichier CSS existe et contient vos styles --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <!-- Styles Admin -->
    <style>
        /* Styles SPÉCIFIQUES pour l'ADMINISTRATION */
        body { background-color: #f8f9fa; font-family: sans-serif; /* Basic font */ }
        :root { /* Define some CSS variables */
            --primary-color: #007bff;
            --secondary-color: #6c757d;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
            --info: #17a2b8;
            --light-bg: #f8f9fa;
            --dark-bg: #343a40;
            --text-color: #212529;
            --border-radius: 0.25rem;
            --box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 15px; }

        .admin-header { background-color: var(--dark-bg); color: white; padding: 5px 0; }
        .admin-header nav { display: flex; justify-content: space-between; align-items: center; }
        .admin-header .logo { color: white; text-decoration: none; font-weight: bold; font-size: 1.2rem; }
        .admin-header .nav-links { display: flex; align-items: center; gap: 5px; list-style: none; padding: 0; margin: 0;}
        .admin-header .nav-links a { color: #eee; padding: 8px 12px; border-bottom: none; text-decoration: none; border-radius: var(--border-radius); transition: background-color 0.2s, color 0.2s;}
        .admin-header .nav-links a:hover,
        .admin-header .nav-links a.active { color: white; background-color: rgba(255,255,255,0.1); }
        .admin-header .notification-link { position: relative; }
        .notification-badge { position: absolute; top: 3px; right: 3px; background-color: var(--danger); color: white; border-radius: 50%; width: 16px; height: 16px; font-size: 0.65rem; display: flex; align-items: center; justify-content: center; font-weight: bold; }

        /* Sous-nav Admin */
        .admin-subnav { background-color: white; padding: 0; border-bottom: 1px solid #dee2e6; margin-bottom: 30px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .admin-subnav .container { display: flex; gap: 20px; align-items: center; overflow-x: auto; /* Handle overflow on small screens */ }
        .admin-subnav a { text-decoration: none; color: var(--text-color); font-weight: 500; padding: 15px 10px; transition: color 0.3s, border-color 0.3s; position: relative; border-bottom: 3px solid transparent; white-space: nowrap; }
        .admin-subnav a:hover, .admin-subnav a.active { color: var(--primary-color); border-bottom-color: var(--primary-color); }

        .admin-content-wrapper { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        .admin-section { background-color: white; padding: 25px; border-radius: var(--border-radius); box-shadow: var(--box-shadow); margin-bottom: 30px; }
        .admin-section h1, .admin-section h2 { color: var(--primary-color); margin-top: 0; margin-bottom: 20px; font-size: 1.5rem; border-bottom: 1px solid #eee; padding-bottom: 10px;}
        .admin-section h1 { font-size: 1.8rem; }

        /* Basic Alert Styles (add to style.css later) */
        .alert { padding: 1rem; margin-bottom: 1rem; border: 1px solid transparent; border-radius: var(--border-radius); }
        .alert-success { color: #155724; background-color: #d4edda; border-color: #c3e6cb; }
        .alert-info { color: #0c5460; background-color: #d1ecf1; border-color: #bee5eb; }
        .alert-danger { color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; }
        .alert-warning { color: #856404; background-color: #fff3cd; border-color: #ffeeba; }

        /* Basic Button Styles (add to style.css later) */
        .btn { display: inline-block; font-weight: 400; color: #212529; text-align: center; vertical-align: middle; user-select: none; background-color: transparent; border: 1px solid transparent; padding: 0.375rem 0.75rem; font-size: 1rem; line-height: 1.5; border-radius: var(--border-radius); transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out; cursor: pointer; }
        .btn-sm { padding: 0.25rem 0.5rem; font-size: 0.875rem; line-height: 1.5; border-radius: 0.2rem; }
        .btn-primary { color: #fff; background-color: var(--primary-color); border-color: var(--primary-color); }
        .btn-secondary { color: #fff; background-color: var(--secondary-color); border-color: var(--secondary-color); }
        .btn-success { color: #fff; background-color: var(--success); border-color: var(--success); }
        .btn-danger { color: #fff; background-color: var(--danger); border-color: var(--danger); }
        .btn-warning { color: #212529; background-color: var(--warning); border-color: var(--warning); }
        .btn-info { color: #fff; background-color: var(--info); border-color: var(--info); }
        /* Add hover/focus states as needed */

        /* Basic Form Control Styles (add to style.css later) */
        .form-control { display: block; width: 100%; height: calc(1.5em + 0.75rem + 2px); padding: 0.375rem 0.75rem; font-size: 1rem; font-weight: 400; line-height: 1.5; color: #495057; background-color: #fff; background-clip: padding-box; border: 1px solid #ced4da; border-radius: var(--border-radius); transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out; box-sizing: border-box; }
        .form-control:focus { color: #495057; background-color: #fff; border-color: #80bdff; outline: 0; box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25); }
        textarea.form-control { height: auto; }
        label { display: inline-block; margin-bottom: 0.5rem; }
        .sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0; }
        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
        .text-muted { color: #6c757d !important; }
        .d-block { display: block !important; }
        .d-inline { display: inline !important; }
        .d-inline-block { display: inline-block !important; }
        .d-flex { display: flex !important; }
        .justify-content-center { justify-content: center !important; }
        .ml-auto { margin-left: auto !important; }
        .mt-4 { margin-top: 1.5rem !important; }
        .p-4 { padding: 1.5rem !important; }
        .mb-3 { margin-bottom: 1rem !important; }

        /* Responsive Table Helper */
        .table-responsive { display: block; width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }

    </style>

    @yield('styles')
</head>
<body>
    <!-- Header Admin -->
    <header class="admin-header">
        <div class="container">
            <nav>
                <a href="{{ route('admin.dashboard') }}" class="logo">MeubleCuisine - Admin</a>
                <div class="nav-links">
                    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
                    <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">Utilisateurs</a>
                    <a href="{{ route('admin.stats') }}" class="{{ request()->routeIs('admin.stats') ? 'active' : '' }}">Statistiques</a>
                    <a href="#notifications" class="notification-link" title="Notifications">
                        {{-- TODO: Passer $notificationCount via View Composer ou contrôleurs --}}
                        <span class="notification-badge">{{ $notificationCount ?? 0 }}</span>
                    </a>
                    <a href="{{ route('home') }}" target="_blank" title="Voir le site public">Site Public ↗</a>

                    {{-- === SECTION LOGOUT COMMENTÉE === --}}
                    {{--
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Déconnexion</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                     --}}
                     {{-- === FIN SECTION LOGOUT COMMENTÉE === --}}

                </div>
            </nav>
        </div>
    </header>

    <!-- Sous-Navigation Admin -->
    <div class="admin-subnav">
        <div class="container">
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Vue d'ensemble</a>
            <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">Gestion Utilisateurs</a>
            <a href="{{ route('admin.stats') }}" class="{{ request()->routeIs('admin.stats') ? 'active' : '' }}">Rapports & Stats</a>
            {{-- Vérifier si la route existe avant de générer le lien --}}
            @if (Route::has('admin.listings.index'))
                <a href="{{ route('admin.listings.index') }}" class="{{ request()->routeIs('admin.listings.*') ? 'active' : '' }}">Gestion Annonces</a>
            @else
                <a href="#" class="disabled" title="Route admin.listings.index non définie">Gestion Annonces (N/A)</a>
            @endif
            <a href="{{ route('admin.settings') }}" class="{{ request()->routeIs('admin.settings') ? 'active' : '' }}">Paramètres</a>
        </div>
    </div>

    <main class="container" style="padding-top: 1rem; padding-bottom: 2rem;">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer style="text-align: center; padding: 1.5rem 0; margin-top: 2rem; border-top: 1px solid #dee2e6; background-color: #e9ecef;">
        <div class="container">
            <div class="footer-bottom" style="color: #6c757d;"> © <span id="current-year"></span> MeubleCuisine Admin Panel. </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const currentYearSpan = document.getElementById('current-year');
            if(currentYearSpan) { currentYearSpan.textContent = new Date().getFullYear(); }
        });
    </script>

    @yield('scripts')
</body>
</html>