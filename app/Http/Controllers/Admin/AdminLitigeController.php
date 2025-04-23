<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reclamation; // Utiliser le modèle Reclamation pour les litiges
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminLitigeController extends Controller
{
    /**
     * Affiche la liste des litiges (réclamations).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        // TODO: Ajouter logique de filtrage (par statut, date, etc.)
        $query = Reclamation::query()->with(['utilisateur', 'reservation.annonce']); // Charger relations

        // Exemple filtre statut
         if ($request->filled('status') && $request->input('status') !== 'all') {
             $query->where('statut', $request->input('status'));
         }

        // TODO: Trier les résultats
        $query->latest('created_at');

        // Le nom de variable $litiges est utilisé pour correspondre à la vue potentielle
        $litiges = $query->paginate(15)->withQueryString();

        // TODO: Créer la vue resources/views/admin/litiges/index.blade.php
        return view('admin.litiges.index', compact('litiges'));

        // Placeholder si la vue n'existe pas encore
         //return redirect()->route('admin.dashboard')->with('info', 'La section Litiges (index) n\'est pas encore implémentée.');
    }

    /**
     * Affiche les détails d'un litige spécifique.
     * NOTE: La route utilise {litige}, mais nous type-hintons Reclamation $litige.
     *
     * @param  \App\Models\Reclamation  $litige
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show(Reclamation $litige) // Laravel fera la liaison modèle-route
    {
        // Charger les relations si besoin
        $litige->loadMissing(['utilisateur', 'reservation.client', 'reservation.annonce.proprietaire']);

        // TODO: Créer la vue resources/views/admin/litiges/show.blade.php
        // return view('admin.litiges.show', compact('litige'));

        // Placeholder si la vue n'existe pas encore
        return redirect()->route('admin.litiges.index')->with('info', 'La vue détail Litige (ID: '.$litige->id.') n\'est pas encore implémentée.');
    }

    /**
     * Marque un litige comme résolu.
     * NOTE: La route utilise {litige}, mais nous type-hintons Reclamation $litige.
     *
     * @param  \App\Models\Reclamation  $litige
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resolve(Reclamation $litige) // Laravel fera la liaison modèle-route
    {
        // TODO: Ajouter logique métier pour la résolution
        // - Vérifier si la résolution est possible
        // - Mettre à jour le statut
        // - Ajouter une note/commentaire de résolution ?
        // - Notifier les parties ?

        try {
            // Exemple simple : mettre à jour le statut
            if ($litige->statut !== 'résolu') { // Vérifier si pas déjà résolu
                $litige->update(['statut' => 'résolu']); // Adapter le nom du statut si besoin
                Log::info("Litige (Réclamation) ID: {$litige->id} marqué comme résolu par l'admin.");
                // TODO: Ajouter notification/événement

                return redirect()->route('admin.litiges.index')->with('success', 'Litige marqué comme résolu.');
            } else {
                 return redirect()->route('admin.litiges.index')->with('info', 'Ce litige est déjà marqué comme résolu.');
            }

        } catch (\Exception $e) {
             Log::error("Erreur lors de la résolution du litige ID: {$litige->id} - " . $e->getMessage());
             return redirect()->route('admin.litiges.index')->with('error', 'Une erreur est survenue lors de la résolution du litige.');
        }
    }
}