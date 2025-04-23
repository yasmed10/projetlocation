@extends('layouts.admin')

@section('title', 'Gestion Annonces | Admin MeubleCuisine')

@section('styles')
<style> /* Styles existants (raccourci) */
    .filter-bar { /*...*/ } .pagination-info { /*...*/ } .admin-annonces-table { /*...*/ }
    .admin-annonces-table th, .admin-annonces-table td { padding: 10px 8px; /*...*/ }
    .status-annonce { padding: 4px 10px; /*...*/ }
    .status-annonce-rejected { background-color: #fdecea; color: #a61d24; border-color: #f8b4b8; } /* Pas de curseur help */
    /* ... autres statuts ... */
    .actions-cell { white-space: nowrap; text-align: right; }
    .actions-cell form, .actions-cell .btn { display: inline-block; margin-left: 3px; vertical-align: middle; }
    .actions-cell .btn { padding: 4px 8px; font-size: 0.8rem; }
    .actions-cell .btn i { margin-right: 3px; }
</style>
@endsection

@section('content')
<div class="admin-content-wrapper">
    <section class="admin-section">
        <h1>Gestion des Annonces</h1>
        @include('partials._flash_messages')
        <form method="GET" action="{{ route('admin.listings.index') }}" class="filter-bar">
             {{-- Filtres (inchangÃ©s) --}}
             <select name="status" onchange="this.form.submit()">...</select>
             <select name="category" onchange="this.form.submit()">...</select>
             <input type="search" name="seller" placeholder="Vendeur..." value="{{ request('seller') }}">
             <div class="search-group"><input type="search" name="search" placeholder="Rechercher..." value="{{ request('search') }}"><button>ðŸ” </button></div>
             @if(request()->hasAny(...)) <a href="{{ route('admin.listings.index') }}" class="btn btn-secondary btn-sm">RÃ©initialiser</a> @endif
        </form>

         @if(isset($listings) && $listings->total() > 0) <div class="pagination-info">Affichage...</div> @else <div class="pagination-info">Aucune annonce</div> @endif

         <div class="table-responsive">
            <table class="admin-annonces-table">
                <thead> <tr> <th>Image</th> <th>Titre</th> <th>Vendeur</th> <th>CatÃ©gorie</th> <th>Prix/jour</th> <th>Statut</th> <th>PubliÃ©e le</th> <th class="text-right">Actions</th> </tr> </thead>
                <tbody>
                    @forelse($listings ?? [] as $listing)
                        <tr>
                            <td data-label="Image"><img src="{{ $listing->thumbnail }}" alt="" style="width:50px; height:auto;"></td>
                            <td data-label="Titre">{{ $listing->title }} <small>ID:{{ $listing->id }}</small></td>
                            <td data-label="Vendeur">{{ $listing->user->full_name ?? 'N/A' }}</td>
                            <td data-label="CatÃ©gorie">{{ $listing->category?->nom ?? 'N/A' }}</td>
                            <td data-label="Prix/jour">{{ number_format($listing->price, 2) }} DH</td>
                            <td data-label="Statut">
                                <span class="status-annonce status-annonce-{{ $listing->statut_class }}">
                                      {{ $listing->statut_label }}
                                </span>
                                {{-- !! Tooltip avec raison retirÃ© !! --}}
                            </td>
                            <td data-label="PubliÃ©e le">{{ $listing->date_publication ? $listing->date_publication->format('d/m/Y') : 'N/A' }}</td>
                            <td data-label="Actions" class="actions-cell">
                                @if($listing->statut === 'pending')
                                    {{-- Approuver --}}
                                    <form method="POST" action="{{ route('admin.listings.approve', $listing->id) }}" class="d-inline" onsubmit="return confirm('Approuver ?')">@csrf @method('PATCH')<button type="submit" class="btn btn-success btn-sm" title="Approuver"><i class="fas fa-check"></i></button></form>
                                    {{-- Rejeter (Direct) --}}
                                    <form method="POST" action="{{ route('admin.listings.reject', $listing->id) }}" class="d-inline" onsubmit="return confirm('Rejeter cette annonce ?')">@csrf @method('PATCH')<button type="submit" class="btn btn-danger btn-sm" title="Rejeter"><i class="fas fa-times"></i></button></form>
                                @endif

                                {{-- !! Bouton Voir Raison RetirÃ© !! --}}

                                @if(in_array($listing->statut, ['active', 'pending', 'rejected']))
                                    {{-- Archiver --}}
                                    <form method="POST" action="{{ route('admin.listings.archive', $listing->id) }}" class="d-inline" onsubmit="return confirm('Archiver ?')">@csrf @method('PATCH')<button type="submit" class="btn btn-secondary btn-sm" title="Archiver"><i class="fas fa-archive"></i></button></form>
                                @endif

                                @if($listing->statut === 'archived')
                                    {{-- DÃ©sarchiver --}}
                                     <form method="POST" action="{{ route('admin.listings.unarchive', $listing->id) }}" class="d-inline" onsubmit="return confirm('DÃ©sarchiver ?')">@csrf @method('PATCH')<button type="submit" class="btn btn-info btn-sm" title="DÃ©sarchiver"><i class="fas fa-box-open"></i></button></form>
                                @endif

                                {{-- Modifier --}}
                                <a href="{{ route('admin.listings.edit', $listing->id) }}" class="btn btn-warning btn-sm" title="Modifier"><i class="fas fa-edit"></i></a>

                                {{-- Supprimer --}}
                                <form method="POST" action="{{ route('admin.listings.destroy', $listing->id) }}" class="d-inline" onsubmit="return confirm('Supprimer DÃ‰FINITIVEMENT ?')">@csrf @method('DELETE')<button type="submit" class="btn btn-danger btn-sm" title="Supprimer"><i class="fas fa-trash"></i></button></form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center p-4">Aucune annonce trouvÃ©e.</td></tr>
                    @endforelse
                </tbody>
            </table>
         </div>
        @if(isset($listings) && $listings->hasPages()) <div class="mt-4 d-flex justify-content-center">{{ $listings->links() }}</div> @endif
    </section>

    {{-- !! Modale de Rejet RetirÃ©e !! --}}

</div>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> {{-- Si vous utilisez les icÃ´nes --}}
@endsection

@section('scripts')
 {{-- !! Scripts JS pour la modale retirÃ©s !! --}}
@endsection