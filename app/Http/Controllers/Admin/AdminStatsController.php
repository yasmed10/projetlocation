<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Annonce;
use App\Models\Reservation;
use App\Models\Categorie;
use App\Models\Objet; // Importer Objet pour le prix moyen
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminStatsController extends Controller
{
    /**
     * Affiche la page des statistiques.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        // Dates par défaut et validation
        $defaultStartDate = Carbon::now()->subMonth()->startOfDay();
        $defaultEndDate = Carbon::now()->endOfDay();

        $validated = $request->validate([
            'start_date' => 'nullable|date_format:Y-m-d',
            'end_date' => 'nullable|date_format:Y-m-d|after_or_equal:start_date',
        ]);

        $startDate = isset($validated['start_date']) ? Carbon::createFromFormat('Y-m-d', $validated['start_date'])->startOfDay() : $defaultStartDate;
        $endDate = isset($validated['end_date']) ? Carbon::createFromFormat('Y-m-d', $validated['end_date'])->endOfDay() : $defaultEndDate;

        // --- Statistiques pour la période sélectionnée ($statsPeriod) ---
        $statsPeriod = [];
        $statsPeriod['uniqueVisitors'] = 0; // Placeholder
        $statsPeriod['pageViews'] = 0; // Placeholder
        $statsPeriod['newUsers'] = User::whereBetween('created_at', [$startDate, $endDate])->count();
        $statsPeriod['newListings'] = Annonce::whereBetween('created_at', [$startDate, $endDate])->count();

        // --- Données pour les graphiques ($chartData) ---
        $chartData = [];

        // Graphique Utilisateurs
        $usersData = User::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
                         ->whereBetween('created_at', [$startDate, $endDate])
                         ->groupBy('date')
                         ->orderBy('date', 'asc')
                         ->pluck('count', 'date');
        $chartData['users'] = $this->prepareChartData($usersData, $startDate, $endDate);

        // Graphique Annonces
        $listingsData = Annonce::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
                             ->whereBetween('created_at', [$startDate, $endDate])
                             ->groupBy('date')
                             ->orderBy('date', 'asc')
                             ->pluck('count', 'date');
        $chartData['listings'] = $this->prepareChartData($listingsData, $startDate, $endDate);

        // Graphique Ventes (Alternative - Calcul à la volée)
        $salesData = Reservation::join('annonces', 'reservations.annonce_id', '=', 'annonces.id')
                            ->join('objets', 'annonces.objet_id', '=', 'objets.id')
                            ->select(
                                DB::raw('DATE(reservations.created_at) as date'),
                                DB::raw('SUM( (DATEDIFF(reservations.date_fin, reservations.date_debut) + 1) * objets.prix_journalier ) as volume')
                            )
                            ->whereBetween('reservations.created_at', [$startDate, $endDate])
                            // ->whereIn('reservations.statut', ['confirmée', 'payée']) // Filtrer si pertinent
                            ->groupBy('date')
                            ->orderBy('date', 'asc')
                            ->pluck('volume', 'date');
        $chartData['sales'] = $this->prepareChartData($salesData, $startDate, $endDate);


        // Graphique Catégories
        $categoryData = Categorie::select('categories.nom as category_name', DB::raw('count(annonces.id) as count'))
                               ->join('objets', 'categories.id', '=', 'objets.categorie_id')
                               ->join('annonces', 'objets.id', '=', 'annonces.objet_id')
                               ->whereBetween('annonces.created_at', [$startDate, $endDate])
                               ->groupBy('categories.id', 'categories.nom')
                               ->orderBy('count', 'desc')
                               ->pluck('count', 'category_name');
        $chartData['categories'] = [
            'labels' => $categoryData->keys()->toArray(),
            'data' => $categoryData->values()->toArray()
        ];


        // --- Statistiques globales ($statsTotal) ---
        $statsTotal = [];
        $statsTotal['totalUsers'] = User::count();
        $statsTotal['totalListings'] = Annonce::count();
        $statsTotal['averageListingPrice'] = Objet::avg('prix_journalier');

        // Volume total des ventes (Alternative - Calcul à la volée)
        $statsTotal['totalSalesVolume'] = Reservation::join('annonces', 'reservations.annonce_id', '=', 'annonces.id')
                            ->join('objets', 'annonces.objet_id', '=', 'objets.id')
                            // ->whereIn('reservations.statut', ['confirmée', 'payée']) // Filtrer si pertinent
                            ->sum(DB::raw('(DATEDIFF(reservations.date_fin, reservations.date_debut) + 1) * objets.prix_journalier'));

        // Commission totale
        $commissionRate = config('site.commission_rate', 0);
        $statsTotal['totalCommission'] = $statsTotal['totalSalesVolume'] * ($commissionRate / 100);

        // Passer les dates utilisées à la vue pour les afficher dans le formulaire
        $currentStartDate = $startDate->format('Y-m-d');
        $currentEndDate = $endDate->format('Y-m-d');

        return view('admin.stats', compact(
            'statsPeriod',
            'chartData',
            'statsTotal',
            'currentStartDate',
            'currentEndDate'
            // defaultStartDate/EndDate ne sont plus nécessaires ici si on passe currentStartDate/EndDate
        ));
    }

    /**
     * Prépare les données pour un graphique de type ligne/barre sur une période donnée.
     * Remplit les jours manquants avec 0.
     *
     * @param \Illuminate\Support\Collection $data Pluck('count', 'date') ou Pluck('volume','date')
     * @param \Carbon\Carbon $startDate
     * @param \Carbon\Carbon $endDate
     * @return array ['labels' => [], 'data' => []]
     */
    private function prepareChartData($data, Carbon $startDate, Carbon $endDate): array
    {
        $labels = [];
        $values = [];
        $currentDate = $startDate->copy();
        $finalDate = $endDate->copy();

        while ($currentDate->lte($finalDate)) {
            $dateString = $currentDate->toDateString();
            $labels[] = $currentDate->format('d/m');
            $values[] = $data->get($dateString, 0);
            $currentDate->addDay();
        }

        return ['labels' => $labels, 'data' => $values];
    }
}