@extends('layouts.admin')

@section('title', 'Gestion Utilisateurs | Admin MeubleCuisine')

@section('styles')
<style>
    .filter-bar { 
        display: flex; 
        align-items: center; 
        margin-bottom: 1rem; 
        gap: 1rem;
    }
    .admin-users-table { 
        width: 100%; 
        border-collapse: collapse; 
        margin-bottom: 1rem; 
    }
    .admin-users-table th, 
    .admin-users-table td { 
        padding: 10px 8px; 
        border-bottom: 1px solid #dee2e6; 
    }
    .status-badge { 
        padding: 3px 8px; 
        border-radius: 12px; 
        font-size: 0.75rem; 
        display: inline-block; 
    }
    .status-badge-cin-pending { 
        background-color: #fff3cd; 
        color: #856404; 
        border-color: #ffeeba; 
    }
    .status-badge-cin-verified { 
        background-color: #d1ecf1; 
        color: #0c5460; 
        border-color: #bee5eb; 
    }
    .status-badge-archived { 
        background-color: #f8d7da; 
        color: #721c24; 
        border-color: #f5c6cb; 
    }
    .actions-cell { 
        white-space: nowrap; 
        text-align: right; 
    }
    .actions-cell form, 
    .actions-cell a.btn { 
        display: inline-block; 
        margin-left: 3px; 
        vertical-align: middle; 
    }
    .actions-cell .btn { 
        padding: 4px 8px; 
        font-size: 0.8rem; 
    }
    .user-info { 
        display: flex; 
        align-items: center; 
        gap: 1rem; 
    }
    .user-info img { 
        width: 40px; 
        height: 40px; 
        border-radius: 50%; 
        object-fit: cover; 
    }
    .user-info .name { 
        display: block; 
        font-weight: 500; 
    }
    .user-info .email { 
        display: block; 
        font-size: 0.85rem; 
        color: #6c757d; 
    }
</style>
@endsection

@section('content')
<div class="admin-content-wrapper">
    <section class="admin-section">
        <h1>Gestion des Utilisateurs</h1>
        @include('partials._flash_messages')

        <div class="filter-bar">
            <a href="{{ request()->has('show_archived') ? route('admin.users.index') : route('admin.users.index', ['show_archived' => 1]) }}" 
               class="btn {{ request()->has('show_archived') ? 'btn-secondary' : 'btn-primary' }}">
                {{ request()->has('show_archived') ? 'Masquer les utilisateurs archivés' : 'Afficher les utilisateurs archivés' }}
            </a>
        </div>

        <div class="table-responsive">
            <table class="admin-users-table">
                <thead>
                    <tr>
                        <th>Utilisateur</th>
                        <th>Rôle</th>
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
                            <div>
                                <span class="name">{{ $user->full_name }}</span>
                                <span class="email">{{ $user->email }}</span>
                                @if($user->archived_at)
                                    <span class="status-badge status-badge-archived">Archivé</span>
                                @endif
                            </div>
                        </td>
                        <td data-label="Rôle">{{ ucfirst($user->role) }}</td>
                        <td data-label="CIN">
                            @if ($user->isCinConsideredVerified())
                                <span class="status-badge status-badge-cin-verified">Vérifiée</span>
                            @elseif ($user->needsCinVerification())
                                <span class="status-badge status-badge-cin-pending">À vérifier</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td data-label="Inscrit le">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td class="actions-cell" data-label="Actions">
                            @if(!$user->archived_at)
                                @if($user->needsCinVerification())
                                    <a href="{{ route('admin.users.verify_cin', $user->id) }}" 
                                       class="btn btn-warning btn-sm" title="Vérifier CIN">
                                        <i class="fas fa-id-card"></i>
                                    </a>
                                @endif

                                <form action="{{ route('admin.users.archive', $user->id) }}" method="POST" 
                                      class="d-inline" onsubmit="return confirm('Voulez-vous vraiment archiver cet utilisateur ?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Archiver l'utilisateur">
                                        Archiver
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('admin.users.restore', $user->id) }}" method="POST" 
                                      class="d-inline" onsubmit="return confirm('Voulez-vous vraiment restaurer cet utilisateur ?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success btn-sm" title="Restaurer l'utilisateur">
                                        Restaurer
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center p-4">Aucun utilisateur trouvé.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection