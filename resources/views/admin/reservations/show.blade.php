@extends('layouts.admin')

@section('title', "Détail Réservation #{$reservation->id} | Admin")

@section('styles')
<style>
    .detail-section { margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid #eee; }
    .detail-section:last-child { border-bottom: none; }
    .detail-section h3 { font-size: 1.2rem; color: var(--primary-color); margin-bottom: 15px; }
    dt { font-weight: bold; color: #555; }
    dd { margin-left: 0; margin-bottom: 0.5rem; }
</style>
@endsection

@section('content')
<div class="admin-content-wrapper">
    <section class="admin-section">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Détail Réservation #{{ $reservation->id }}</h1>
            <a href="{{ route('admin.reservations.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
        </div>

        @include('partials._flash_messages')

        <div class="row">
            {{-- Colonne Informations principales --}}
            <div class="col-md-7">
                <div class="detail-section">
                    <h3>Informations sur la Réservation</h3>
                    <dl class="row">
                        <dt class="col-sm-4">ID Réservation :</dt>
                        <dd class="col-sm-8">{{ $reservation->id }}</dd>

                        <dt class="col-sm-4">Statut :</dt>
                        <dd class="col-sm-8">
                             <span class="status-badge status-{{ Str::slug($reservation->statut ?? 'inconnu', '-') }}">
                                {{ ucfirst($reservation->statut ?? 'Inconnu') }}
                             </span>
                        </dd>

                        <dt class="col-sm-4">Date de début :</dt>
                        <dd class="col-sm-8">{{ $reservation->date_debut?->format('d/m/Y') ?? 'N/A' }}</dd>

                        <dt class="col-sm-4">Date de fin :</dt>
                        <dd class="col-sm-8">{{ $reservation->date_fin?->format('d/m/Y') ?? 'N/A' }}</dd>

                        <dt class="col-sm-4">Créée le :</dt>
                        <dd class="col-sm-8">{{ $reservation->created_at?->format('d/m/Y H:i:s') ?? 'N/A' }}</dd>

                        <dt class="col-sm-4">Mise à jour le :</dt>
                        <dd class="col-sm-8">{{ $reservation->updated_at?->format('d/m/Y H:i:s') ?? 'N/A' }}</dd>

                        {{-- TODO: Calculer et afficher le montant total si pertinent --}}
                        {{-- <dt class="col-sm-4">Montant Total Estimé :</dt>
                        <dd class="col-sm-8"> XXX.XX DH</dd> --}}
                    </dl>
                </div>

                <div class="detail-section">
                    <h3>Informations sur l'Annonce</h3>
                    @if ($reservation->annonce)
                        <dl class="row">
                            <dt class="col-sm-4">ID Annonce :</dt>
                            <dd class="col-sm-8">{{ $reservation->annonce_id }}</dd>

                            <dt class="col-sm-4">Titre Objet :</dt>
                            <dd class="col-sm-8">{{ $reservation->annonce->objet?->nom ?? 'N/A' }}</dd>

                            <dt class="col-sm-4">Prix Journalier :</dt>
                            <dd class="col-sm-8">{{ number_format($reservation->annonce->objet?->prix_journalier ?? 0, 2) }} DH</dd>

                             <dt class="col-sm-4">Propriétaire :</dt>
                             <dd class="col-sm-8">
                                 {{ $reservation->annonce->proprietaire?->full_name ?? 'N/A' }}
                                 ({{ $reservation->annonce->proprietaire?->email ?? 'N/A' }})
                             </dd>
                        </dl>
                    @else
                        <p class="text-muted">Aucune information d'annonce disponible.</p>
                    @endif
                </div>

            </div>

            {{-- Colonne Client et Actions --}}
            <div class="col-md-5">
                 <div class="detail-section">
                     <h3>Informations sur le Client</h3>
                     @if ($reservation->client)
                        <dl>
                            <dt>Nom Complet :</dt>
                            <dd>{{ $reservation->client->full_name }}</dd>

                            <dt>Email :</dt>
                            <dd>{{ $reservation->client->email }}</dd>

                            {{-- TODO: Ajouter d'autres infos client si nécessaire (tél, etc.) --}}
                        </dl>
                     @else
                         <p class="text-muted">Aucune information client disponible.</p>
                     @endif
                 </div>

                 <div class="detail-section">
                    <h3>Actions Administrateur</h3>
                     @if(in_array($reservation->statut, ['en attente', 'confirmée', 'payée'])) {{-- Adapter --}}
                        <form action="{{ route('admin.reservations.cancel', $reservation->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette réservation ?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-warning" title="Annuler Réservation">
                                <i class="fas fa-ban"></i> Annuler la Réservation
                            </button>
                        </form>
                     @else
                        <p class="text-muted">Aucune action disponible pour le statut actuel ({{ $reservation->statut }}).</p>
                     @endif
                     {{-- TODO: Ajouter d'autres actions --}}
                 </div>

                 {{-- Section Réclamations liées (si vous l'implémentez) --}}
                 {{-- <div class="detail-section">
                    <h3>Réclamations Associées</h3>
                    @forelse($reservation->reclamations ?? [] as $reclamation)
                       <p> - <a href="{{ route('admin.litiges.show', $reclamation->id) }}">Litige #{{ $reclamation->id }}</a> (Statut: {{ $reclamation->statut }}) </p>
                    @empty
                       <p class="text-muted">Aucune réclamation liée.</p>
                    @endforelse
                 </div> --}}

            </div>
        </div>

    </section>
</div>
{{-- Inclure FontAwesome si vous utilisez les icônes --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endsection

@section('scripts')
{{-- Scripts JS spécifiques si nécessaire --}}
@endsection