@extends('layouts.admin')

@section('title', 'Paramètres | Admin MeubleCuisine')

@section('styles')
<style>
    /* Styles SPÉCIFIQUES pour la page des paramètres */
    .admin-content-wrapper {
        max-width: 900px; /* Limite largeur pour paramètres */
    }
    .admin-section .form-group {
        margin-bottom: 25px;
    }
    .admin-section label {
        margin-bottom: 8px;
        font-weight: 500;
        display: block;
    }
    .admin-section small {
        font-size: 0.85rem;
        color: #777;
        display: block;
        margin-top: 5px;
    }
    .admin-section .form-control[type="number"] {
        max-width: 150px; /* Limite largeur input numérique */
    }
    /* Style pour les champs non modifiables ici */
    .admin-section .form-control[readonly],
    .admin-section .form-control:disabled, /* Ajouté pour les selects désactivés */
    .admin-section textarea[readonly] {
        background-color: #e9ecef;
        cursor: not-allowed;
        opacity: 0.7; /* Un peu de transparence pour indiquer */
    }

    .category-management {
        margin-top: 20px;
    }
    .category-list {
        list-style: none;
        padding: 0;
    }
    .category-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid #eee;
    }
    .category-item:last-child {
        border-bottom: none;
    }
    .category-item span {
        flex-grow: 1;
        margin-right: 15px;
    }
    .category-item .delete-form { /* Style pour le formulaire de suppression */
        display: inline; /* Pour garder le bouton sur la même ligne */
    }
    .category-item button {
        padding: 3px 8px;
        font-size: 0.8rem;
    }
    .add-category-form {
        display: flex;
        gap: 10px;
        margin-top: 15px;
    }
    .add-category-form input {
        flex-grow: 1;
    }
</style>
@endsection

