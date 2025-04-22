{{-- resources/views/admin/stats.blade.php --}}

@extends('layouts.admin')

@section('title', 'Statistiques | Admin MeubleCuisine')

@section('styles')
{{-- Inclure Chart.js si nécessaire (peut aussi être dans le layout principal si utilisé ailleurs) --}}
{{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}
<style>
    /* Styles spécifiques stats */
    .date-range-selector {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid #eee;
    }
    .date-range-selector h2 {
        margin: 0 20px 0 0;
        font-size: 1.3rem;
        border-bottom: none;
        padding-bottom: 0;
    }
    .date-range-selector .date-inputs {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-left: auto;
    }
    .date-range-selector label {
        font-size: 0.9rem;
        color: #555;
        margin-bottom: 0;
    }
    .date-range-selector input[type="date"] {
        padding: 6px 10px;
        border: 1px solid #ccc;
        border-radius: var(--border-radius);
        font-size: 0.9rem;
        height: 36px;
    }
    .date-range-selector .btn {
        padding: 6px 15px;
        height: 36px;
    }

    .dashboard-stats-grid { /* Réutilisation du style dashboard */
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    .stat-box { /* Réutilisation du style dashboard */
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

    .charts-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 30px;
        margin-bottom: 30px;
    }
    .chart-container {
        border: 1px solid #eee;
        padding: 20px;
        border-radius: var(--border-radius);
        background-color: #fff; /* Ajout pour démarquer */
    }
    .chart-container h3 {
        margin-top: 0;
        margin-bottom: 20px;
        font-size: 1.1rem;
        color: var(--secondary-color);
        text-align: center;
    }
    /* Enlever le placeholder si canvas est présent */
    /* .chart-placeholder { background-color: #f0f0f0; min-height: 250px; display: flex; align-items: center; justify-content: center; color: #aaa; font-style: italic; border-radius: var(--border-radius); } */
    .chart-container canvas {
        max-width: 100%;
        height: auto;
        min-height: 250px; /* Garder une hauteur min */
    }

    .key-numbers-table {
        width: 100%;
        max-width: 600px;
        margin: 20px auto;
        border-collapse: collapse;
    }
    .key-numbers-table td {
        padding: 10px;
        border-bottom: 1px solid #eee;
    }
    .key-numbers-table tr:last-child td {
        border-bottom: none;
    }
    .key-numbers-table td:first-child {
        color: #555;
    }
    .key-numbers-table td:last-child {
        font-weight: bold;
        text-align: right;
        color: var(--primary-color);
    }
</style>
@endsection

@section('content')
<div class="admin-content-wrapper">
    <section class="admin-section">
         <!-- Sélecteur de Période -->
         {{-- Utilisation de GET pour permettre le partage/bookmarking des filtres --}}
        <form method="GET" action="{{ route('admin.stats') }}" class="date-range-selector">
            <h2>Statistiques du Site</h2>
            <div class="date-inputs">
                 <label for="start_date">Du</label>
                 {{-- request() pour récupérer la valeur actuelle du filtre ou une valeur par défaut --}}
                 <input type="date" id="start_date" name="start_date" class="form-control" value="{{ request('start_date', $defaultStartDate ?? now()->subMonth()->format('Y-m-d')) }}">
                 <label for="end_date">au</label>
                 <input type="date" id="end_date" name="end_date" class="form-control" value="{{ request('end_date', $defaultEndDate ?? now()->format('Y-m-d')) }}">
                 <button type="submit" class="btn btn-primary">Filtrer</button>
            </div>
        </form>

         <!-- Indicateurs Clés (Stats Rapides pour la période sélectionnée) -->
         <h2>Indicateurs Clés (Période Sélectionnée)</h2>
         <div class="dashboard-stats-grid">
              <div class="stat-box"> <span class="value">{{ number_format($statsPeriod['uniqueVisitors'] ?? 0) }}</span> <span class="label">Visiteurs Uniques</span> </div>
              <div class="stat-box"> <span class="value">{{ number_format($statsPeriod['pageViews'] ?? 0) }}</span> <span class="label">Pages Vues</span> </div>
              <div class="stat-box"> <span class="value">{{ $statsPeriod['newUsers'] ?? 0 }}</span> <span class="label">Nouvelles Inscriptions</span> </div>
              <div class="stat-box"> <span class="value">{{ $statsPeriod['newListings'] ?? 0 }}</span> <span class="label">Nouvelles Annonces</span> </div>
         </div>

         <!-- Section Graphiques -->
         <h2>Tendances</h2>
         <div class="charts-grid">
             <div class="chart-container">
                <h3>Évolution des Utilisateurs</h3>
                {{-- Le canvas recevra les données via JS --}}
                <canvas id="userChart"></canvas>
             </div>
             <div class="chart-container">
                <h3>Évolution des Annonces</h3>
                <canvas id="listingChart"></canvas>
             </div>
              <div class="chart-container">
                <h3>Volume des Ventes (DH)</h3>
                <canvas id="salesChart"></canvas>
             </div>
              <div class="chart-container">
                <h3>Catégories Populaires (Nb Annonces)</h3>
                <canvas id="categoryChart"></canvas>
             </div>
         </div>

         <!-- Chiffres Clés (Total) -->
          <h2>Chiffres Clés (Total)</h2>
          <table class="key-numbers-table">
              <tbody>
                  <tr><td>Nombre total d'utilisateurs enregistrés</td><td>{{ $statsTotal['totalUsers'] ?? 0 }}</td></tr>
                  <tr><td>Nombre total d'annonces publiées</td><td>{{ $statsTotal['totalListings'] ?? 0 }}</td></tr>
                   <tr><td>Prix moyen d'une annonce</td><td>{{ number_format($statsTotal['averageListingPrice'] ?? 0, 2) }} DH</td></tr>
                  <tr><td>Volume total des ventes (depuis début)</td><td>{{ number_format($statsTotal['totalSalesVolume'] ?? 0, 2) }} DH</td></tr>
                   <tr><td>Commission totale générée</td><td>{{ number_format($statsTotal['totalCommission'] ?? 0, 2) }} DH</td></tr>
              </tbody>
          </table>

    </section>
</div>
@endsection

@section('scripts')
{{-- Charger Chart.js ici si pas chargé globalement --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {

        // --- Initialisation des graphiques ---
        // Les données doivent être passées depuis le contrôleur, idéalement en JSON

        // Exemple pour User Chart (adapter pour les autres)
        const userChartCtx = document.getElementById('userChart')?.getContext('2d');
        const userData = @json($chartData['users'] ?? ['labels' => [], 'data' => []]); // Récupère les données JSON
        if (userChartCtx && userData.labels.length > 0) {
            new Chart(userChartCtx, {
                type: 'line',
                data: {
                    labels: userData.labels,
                    datasets: [{
                        label: 'Nouveaux Utilisateurs',
                        data: userData.data,
                        borderColor: 'rgba(54, 162, 235, 1)', // Couleur Primaire
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        tension: 0.1,
                        fill: true
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        } else if(userChartCtx) {
             userChartCtx.font = "16px Arial"; userChartCtx.fillStyle = "#aaa"; userChartCtx.textAlign = "center"; userChartCtx.fillText("Pas de données pour cette période", userChartCtx.canvas.width / 2, userChartCtx.canvas.height / 2);
        }

        // Exemple pour Listing Chart
        const listingChartCtx = document.getElementById('listingChart')?.getContext('2d');
        const listingData = @json($chartData['listings'] ?? ['labels' => [], 'data' => []]);
        if (listingChartCtx && listingData.labels.length > 0) {
            new Chart(listingChartCtx, {
                type: 'line',
                 data: {
                    labels: listingData.labels,
                    datasets: [{
                        label: 'Nouvelles Annonces',
                        data: listingData.data,
                        borderColor: 'rgba(75, 192, 192, 1)', // Couleur Secondaire
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.1,
                        fill: true
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        } else if(listingChartCtx) {
             listingChartCtx.font = "16px Arial"; listingChartCtx.fillStyle = "#aaa"; listingChartCtx.textAlign = "center"; listingChartCtx.fillText("Pas de données pour cette période", listingChartCtx.canvas.width / 2, listingChartCtx.canvas.height / 2);
        }

        // Exemple pour Sales Chart (Bar)
        const salesChartCtx = document.getElementById('salesChart')?.getContext('2d');
        const salesData = @json($chartData['sales'] ?? ['labels' => [], 'data' => []]);
        if (salesChartCtx && salesData.labels.length > 0) {
            new Chart(salesChartCtx, {
                type: 'bar',
                 data: {
                    labels: salesData.labels,
                    datasets: [{
                        label: 'Volume Ventes (DH)',
                        data: salesData.data,
                        backgroundColor: 'rgba(255, 159, 64, 0.7)', // Couleur Warning
                        borderColor: 'rgba(255, 159, 64, 1)',
                        borderWidth: 1
                    }]
                },
                 options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
            });
         } else if(salesChartCtx) {
             salesChartCtx.font = "16px Arial"; salesChartCtx.fillStyle = "#aaa"; salesChartCtx.textAlign = "center"; salesChartCtx.fillText("Pas de données pour cette période", salesChartCtx.canvas.width / 2, salesChartCtx.canvas.height / 2);
         }

        // Exemple pour Category Chart (Pie/Doughnut)
        const categoryChartCtx = document.getElementById('categoryChart')?.getContext('2d');
        const categoryData = @json($chartData['categories'] ?? ['labels' => [], 'data' => []]);
        if (categoryChartCtx && categoryData.labels.length > 0) {
            new Chart(categoryChartCtx, {
                type: 'doughnut', // ou 'pie'
                 data: {
                    labels: categoryData.labels,
                    datasets: [{
                        label: 'Annonces par Catégorie',
                        data: categoryData.data,
                         // Fournir un tableau de couleurs si besoin
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.7)',
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(255, 206, 86, 0.7)',
                            'rgba(75, 192, 192, 0.7)',
                            'rgba(153, 102, 255, 0.7)',
                            'rgba(255, 159, 64, 0.7)',
                            'rgba(199, 199, 199, 0.7)'
                        ],
                        hoverOffset: 4
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        } else if(categoryChartCtx) {
             categoryChartCtx.font = "16px Arial"; categoryChartCtx.fillStyle = "#aaa"; categoryChartCtx.textAlign = "center"; categoryChartCtx.fillText("Pas de données", categoryChartCtx.canvas.width / 2, categoryChartCtx.canvas.height / 2);
         }
    });
</script>
@endsection