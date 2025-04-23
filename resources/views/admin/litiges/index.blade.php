@extends('layouts.admin')

@section('title', 'Gestion des Litiges | Admin')

@section('styles')
<style>
    /* Styles badges */
    .status-badge { padding: 3px 8px; border-radius: 12px; font-size: 0.75rem; color: #fff; }
    .status-ouvert { background-color: #dc3545; }
    .status-en-cours { background-color: #ffc107; color: #333; }
    .status-résolu { background-color: #198754; }
    .status-clos { background-color: #adb5bd; color: #333; }
    /* Adaptez les noms de statuts et couleurs */

    .table th, .table td { vertical-align: middle; }
    .actions-cell form { display: inline-block; margin-left: 5px; }
</style>
@endsection

@section('content')
<div class="admin-content-wrapper">
    <section class="admin-section">
        <h1>Gestion des Litiges (Réclamations)</h1>

        @include('partials._flash_messages')

        {{-- Barre de Filtres (Exemple simple) --}}
        <form method="GET" action="{{ route('admin.litiges.index') }}" class="filter-bar mb-3">
             <div class="row g-2">
                <div class="col-md-3">
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Tous les statuts</option>
                        <option value="ouvert" {{ request('status') == 'ouvert' ? 'selected' : '' }}>Ouvert</option>
                        <option value="en cours" {{ request('status') == 'en cours' ? 'selected' : '' }}>En cours</option>
                        <option value="résolu" {{ request('status') == 'résolu' ? 'selected' : '' }}>Résolu</option>
                        <option value="clos" {{ request('status') == 'clos' ? 'selected' : '' }}>Clos</option>
                        {{-- Adaptez les statuts --}}
                    </select>
                </div>
                 <div class="col-md-2">
                     @if(request()->has('status') && request('status') != 'all')
                        <a href="{{ route('admin.litiges.index') }}" class="btn btn-secondary btn-sm w-100">Réinitialiser</a>
                    @endif
                </div>
            </div>
        </form>

        @if (isset($litiges) && $litiges->total() > 0)
            <p class="text-muted mb-2">Affichage de {{ $litiges->firstItem() }} à {{ $litiges->lastItem() }} sur {{ $litiges->total() }} litiges.</p>
        @else
             <p class="text-muted mb-2">Aucun litige trouvé.</p>
        @endif

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID Litige</th>
                        <th>Utilisateur</th>
                        <th>Réservation Liée</th>
                        <th>Statut</th>
                        <th>Créé le</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($litiges ?? [] as $litige)
                    <tr>
                        <td>{{ $litige->id }}</td>
                        <td>{{ $litige->utilisateur?->full_name ?? 'N/A' }} <br><small>{{ $litige->utilisateur?->email }}</small></td>
                        <td>
                            @if($litige->reservation)
                                <a href="{{ route('admin.reservations.show', $litige->reservation_id) }}">
                                    Rés. #{{ $litige->reservation_id }}
                                </a>
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                             <span class="status-badge status-{{ Str::slug($litige->statut ?? 'inconnu', '-') }}">
                                {{ ucfirst($litige->statut ?? 'Inconnu') }}
                             </span>
                        </td>
                        <td>{{ $litige->created_at?->format('d/m/Y H:i') ?? 'N/A' }}</td>
                        <td class="text-end actions-cell">
                             <a href="{{ route('admin.litiges.show', $litige->id) }}" class="btn btn-info btn-sm" title="Voir Détails">
                                <i class="fas fa-eye"></i> Voir
                            </a>
                             {{-- Bouton Résoudre --}}
                            @if($litige->statut !== 'résolu' && $litige->statut !== 'clos') {{-- Adapter --}}
                                <form action="{{ route('admin.litiges.resolve', $litige->id) }}" method="POST" onsubmit="return confirm('Marquer ce litige comme résolu ?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success btn-sm" title="Marquer comme Résolu">
                                        <i class="fas fa-check-circle"></i> Résoudre
                                    </button>
                                </form>
                            @endif
                             {{-- TODO: Ajouter d'autres actions --}}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center p-4">Aucun litige à afficher.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

         @if(isset($litiges) && $litiges->hasPages())
            <div class="mt-4 d-flex justify-content-center">
                {{ $litiges->links() }}
            </div>
        @endif

    </section>
</div>
{{-- Inclure FontAwesome si vous utilisez les icônes --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endsection

@section('scripts')
{{-- Scripts JS spécifiques si nécessaire --}}
@endsection