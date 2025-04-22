<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// Correction: Utiliser le modèle User standard (configuré pour la table utilisateurs)
use App\Models\User;
use App\Models\Annonce;
use App\Models\Reservation;
// Le modèle ActivityLog n'existe pas dans les fichiers fournis
// use App\Models\ActivityLog;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    /**
     * Affiche le tableau de bord administrateur.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        // Récupérer les statistiques rapides
        $totalUsers = User::count(); // OK, User pointe vers utilisateurs

        // La colonne 'status' n'existe pas pour les utilisateurs - BIEN COMMENTÉ
        // $pendingUsers = User::where('status', 'pending')->count();
        // Alternative possible (si logique basée sur role ou autre champ):
        // $pendingUsers = User::whereNull('email_verified_at')->count(); // Exemple si email non vérifié = pending

        // Statut 'active' pour les annonces (si pertinent)
        // $activeListings = Annonce::where('statut', 'active')->count(); // Modèle Annonce a 'statut'

        // Ventes/Réservations des 7 derniers jours
        // Assurez-vous que 'created_at' existe sur Reservation (oui, via timestamps())
        $recentSales = Reservation::where('created_at', '>=', Carbon::now()->subDays(7))->count();

        // Activités récentes (modèle ActivityLog manquant)
        // $recentActivities = ActivityLog::latest()->take(5)->get();

        // Passer les données à la vue
        return view('admin.dashboard', compact(
            'totalUsers',
            // 'pendingUsers', // Décommenter si une logique alternative est implémentée
            // 'activeListings', // Décommenter si besoin
            'recentSales'
            // 'recentActivities' // Laisser commenté car modèle manquant
        ));
    }
}