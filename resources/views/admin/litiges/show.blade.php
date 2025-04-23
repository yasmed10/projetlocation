@extends('layouts.admin')

@section('title', "Détail Litige #{$litige->id} | Admin")

@section('styles')
<style>
    .detail-section { margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid #eee; }
    .detail-section:last-child { border-bottom: none; }
    .detail-section h3 { font-size: 1.2rem; color: var(--primary-color); margin-bottom: 15px; }
    dt { font-weight: bold; color: #555; }
    dd { margin-left: 0; margin-bottom: 0.5rem; }
    .litige-content { background-color: #f8f9fa; padding: 15px; border-radius: 5px; border: 1px solid #dee2e6; white-space: pre-wrap; /* Pour respecter les retours à la ligne */ }
</style>
@endsection

@section('content')
<div class="admin-content-wrapper">
     <section class="admin-section">
         <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Détail Litige (Réclamation) #{{ $litige->id }}</h1>
            <a href="{{ route('admin.litiges.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
        </div>

         @include('partials._flash_messages')

         <div class="row">
             <div class="col-md-8">
                 <div class="detail-section">
                     <h3>Informations sur le Litige</h3>
                    <dl class="row">
                        <dt class="col-sm-3">ID Litige :</dt>
                        <dd class="col-sm-9">{{ $litige->id }}</dd>

                        <dt class="col-sm-3">Statut :</dt>
                        <dd class="col-sm-9">
                            <span class="status-badge status-{{ Str::slug($litige->statut ?? 'inconnu', '-') }}">
                                {{ ucfirst($litige->statut ?? 'Inconnu') }}
                            </span>
                        </dd>

                        <dt class="col-sm-3">Date de Création :</dt>
                        {{-- Attention: La migration originale avait 'date_creation', mais on utilise created_at ici --}}
                        <dd class="col-sm-9">{{ $litige->created_at?->format('d/m/Y H:i:s') ?? 'N/A' }}</dd>

                        <dt class="col-sm-3">Dernière MàJ :</dt>
                         <dd class="col-sm-9">{{ $litige->updated_at?->format('d/m/Y H:i:s') ?? 'N/A' }}</dd>
                    </dl>

                    <h3>Contenu de la Réclamation</h3>
                    <div class="litige-content">
                        {{ $litige->contenu ?? 'Aucun contenu.' }}
                    </div>
                 </div>

                <div class="detail-section">
                    <h3>Informations sur la Réservation Liée</h3>
                     @if ($litige->reservation)
                        <dl class="row">
                             <dt class="col-sm-3">ID Réservation :</dt>
                             <dd class="col-sm-9">
                                 <a href="{{ route('admin.reservations.show', $litige->reservation_id) }}">
                                     {{ $litige->reservation_id }}
                                 </a>
                             </dd>

                             <dt class="col-sm-3">Annonce Concernée :</dt>
                             <dd class="col-sm-9">{{ $litige->reservation->annonce?->objet?->nom ?? 'N/A' }}</dd>

                             <dt class="col-sm-3">Client :</dt>
                             <dd class="col-sm-9">{{ $litige->reservation->client?->full_name ?? 'N/A' }}</dd>

                             <dt class="col-sm-3">Propriétaire Annonce :</dt>
                             <dd class="col-sm-9">{{ $litige->reservation->annonce?->proprietaire?->full_name ?? 'N/A' }}</dd>
                        </dl>
                     @else
                         <p class="text-muted">Aucune réservation liée.</p>
                     @endif
                </div>

             </div>
             <div class="col-md-4">
                 <div class="detail-section">
                    <h3>Utilisateur ayant créé le litige</h3>
                     @if ($litige->utilisateur)
                        <dl>
                            <dt>Nom Complet :</dt>
                            <dd>{{ $litige->utilisateur->full_name }}</dd>

                            <dt>Email :</dt>
                            <dd>{{ $litige->utilisateur->email }}</dd>
                        </dl>
                     @else
                         <p class="text-muted">Utilisateur inconnu.</p>
                     @endif
                 </div>

                 <div class="detail-section">
                     <h3>Actions Administrateur</h3>
                    @if($litige->statut !== 'résolu' && $litige->statut !== 'clos') {{-- Adapter --}}
                        <form action="{{ route('admin.litiges.resolve', $litige->id) }}" method="POST" onsubmit="return confirm('Marquer ce litige comme résolu ?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success w-100" title="Marquer comme Résolu">
                                <i class="fas fa-check-circle"></i> Marquer comme Résolu
                            </button>
                        </form>
                        {{-- TODO: Ajouter bouton pour changer statut vers "en cours" ? --}}
                    @else
                        <p class="text-muted">Ce litige est déjà {{ $litige->statut }}.</p>
                    @endif
                    {{-- TODO: Ajouter la possibilité d'ajouter une note admin, etc. --}}
                 </div>
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