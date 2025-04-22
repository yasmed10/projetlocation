@extends('layouts.admin')

@section('title', 'Admin Dashboard | MeubleCuisine')

@section('styles')
<style>
    /* Styles SPÉCIFIQUES pour le dashboard admin */
    .dashboard-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }
    
    .stat-box {
        background-color: var(--light-bg);
        padding: 20px;
        border-radius: var(--border-radius);
        text-align: center;
        border: 1px solid #eee;
    }
    
    .stat-box .value {
        font-size: 2rem;
        font-weight: bold;
        color: var(--primary-color);
        display: block;
        margin-bottom: 5px;
    }
    
    .stat-box .label {
        font-size: 0.9rem;
        color: #555;
    }
    
    .stat-box a {
        display: block;
        margin-top: 15px;
        font-size: 0.85rem;
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 500;
    }
    
    .stat-box a:hover {
        text-decoration: underline;
    }
</style>
@endsection

@section('content')
<div class="admin-content-wrapper">
    <h1>Tableau de Bord Administrateur</h1>

    <section class="admin-section">
        <h2>Aperçu rapide</h2>
        <div class="dashboard-stats-grid">
            <div class="stat-box">
                <span class="value">{{ $totalUsers ?? 152 }}</span>
                <span class="label">Utilisateurs Total</span>
                <a href="{{ route('admin.users.index') }}">Gérer</a>
            </div>
            <div class="stat-box">
                <span class="value" style="color: var(--warning);">{{ $pendingUsers ?? 5 }}</span>
                <span class="label">Utilisateurs en attente</span>
                <a href="{{ route('admin.users.index', ['status' => 'pending']) }}">Vérifier</a>
            </div>
            <div class="stat-box">
                <span class="value">{{ $activeListings ?? 65 }}</span>
                <span class="label">Annonces Actives</span>
                <a href="{{ route('admin.listings.index') }}">Gérer les annonces</a>
            </div>
            <div class="stat-box">
                <span class="value">{{ $recentSales ?? 12 }}</span>
                <span class="label">Ventes (7 derniers jours)</span>
                <a href="{{ route('admin.stats') }}">Voir stats</a>
            </div>
        </div>
    </section>

    <section class="admin-section">
        <h2>Activité Récente</h2>
        @if(isset($recentActivities) && count($recentActivities) > 0)
            <ul>
                @foreach($recentActivities as $activity)
                    <li>{{ $activity->description }} ({{ $activity->created_at->diffForHumans() }})</li>
                @endforeach
            </ul>
        @else
            <!-- Données statiques pour exemple -->
            <ul>
                <li>Nouvel utilisateur enregistré : Ahmed K. (Aujourd'hui)</li>
                <li>Nouvelle annonce ajoutée : "Table de cuisine extensible" par Fatima Z. (Hier)</li>
                <li>Utilisateur confirmé : Youssef B. (Hier)</li>
                <li>Annonce signalée : "Chaise haute bébé" (ID: 456)</li>
            </ul>
        @endif
    </section>

    <!-- D'autres sections pourraient être ajoutées ici -->
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Scripts spécifiques au dashboard si nécessaire
    });
</script>
@endsection