@section('content')
<div class="admin-content-wrapper">
    <h1>Paramètres du Site</h1>

    {{-- Afficher les messages flash (succès ou info) --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif


    {{-- Le formulaire est toujours nécessaire pour la gestion des catégories --}}
    {{-- Mais les champs de paramètres seront readonly/disabled --}}
    <form action="{{ route('admin.settings.update') }}" method="POST" id="settings-form">
        @csrf
        @method('PATCH')

        <section class="admin-section">
            <h2>Paramètres Généraux (Non modifiables ici)</h2>
             <p class="text-muted"><small>Ces paramètres sont définis dans les fichiers de configuration.</small></p>
            <div class="form-group">
                <label for="site-name">Nom du Site</label>
                <input type="text" id="site-name" name="settings[site_name]" class="form-control"
                       value="{{ old('settings.site_name', $settings['site_name'] ?? 'MeubleCuisine') }}" readonly>
                @error('settings.site_name') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label for="contact-email">Email de Contact Principal</label>
                <input type="email" id="contact-email" name="settings[contact_email]" class="form-control"
                       value="{{ old('settings.contact_email', $settings['contact_email'] ?? 'contact@meublecuisine.ma') }}" readonly>
                @error('settings.contact_email') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
             <div class="form-group">
                 <label for="contact-phone">Téléphone Principal</label>
                 <input type="tel" id="contact-phone" name="settings[contact_phone]" class="form-control"
                        value="{{ old('settings.contact_phone', $settings['contact_phone'] ?? '+212 5 00 00 00 00') }}" readonly>
                 @error('settings.contact_phone') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label for="main-address">Adresse Principale (Showroom)</label>
                <textarea id="main-address" name="settings[main_address]" class="form-control" rows="3" readonly>{{ old('settings.main_address', $settings['main_address'] ?? "Showroom: 123 Boulevard Anfa,\nCasablanca, Maroc") }}</textarea>
                @error('settings.main_address') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
             {{-- Bouton de sauvegarde retiré ou désactivé pour cette section
             <button type="submit" name="save_general" class="btn btn-secondary" disabled>Sauvegarder Généraux</button>
             --}}
        </section>

        <section class="admin-section">
            <h2>Paramètres de Vente (Non modifiables ici)</h2>
            <p class="text-muted"><small>Ces paramètres sont définis dans les fichiers de configuration.</small></p>
             <div class="form-group">
                <label for="commission-rate">Taux de Commission (%)</label>
                <input type="number" id="commission-rate" name="settings[commission_rate]" class="form-control"
                       value="{{ old('settings.commission_rate', $settings['commission_rate'] ?? 15) }}" step="0.1" min="0" max="100" readonly>
                <small>Pourcentage prélevé sur chaque vente réalisée par les propriétaires.</small>
                @error('settings.commission_rate') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
             <div class="form-group">
                 <label for="max-reservation-days">Durée Maximale de Réservation (jours)</label>
                 <input type="number" id="max-reservation-days" name="settings[max_reservation_days]" class="form-control"
                        value="{{ old('settings.max_reservation_days', $settings['max_reservation_days'] ?? 7) }}" step="1" min="1" readonly>
                 <small>Nombre maximum de jours pendant lesquels un article peut être réservé.</small>
                 @error('settings.max_reservation_days') <span class="text-danger">{{ $message }}</span> @enderror
             </div>
             <div class="form-group">
                 <label for="pending-listing-approval">Approbation manuelle des annonces</label>
                 {{-- Utiliser disabled pour le select car readonly n'est pas standard --}}
                 <select id="pending-listing-approval" name="settings[listing_approval]" class="form-control" disabled>
                     <option value="1" {{ old('settings.listing_approval', $settings['listing_approval'] ?? 1) == 1 ? 'selected' : '' }}>Oui (Requis)</option>
                     <option value="0" {{ old('settings.listing_approval', $settings['listing_approval'] ?? 1) == 0 ? 'selected' : '' }}>Non (Publication immédiate)</option>
                 </select>
                 <small>Si 'Oui', les nouvelles annonces devront être approuvées par un admin avant d'être visibles.</small>
                 @error('settings.listing_approval') <span class="text-danger">{{ $message }}</span> @enderror
             </div>
              {{-- Bouton de sauvegarde retiré ou désactivé pour cette section
              <button type="submit" name="save_sales" class="btn btn-secondary" disabled>Sauvegarder Ventes</button>
               --}}
        </section>

    </form> {{-- Fin du formulaire principal des paramètres --}}

    {{-- La gestion des catégories reste fonctionnelle --}}
    <section class="admin-section">
        <h2>Gestion des Catégories</h2>
        <div class="category-management">
            <label>Catégories Actuelles :</label>
            <ul class="category-list" id="category-list-ul">
                {{-- S'assure que $categories est défini et itérable --}}
                @forelse($categories ?? [] as $category)
                    <li class="category-item" data-id="{{ $category->id }}">
                        <span>{{ $category->name }}</span>
                        {{-- Formulaire pour supprimer une catégorie (méthode DELETE) --}}
                        <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="delete-form" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer la catégorie \'{{ $category->name }}\' ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                        </form>
                    </li>
                @empty
                    <li class="text-muted">Aucune catégorie définie.</li>
                @endforelse
            </ul>

            {{-- Formulaire pour ajouter une nouvelle catégorie (POST) --}}
            <form action="{{ route('admin.categories.store') }}" method="POST" class="add-category-form" id="add-category-form">
                @csrf
                <input type="text" id="new-category-name" name="name" class="form-control" placeholder="Nom de la nouvelle catégorie" required>
                <button type="submit" id="add-category-btn" class="btn btn-success">Ajouter</button>
            </form>
            @error('name') {{-- Erreur de validation pour l'ajout --}}
                <div class="text-danger mt-2">{{ $message }}</div>
            @enderror
        </div>
    </section>

     {{-- Aucun bouton de sauvegarde global n'est nécessaire si les paramètres ne sont pas sauvegardés --}}

</div>
@endsection

@section('scripts')
{{-- Les scripts JS pour la gestion AJAX des catégories peuvent rester si vous les utilisez --}}
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Scripts spécifiques si besoin
    });
</script>
@endsection