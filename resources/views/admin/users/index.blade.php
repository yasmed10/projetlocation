@extends('layouts.admin')

@section('title', 'Gestion Utilisateurs | Admin MeubleCuisine')

@section('styles')
<style> /* Styles existants (raccourci) */
    .filter-bar { /*...*/ } .pagination-info { /*...*/ } .admin-users-table { /*...*/ }
    .admin-users-table th, .admin-users-table td { padding: 10px 8px; /*...*/ }
    .status-badge { padding: 3px 8px; border-radius: 12px; font-size: 0.75rem; /*...*/ }
    .status-badge-cin-pending { background-color: #fff3cd; color: #856404; border-color: #ffeeba; }
    .status-badge-cin-verified { background-color: #d1ecf1; color: #0c5460; border-color: #bee5eb; } /* ConsidÃ©rÃ© si role=proprietaire */
    .actions-cell { white-space: nowrap; text-align: right; }
    .actions-cell form, .actions-cell a.btn { display: inline-block; margin-left: 3px; vertical-align: middle; }
    .actions-cell .btn { padding: 4px 8px; font-size: 0.8rem; }
</style>
@endsection

@section('content')
<div class="admin-content-wrapper">
    <section class="admin-section">
        <h1>Gestion des Utilisateurs</h1>
        @include('partials._flash_messages')

        <form method="GET" action="{{ route('admin.users.index') }}" class="filter-bar">
            {{-- TODO: Ajouter filtres si besoin --}}
            <div class="search-group ml-auto">
                <input type="search" name="search" class="form-control" placeholder="Rechercher..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary" title="Rechercher">ðŸ” </button>
            </div>
             @if(request()->has('search')) <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm reset-filters">RÃ©initialiser</a> @endif
        </form>

        @if ($users->total() > 0) <div class="pagination-info">Affichage...</div> @else <div class="pagination-info">Aucun utilisateur</div> @endif

        <div class="table-responsive">
            <table class="admin-users-table">
                <thead>
                    <tr>
                        <th>Utilisateur</th>
                        <th>RÃ´le</th>
                        {{-- Colonne Statut (Blocage) RetirÃ©e --}}
                        <th>CIN</th>
                        <th>Inscrit le</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td class="user-info" data-label="Utilisateur">
                            <img src="{{ $user->avatar_url }}" alt="Avatar">
                            <div><span class="name">{{ $user->full_name }}</span><span class="email">{{ $user->email }}</span></div>
                        </td>
                        <td data-label="RÃ´le">{{ ucfirst($user->role) }}</td>
                        {{-- Cellule Statut (Blocage) RetirÃ©e --}}
                        <td data-label="CIN">
                             @if ($user->isCinConsideredVerified()) {{-- role == proprietaire --}}
                                 <span class="status-badge status-badge-cin-verified">VÃ©rifiÃ©e</span>
                             @elseif ($user->needsCinVerification()) {{-- role != prop et fichiers prÃ©sents --}}
                                 <span class="status-badge status-badge-cin-pending">Ã€ vÃ©rifier</span>
                             @else
                                 <span class="text-muted">-</span> {{-- Pas proprietaire et/ou pas de fichiers --}}
                             @endif
                        </td>
                        <td data-label="Inscrit le">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td class="actions-cell" data-label="Actions">
                             {{-- Approuver (Action symbolique) --}}
                             <form action="{{ route('admin.users.approve', $user->id) }}" method="POST" onsubmit="return confirm('Marquer comme approuvÃ© (action informative) ?')">
                                 @csrf @method('PATCH')
                                 <button type="submit" class="btn btn-success btn-sm" title="Approuver (informatif)">App.</button>
                             </form>

                            {{-- !! Boutons Bloquer / DÃ©bloquer RetirÃ©s !! --}}

                            {{-- VÃ©rifier CIN (Lien) --}}
                            @if($user->needsCinVerification())
                                <a href="{{ route('admin.users.verify_cin', $user->id) }}" class="btn btn-warning btn-sm" title="VÃ©rifier CIN">CIN</a>
                            @endif

                            {{-- Rejeter/Supprimer --}}
                            <form action="{{ route('admin.users.reject', $user->id) }}" method="POST" onsubmit="return confirm('ATTENTION: Supprimer cet utilisateur DÃ‰FINITIVEMENT ?')">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-danger btn-sm" title="Supprimer Utilisateur">Supp.</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center p-4">Aucun utilisateur trouvÃ©.</td></tr> {{-- Colspan ajustÃ© --}}
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center">{{ $users->links() }}</div>
    </section>
</div>
@endsection