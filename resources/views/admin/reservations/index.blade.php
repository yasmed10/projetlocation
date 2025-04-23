@extends('layouts.admin')

@section('title', 'Gestion des Réservations | Admin')

@section('styles')
<style>
    /* Styles spécifiques si nécessaire, ex: badges de statut */
    .status-badge { padding: 3px 8px; border-radius: 12px; font-size: 0.75rem; color: #fff; }
    .status-en-attente { background-color: #ffc107; color: #333; }
    .status-confirmée { background-color: #198754; }
    .status-annulée_client { background-color: #dc3545; }
    .status-annulée_admin { background-color: #adb5bd; color: #333; }
    .status-terminée { background-color: #0dcaf0; color: #333; }
    .status-payée { background-color: #0d6efd; }
    /* Ajoutez d'autres statuts au besoin */

    .table th, .table td { vertical-align: middle; }
    .actions-cell form { display: inline-block; margin-left: 5px; }
</style>
@endsection

@section('content')
<div class="admin-content-wrapper">
    <section class="admin-section">
        <h1>Gestion des Réservations</h1>

        @include('partials._flash_messages')

        {{-- Barre de Filtres (Exemple simple) --}}
        <form method="GET" action="{{ route('admin.reservations.index') }}" class="filter-bar mb-3">
            <div class="row g-2">
                <div class="col-md-3">
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Tous les statuts</option>
                        <option value="en attente" {{ request('status') == 'en attente' ? 'selected' : '' }}>En attente</option>
                        <option value="confirmée" {{ request('status') == 'confirmée' ? 'selected' : '' }}>Confirmée</option>
                        <option value="payée" {{ request('status') == 'payée' ? 'selected' : '' }}>Payée</option>
                        <option value="annulée_client" {{ request('status') == 'annulée_client' ? 'selected' : '' }}>Annulée (Client)</option>
                        <option value="annulée_admin" {{ request('status') == 'annulée_admin' ? 'selected' : '' }}>Annulée (Admin)</option>
                        <option value="terminée" {{ request('status') == 'terminée' ? 'selected' : '' }}>Terminée</option>
                        {{-- Ajoutez d'autres statuts --}}
                    </select>
                </div>
                {{-- TODO: Ajouter d'autres filtres (dates, client, etc.) --}}
                <div class="col-md-2">
                     @if(request()->has('status') && request('status') != 'all')
                        <a href="{{ route('admin.reservations.index') }}" class="btn btn-secondary btn-sm w-100">Réinitialiser</a>
                    @endif
                </div>
            </div>
        </form>

        @if (isset($reservations) && $reservations->total() > 0)
            <p class="text-muted mb-2">Affichage de {{ $reservations->firstItem() }} à {{ $reservations->lastItem() }} sur {{ $reservations->total() }} réservations.</p>
        @else
            <p class="text-muted mb-2">Aucune réservation trouvée.</p>
        @endif

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Client</th>
                        <th>Annonce</th>
                        <th>Dates</th>
                        <th>Statut</th>
                        <th>Créée le</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reservations ?? [] as $reservation)
                    <tr>
                        <td>{{ $reservation->id }}</td>
                        <td>{{ $reservation->client?->full_name ?? 'N/A' }} <br><small>{{ $reservation->client?->email }}</small></td>
                        <td>
                            @if($reservation->annonce)
                                {{ $reservation->annonce->objet?->nom ?? 'Objet N/A' }} <small>(Annonce #{{ $reservation->annonce_id }})</small>
                            @else
                                Annonce N/A
                            @endif
                        </td>
                        <td>
                            {{ $reservation->date_debut?->format('d/m/Y') ?? 'N/A' }} au
                            {{ $reservation->date_fin?->format('d/m/Y') ?? 'N/A' }}
                        </td>
                        <td>
                            <span class="status-badge status-{{ Str::slug($reservation->statut ?? 'inconnu', '-') }}">
                                {{ ucfirst($reservation->statut ?? 'Inconnu') }}
                            </span>
                        </td>
                        <td>{{ $reservation->created_at?->format('d/m/Y H:i') ?? 'N/A' }}</td>
                        <td class="text-end actions-cell">
                            <a href="{{ route('admin.reservations.show', $reservation->id) }}" class="btn btn-info btn-sm" title="Voir Détails">
                                <i class="fas fa-eye"></i> Voir
                            </a>
                            {{-- Bouton Annuler --}}
                            @if(in_array($reservation->statut, ['en attente', 'confirmée', 'payée'])) {{-- Adapter les statuts annulables --}}
                                <form action="{{ route('admin.reservations.cancel', $reservation->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette réservation ?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-warning btn-sm" title="Annuler Réservation">
                                        <i class="fas fa-ban"></i> Annuler
                                    </button>
                                </form>
                            @endif
                            {{-- TODO: Ajouter d'autres actions si nécessaire --}}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center p-4">Aucune réservation à afficher.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($reservations) && $reservations->hasPages())
            <div class="mt-4 d-flex justify-content-center">
                {{ $reservations->links() }}
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