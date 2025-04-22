@extends('layouts.admin')

@section('title', 'Gestion Annonces | Admin MeubleCuisine')

@section('styles')
<style>
    /* Styles spécifiques Gestion Annonces */
    .filter-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        align-items: center; /* Align items vertically */
        margin-bottom: 25px;
        padding-bottom: 20px;
        border-bottom: 1px solid #eee;
    }
    .filter-bar .form-group { margin-bottom: 0; }
    .filter-bar select.form-control, .filter-bar input[type="search"].form-control {
        padding: 8px 12px;
        min-width: 150px;
        height: 38px;
    }
    .filter-bar .search-group {
        display: flex;
        margin-left: auto; /* Pushes search to the right */
    }
    .filter-bar .search-group input {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
        min-width: 200px;
    }
    .filter-bar .search-group button {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        padding: 8px 15px;
        height: 38px;
    }
     .filter-bar .reset-filters {
        margin-left: 10px; /* Space before reset button */
    }
    .pagination-info {
        text-align: right;
        font-size: 0.9rem;
        color: #777;
        margin-bottom: 15px;
    }

    /* Tableau annonces */
    .admin-annonces-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.9rem;
    }
    .admin-annonces-table th, .admin-annonces-table td {
        padding: 10px 8px;
        text-align: left;
        border-bottom: 1px solid #eee;
        vertical-align: middle;
    }
    .admin-annonces-table thead th {
        color: #555;
        font-weight: 600;
        background-color: #f8f9fa; /* Light background for header */
        white-space: nowrap;
    }
    .admin-annonces-table tbody tr:hover {
        background-color: #f1f1f1; /* Hover effect */
    }
    .admin-annonces-table td.annonce-thumb img {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 4px; /* Slightly rounded corners */
        border: 1px solid #eee;
        background-color: #eee; /* Fallback bg */
    }
    .admin-annonces-table td.annonce-title {
        font-weight: 500;
    }
    .admin-annonces-table td.annonce-title a {
        color: #007bff; /* Bootstrap primary color */
        text-decoration: none;
    }
    .admin-annonces-table td.annonce-title a:hover {
        text-decoration: underline;
    }
    .admin-annonces-table td.annonce-seller a {
        color: #343a40; /* Darker text color */
        text-decoration: none;
        font-size: 0.85rem;
    }
    .admin-annonces-table td.annonce-seller a:hover {
        color: #007bff;
    }

    /* Statuts Annonce */
    .status-annonce {
        padding: 4px 10px;
        border-radius: 15px;
        font-size: 0.8rem;
        font-weight: 500;
        white-space: nowrap;
        display: inline-block;
        text-align: center;
        min-width: 80px;
        border: 1px solid transparent;
        text-transform: capitalize;
    }
    .status-annonce.status-annonce-active { background-color: #e7f7ef; color: #0d6b3f; border-color: #a7e0c2; }
    .status-annonce.status-annonce-pending { background-color: #fef3e2; color: #a0670d; border-color: #f7dcb0; }
    .status-annonce.status-annonce-rejected { background-color: #fdecea; color: #a61d24; border-color: #f8b4b8; }
    .status-annonce.status-annonce-draft { background-color: #e9ecef; color: #495057; border-color: #ced4da; }
    .status-annonce.status-annonce-archived { background-color: #f8f9fa; color: #6c757d; border-color: #dee2e6; }
    .status-annonce.status-annonce-unknown { background-color: #f8d7da; color: #721c24; border-color: #f5c6cb; } /* Style for unknown */


    /* Actions Annonce */
    .actions-cell {
        white-space: nowrap;
        text-align: right;
    }
     .actions-cell form,
     .actions-cell .btn,
     .actions-cell button { /* Target buttons inside forms too */
        display: inline-block;
        margin-left: 3px; /* Reduced margin */
        vertical-align: middle; /* Align buttons nicely */
    }
    .actions-cell .btn {
        padding: 4px 8px;
        font-size: 0.8rem;
    }
    .actions-cell .btn-success { background-color: #28a745; border-color: #28a745; color: white; }
    .actions-cell .btn-danger { background-color: #dc3545; border-color: #dc3545; color: white; }
    .actions-cell .btn-secondary { background-color: #6c757d; border-color: #6c757d; color: white; }
    .actions-cell .btn-warning { background-color: #ffc107; border-color: #ffc107; color: #212529; }
    .actions-cell .btn-info { background-color: #17a2b8; border-color: #17a2b8; color: white; }
    .actions-cell .btn-outline-secondary { /* For details button */
        color: #6c757d; border-color: #6c757d;
    }
     .actions-cell .btn-outline-secondary:hover {
        background-color: #6c757d; color: white;
    }
    .actions-cell .btn i { margin-right: 3px; } /* Optional icon spacing */

    /* Responsive Table */
    @media (max-width: 1100px) {
        .admin-annonces-table {
            display: block;
            overflow-x: auto; /* Allow horizontal scroll on small screens */
            white-space: nowrap; /* Prevent wrapping inside table */
        }
         .admin-annonces-table thead {
            display: none; /* Hide header in horizontal scroll */
        }
         .admin-annonces-table tbody,
         .admin-annonces-table tr,
         .admin-annonces-table td {
            display: block; /* Stack cells vertically */
            text-align: right; /* Align text to right for labels */
            white-space: normal; /* Allow text wrapping in cells */
        }
        .admin-annonces-table tr {
             border: 1px solid #ccc;
             margin-bottom: 10px;
        }
         .admin-annonces-table td {
            padding-left: 50%; /* Make space for label */
            position: relative;
            border-bottom: 1px solid #eee;
         }
         .admin-annonces-table td:before {
            content: attr(data-label); /* Use data-label for responsive headers */
            position: absolute;
            left: 6px;
            width: 45%;
            padding-right: 10px;
            font-weight: bold;
            text-align: left; /* Align labels to left */
            white-space: nowrap;
         }
         .admin-annonces-table td.annonce-thumb,
         .admin-annonces-table td.actions-cell {
            text-align: left; /* Reset alignment for specific cells */
            padding-left: 8px;
         }
         .admin-annonces-table td.actions-cell form,
         .admin-annonces-table td.actions-cell .btn {
             margin-left: 0; margin-right: 5px; margin-bottom: 5px; /* Adjust button spacing */
         }
    }
</style>
@endsection

@section('content')
<div class="admin-content-wrapper">
    <section class="admin-section">
        <h1>Gestion des Annonces</h1>

         {{-- Afficher les messages flash --}}
        @include('partials._flash_messages') {{-- Créez ce partial si nécessaire --}}
        {{-- Ou affichez-les directement --}}
        @if (session('success')) <div class="alert alert-success" role="alert">{{ session('success') }}</div> @endif
        @if (session('info')) <div class="alert alert-info" role="alert">{{ session('info') }}</div> @endif
        @if (session('error')) <div class="alert alert-danger" role="alert">{{ session('error') }}</div> @endif

        <!-- Barre de filtres -->
        <form method="GET" action="{{ route('admin.listings.index') }}" class="filter-bar">
            <div class="form-group">
                <label for="filter-annonce-status" class="sr-only">Statut</label>
                <select id="filter-annonce-status" name="status" class="form-control" onchange="this.form.submit()">
                    <option value="" {{ !request('status') ? 'selected' : '' }}>Statut : Tous</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>En ligne</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejetée</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Brouillon</option>
                    <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archivée</option>
                </select>
            </div>
            <div class="form-group">
                 <label for="filter-annonce-category" class="sr-only">Catégorie</label>
                <select id="filter-annonce-category" name="category" class="form-control" onchange="this.form.submit()">
                    <option value="" {{ !request('category') ? 'selected' : '' }}>Catégorie : Toutes</option>
                    {{-- $categories est passé par AdminListingController@index --}}
                    @foreach($categories ?? [] as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->nom }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                 <label for="filter-annonce-seller" class="sr-only">Vendeur</label>
                <input type="search" id="filter-annonce-seller" name="seller" class="form-control"
                       placeholder="Filtrer par vendeur (ID/Nom)..." value="{{ request('seller') }}">
            </div>
            <!-- Recherche -->
            <div class="search-group">
                 <label for="search-annonce" class="sr-only">Recherche</label>
                <input type="search" id="search-annonce" name="search" class="form-control"
                       placeholder="Rechercher titre/desc/adr..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary" title="Rechercher">
                     <span aria-hidden="true">🔍</span>
                     <span class="sr-only">Rechercher</span>
                 </button>
            </div>
             @if(request()->hasAny(['status', 'category', 'seller', 'search']))
                <a href="{{ route('admin.listings.index') }}" class="btn btn-secondary btn-sm reset-filters">Réinitialiser</a>
            @endif
        </form>

        <!-- Info Pagination -->
         @if(isset($listings) && $listings->total() > 0)
         <div class="pagination-info">
            Affichage de {{ $listings->firstItem() }} à {{ $listings->lastItem() }} sur {{ $listings->total() }} annonces
        </div>
        @else
         <div class="pagination-info">
            Aucune annonce trouvée
        </div>
        @endif

        <!-- Tableau des Annonces -->
         <div class="table-responsive"> {{-- Ensure responsiveness --}}
            <table class="admin-annonces-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Titre</th>
                        <th>Vendeur</th>
                        <th>Catégorie</th>
                        <th>Prix/jour (DH)</th>
                        <th>Statut</th>
                        <th>Publiée le</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($listings ?? [] as $listing)
                        <tr>
                            {{-- Ajouter data-label pour responsive --}}
                            <td data-label="Image" class="annonce-thumb">
                                <img src="{{ $listing->objet?->images?->first()?->public_url ?? asset('images/placeholder.jpg') }}" alt="{{ $listing->title }}">
                            </td>
                            <td data-label="Titre" class="annonce-title">
                                {{-- Lien vers le détail admin si implémenté --}}
                                {{-- <a href="{{ route('admin.listings.show', $listing->id) }}"> --}}
                                    {{ $listing->title }}
                                {{-- </a> --}}
                                <small class="d-block text-muted">ID: {{ $listing->id }}</small>
                            </td>
                            <td data-label="Vendeur" class="annonce-seller">
                                {{-- Lien vers user admin si implémenté --}}
                                {{-- <a href="{{ route('admin.users.show', $listing->user_id) }}"> --}}
                                    {{ $listing->user->full_name ?? 'N/A' }}
                                {{-- </a> --}}
                            </td>
                            <td data-label="Catégorie">{{ $listing->objet?->categorie?->nom ?? 'N/A' }}</td>
                            <td data-label="Prix/jour (DH)">{{ number_format($listing->price, 2) }}</td>
                            <td data-label="Statut">
                                @php
                                     $status = $listing->statut ?? 'unknown';
                                     $statusClass = \Illuminate\Support\Str::slug($status);
                                     $statusLabel = ucfirst($status);
                                @endphp
                                <span class="status-annonce status-annonce-{{ $statusClass }}">{{ $statusLabel }}</span>
                            </td>
                            <td data-label="Publiée le">{{ $listing->date_publication ? \Carbon\Carbon::parse($listing->date_publication)->format('d/m/Y') : ($listing->created_at ? $listing->created_at->format('d/m/Y') : 'N/A') }}</td>
                            <td data-label="Actions" class="actions-cell">
                                {{-- Vérifier que les routes existent bien --}}
                                @if($listing->statut === 'pending')
                                    <form method="POST" action="{{ route('admin.listings.approve', $listing->id) }}" class="d-inline" onsubmit="return confirm('Approuver cette annonce ?')">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-success btn-sm" title="Approuver"><i class="fas fa-check"></i> App.</button> {{-- Icon + Text --}}
                                    </form>
                                    <form method="POST" action="{{ route('admin.listings.reject', $listing->id) }}" class="d-inline" onsubmit="return confirm('Rejeter cette annonce ?')">
                                        @csrf @method('PATCH')
                                        {{-- TODO: Ajouter un champ pour la raison si nécessaire --}}
                                        <button type="submit" class="btn btn-danger btn-sm" title="Rejeter"><i class="fas fa-times"></i> Rej.</button>
                                    </form>
                                @endif

                                @if($listing->statut === 'rejected')
                                    <button type="button" class="btn btn-outline-secondary btn-sm" title="Voir Détails Rejet"
                                            onclick="showRejectionDetails({{ $listing->id }})"><i class="fas fa-info-circle"></i> Dét.</button>
                                @endif

                                @if($listing->statut === 'active')
                                    <form method="POST" action="{{ route('admin.listings.archive', $listing->id) }}" class="d-inline" onsubmit="return confirm('Archiver cette annonce ?')">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-secondary btn-sm" title="Archiver"><i class="fas fa-archive"></i> Arch.</button>
                                    </form>
                                @endif

                                {{-- Modifier --}}
                                <a href="{{ route('admin.listings.edit', $listing->id) }}" class="btn btn-warning btn-sm" title="Modifier"><i class="fas fa-edit"></i> Mod.</a>

                                {{-- Supprimer --}}
                                <form method="POST" action="{{ route('admin.listings.destroy', $listing->id) }}" class="d-inline delete-form" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette annonce définitivement ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Supprimer"><i class="fas fa-trash"></i> Supp.</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center p-4">Aucune annonce trouvée.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
         </div>

        <!-- Pagination -->
        @if(isset($listings) && $listings->hasPages())
            <div class="mt-4 d-flex justify-content-center">
                {{ $listings->appends(request()->query())->links() }}
            </div>
        @endif
    </section>
</div>
{{-- Ajouter Font Awesome si vous utilisez les icônes --}}
{{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> --}}
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Soumission automatique du formulaire lors du changement des filtres <select>
        const filterSelects = document.querySelectorAll('.filter-bar select');
        filterSelects.forEach(select => {
            select.addEventListener('change', () => {
                select.closest('form').submit(); // Trouve le formulaire parent et le soumet
            });
        });

        // Confirmation avant suppression
        const deleteForms = document.querySelectorAll('.delete-form');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!confirm('Êtes-vous sûr de vouloir supprimer cette annonce définitivement ?')) {
                    e.preventDefault(); // Annule la soumission si l'utilisateur clique sur "Annuler"
                }
            });
        });

        // Fonction pour afficher les détails de rejet (nécessite route et logique backend)
        window.showRejectionDetails = function(listingId) {
            // Assurez-vous que la route GET /admin/listings/{listing}/rejection-details existe
            fetch(`/admin/listings/${listingId}/rejection-details`)
                .then(response => {
                    if (!response.ok) {
                         // Gérer les erreurs HTTP (ex: 404 Not Found)
                         throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    alert('Raison du rejet : ' + (data.reason || 'Non spécifiée'));
                })
                .catch(error => {
                    console.error('Erreur lors de la récupération des détails du rejet:', error);
                    alert('Impossible de récupérer les détails du rejet. Vérifiez la console ou l\'implémentation backend.');
                });
        };
    });
</script>
@endsection