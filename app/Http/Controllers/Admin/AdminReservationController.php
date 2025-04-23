<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation; // Assurez-vous que le modèle Reservation existe et est correctement importé
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminReservationController extends Controller
{
    /**
     * Affiche la liste des réservations.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        // TODO: Ajouter la logique de filtrage si nécessaire (par statut, date, client, etc.)
        $query = Reservation::query()->with(['client', 'annonce.objet']); // Charger les relations utiles

        // Exemple de filtre simple par statut
        if ($request->filled('status') && $request->input('status') !== 'all') {
             $query->where('statut', $request->input('status'));
        }

        // TODO: Trier les résultats (par ex. par date de création la plus récente)
        $query->latest('created_at');

        $reservations = $query->paginate(15)->withQueryString(); // Paginer les résultats

        // --- CORRECTION ICI ---
        // Décommentez la ligne suivante pour retourner la vue que vous avez créée
        return view('admin.reservations.index', compact('reservations'));

        // Commentez ou supprimez la ligne de redirection placeholder
        // return redirect()->route('admin.dashboard')->with('info', 'La section Réservations (index) n\'est pas encore implémentée.');
        // --- FIN CORRECTION ---
    }

    /**
     * Affiche les détails d'une réservation spécifique.
     *
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show(Reservation $reservation)
    {
        // Charger les relations si nécessaire (déjà fait dans index mais peut être utile ici aussi)
        $reservation->loadMissing(['client', 'annonce.objet', 'annonce.proprietaire', 'reclamations', 'notifications']);

        // --- CORRECTION ICI ---
        // Décommentez la ligne suivante pour retourner la vue de détail
        return view('admin.reservations.show', compact('reservation'));

        // Commentez ou supprimez la ligne de redirection placeholder
        // return redirect()->route('admin.reservations.index')->with('info', 'La vue détail Réservation (ID: '.$reservation->id.') n\'est pas encore implémentée.');
        // --- FIN CORRECTION ---
    }

    /**
     * Annule une réservation.
     *
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel(Reservation $reservation)
    {
        // TODO: Ajouter la logique métier pour l'annulation
        // - Vérifier si l'annulation est possible (selon statut actuel, règles métier)
        // - Mettre à jour le statut de la réservation
        // - Envoyer des notifications (client, propriétaire) ?
        // - Gérer les remboursements si applicable ?

        try {
            // Exemple simple : mettre à jour le statut
            if (in_array($reservation->statut, ['confirmée', 'payée', 'en attente'])) { // Adapter les statuts possibles
                $reservation->update(['statut' => 'annulée_admin']); // Utiliser un statut spécifique
                Log::info("Réservation ID: {$reservation->id} annulée par l'admin.");
                // TODO: Ajouter notification/événement si nécessaire

                return redirect()->route('admin.reservations.index')->with('success', 'Réservation annulée avec succès.');
            } else {
                return redirect()->route('admin.reservations.index')->with('warning', 'Cette réservation ne peut pas être annulée (statut: '.$reservation->statut.').');
            }

        } catch (\Exception $e) {
            Log::error("Erreur lors de l'annulation de la réservation ID: {$reservation->id} - " . $e->getMessage());
            return redirect()->route('admin.reservations.index')->with('error', 'Une erreur est survenue lors de l\'annulation de la réservation.');
        }
    }
}