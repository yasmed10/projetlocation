@extends('layouts.admin')

@section('title', 'Gestion Utilisateurs | Admin MeubleCuisine')

@section('styles')
<style>
    /* Styles spécifiques pour la gestion des utilisateurs */
    .filter-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        align-items: center;
        margin-bottom: 25px;
        padding-bottom: 20px;
        border-bottom: 1px solid #eee;
    }
    .filter-bar .form-group {
        margin-bottom: 0;
    }
    .filter-bar label {
        margin-right: 5px;
        font-size: 0.9em;
        color: #555;
    }
    .filter-bar select.form-control,
    .filter-bar input[type="search"].form-control {
        padding: 8px 12px;
        min-width: 150px;
        height: 38px;
        display: inline-block;
        width: auto;
    }
    .filter-bar .search-group {
        display: flex;
        /* margin-left: auto; Removed if it's the only element */
    }
    .filter-bar .search-group input {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
        min-width: 250px; /* Give search more space */
    }
    .filter-bar .search-group button {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        padding: 8px 15px;
        height: 38px;
    }
    .filter-bar .reset-filters {
        margin-left: 10px;
    }

    .pagination-info {
        text-align: right;
        font-size: 0.9rem;
        color: #777;
        margin-bottom: 15px;
    }

    /* Tableau utilisateurs */
    .admin-users-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.9rem;
        margin-bottom: 20px;
    }
    .admin-users-table th,
    .admin-users-table td {
        padding: 12px 8px;
        text-align: left;
        border-bottom: 1px solid #eee;
        vertical-align: middle;
    }
    .admin-users-table thead th {
        color: #555;
        font-weight: 600;
        background-color: #f8f9fa;
        white-space: nowrap;
    }
    .admin-users-table tbody tr:hover {
        background-color: #f1f1f1;
    }
    .admin-users-table td.user-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .admin-users-table td.user-info img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        background-color: #eee; /* Fallback bg */
        border: 1px solid #ddd;
    }
    .admin-users-table td.user-info .name {
        font-weight: 500;
        color: var(--text-color);
        display: block;
    }
    .admin-users-table td.user-info .email {
        font-size: 0.85rem;
        color: #777;
        display: block;
    }

    /* Statuts (commentés car non utilisés actuellement) */
    /* .status-badge { ... } */
    /* .status-cin.verified { ... } */
    /* ... etc ... */

    /* Boutons d'action dans le tableau */
    .actions-cell {
        white-space: nowrap;
        text-align: right;
    }
    .actions-cell form,
    .actions-cell a.btn { /* Appliquer aussi aux liens-boutons */
        display: inline-block;
        margin-left: 3px; /* Reduced margin */
    }
    .actions-cell .btn {
        padding: 4px 8px;
        font-size: 0.8rem;
    }
    .actions-cell .btn-success { background-color: #28a745; border-color: #28a745; color: white; } /* Standard Bootstrap Success */
    .actions-cell .btn-warning { background-color: #ffc107; border-color: #ffc107; color: #212529; } /* Standard Bootstrap Warning */
    .actions-cell .btn-danger { background-color: #dc3545; border-color: #dc3545; color: white; } /* Standard Bootstrap Danger */
    .actions-cell .btn-secondary { background-color: #6c757d; border-color: #6c757d; color: white; } /* Standard Bootstrap Secondary */
    .actions-cell .btn-info { background-color: #17a2b8; border-color: #17a2b8; color: white; } /* Standard Bootstrap Info */


    /* Pagination Style */
    .pagination {
        justify-content: center;
        margin-top: 20px;
    }

    /* Responsive Table */
    @media (max-width: 992px) {
        .admin-users-table,
        .admin-users-table thead,
        .admin-users-table tbody,
        .admin-users-table th,
        .admin-users-table td,
        .admin-users-table tr {
            display: block;
        }

        .admin-users-table thead tr {
            position: absolute;
            top: -9999px;
            left: -9999px;
        }

        .admin-users-table tr {
            border: 1px solid #ccc;
            margin-bottom: 10px;
        }

        .admin-users-table td {
            border: none;
            border-bottom: 1px solid #eee;
            position: relative;
            padding-left: 50%;
            padding-top: 10px;
            padding-bottom: 10px;
            white-space: normal;
            text-align: left;
            min-height: 38px; /* Ensure height for labels */
        }

        .admin-users-table td:before {
            position: absolute;
            top: 10px;
            left: 6px;
            width: 45%;
            padding-right: 10px;
            white-space: nowrap;
            text-align: left;
            font-weight: bold;
            color: #555;
        }

        /* Label the data */
        .admin-users-table td:nth-of-type(1):before { content: "Utilisateur"; }
        .admin-users-table td:nth-of-type(2):before { content: "Inscrit le"; }
        .admin-users-table td:nth-of-type(3):before { content: "Actions"; }

        /* Exceptions pour les cellules user-info et actions */
        .admin-users-table td.user-info {
            padding-left: 8px;
            display: flex;
            min-height: 60px; /* More space for avatar */
        }

        .admin-users-table td.actions-cell {
            padding-left: 8px;
            text-align: left;
        }

        .actions-cell form,
        .actions-cell a.btn {
            margin-left: 0;
            margin-right: 5px;
            margin-bottom: 5px;
        }
    }
</style>
@endsection

@section('content')
<div class="admin-content-wrapper">
    <section class="admin-section">
        <h1>Gestion des Utilisateurs</h1>

        {{-- Afficher les messages flash (succès, info, error) --}}
        @if (session('success'))
            <div class="alert alert-success" role="alert">{{ session('success') }}</div>
        @endif
        @if (session('info'))
            <div class="alert alert-info" role="alert">{{ session('info') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
        @endif


        <!-- Barre de filtres (simplifiée) -->
        <form method="GET" action="{{ route('admin.users.index') }}" class="filter-bar">
            {{-- Filtres status et cin_status retirés --}}

            <!-- Recherche -->
            <div class="search-group ml-auto"> {{-- Pousser à droite si seul élément --}}
                <label for="search" class="sr-only">Rechercher:</label>
                <input type="search" id="search" name="search" class="form-control" placeholder="Rechercher par nom/prénom/email..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary" title="Rechercher">
                    <span aria-hidden="true">🔍</span>
                    <span class="sr-only">Rechercher</span>
                </button>
            </div>
            <!-- Lien pour réinitialiser les filtres -->
            @if(request()->has('search'))
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm reset-filters">Réinitialiser</a>
            @endif
        </form>

        <!-- Info Pagination -->
        @if ($users->total() > 0)
            <div class="pagination-info">
                Affichage de {{ $users->firstItem() ?? 0 }} à {{ $users->lastItem() ?? 0 }} sur {{ $users->total() }} utilisateurs
            </div>
        @else
            <div class="pagination-info">
                Aucun utilisateur trouvé
            </div>
        @endif

        <!-- Tableau des Utilisateurs (simplifié) -->
        <div class="table-responsive">
            <table class="admin-users-table">
                <thead>
                    <tr>
                        <th>Utilisateur</th>
                        <th>Inscrit le</th>
                        {{-- Colonnes status et cin_status retirées --}}
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td class="user-info">
                            {{-- Utiliser l'accessor avatar_url (vérifier qu'il existe dans User model) --}}
                            <img src="{{ $user->avatar_url }}" alt="Avatar de {{ $user->full_name }}">
                            <div>
                                {{-- Utiliser l'accessor full_name (vérifier qu'il existe dans User model) --}}
                                <span class="name">{{ $user->full_name ?? 'Sans nom' }}</span>
                                <span class="email">{{ $user->email ?? 'Email non disponible' }}</span>
                            </div>
                        </td>
                        <td>{{ $user->created_at ? $user->created_at->format('d/m/Y') : 'N/A' }}</td>
                        {{-- Cellules status et cin_status retirées --}}
                        <td class="actions-cell">
                            {{-- Affichage des boutons placeholder - La logique réelle dépendra des champs ajoutés --}}
                            {{-- TODO: Adapter les conditions d'affichage des boutons selon la logique implémentée --}}

                            {{-- Bouton Approuver (Placeholder) --}}
                            <form action="{{ route('admin.users.approve', $user->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-success btn-sm" title="Approuver (Logique à définir)"
                                        onclick="return confirm('Action Approuver pour {{ $user->nom }} (logique à définir) ?')">App.</button>
                            </form>

                             {{-- Bouton Rejeter (Placeholder) --}}
                            <form action="{{ route('admin.users.reject', $user->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-danger btn-sm" title="Rejeter (Logique à définir)"
                                        onclick="return confirm('Action Rejeter pour {{ $user->nom }} (logique à définir) ?')">Rej.</button>
                            </form>

                             {{-- Bouton Bloquer (Placeholder) --}}
                            {{-- TODO: Ajouter condition: afficher si user non bloqué ? --}}
                            <form action="{{ route('admin.users.block', $user->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-secondary btn-sm" title="Bloquer (Logique à définir)"
                                        onclick="return confirm('Action Bloquer pour {{ $user->nom }} (logique à définir) ?')">Bloq.</button>
                            </form>

                             {{-- Bouton Débloquer (Placeholder) --}}
                             {{-- TODO: Ajouter condition: afficher si user est bloqué ? --}}
                             <form action="{{ route('admin.users.unblock', $user->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-info btn-sm" title="Débloquer (Logique à définir)"
                                        onclick="return confirm('Action Débloquer pour {{ $user->nom }} (logique à définir) ?')">Débl.</button>
                            </form>

                            {{-- Lien vers la vérification CIN (condition simple basée sur existence fichiers) --}}
                             {{-- TODO: Ajouter condition: afficher si CIN non vérifiée ? --}}
                            @if($user->cin_recto && $user->cin_verso)
                                <a href="{{ route('admin.users.verify_cin', $user->id) }}" class="btn btn-warning btn-sm" title="Vérifier CIN">CIN</a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        {{-- Correction du colspan --}}
                        <td colspan="3" style="text-align: center; padding: 20px; color: #777;">Aucun utilisateur trouvé.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $users->withQueryString()->links() }}
        </div>
    </section>
</div>
@endsection

@section('scripts')
{{-- Aucun script spécifique requis pour les confirmations de base --}}
@endsection