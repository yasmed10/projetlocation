<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Annonce; // Modèle Annonce
use App\Models\Categorie; // Modèle Categorie pour filtre
use Illuminate\Support\Facades\Log; // Pour logging

class AdminListingController extends Controller
{
    /**
     * Affiche la liste des annonces pour l'admin avec filtres et pagination.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        Log::info('AdminListingController@index called'); // Log pour vérifier l'appel

        $query = Annonce::query()
                 ->with(['objet.categorie', 'user']); // Eager load relations pour performance

        // Filtre par Statut
        if ($request->filled('status') && $request->input('status') !== 'all') {
             Log::info('Filtering by status: ' . $request->input('status'));
            $query->where('statut', $request->input('status'));
        }

        // Filtre par Catégorie (via l'objet lié)
        if ($request->filled('category') && $request->input('category') !== 'all') {
             Log::info('Filtering by category ID: ' . $request->input('category'));
            $categoryId = $request->input('category');
            $query->whereHas('objet', function ($q) use ($categoryId) {
                $q->where('categorie_id', $categoryId);
            });
        }

        // Filtre par Vendeur (ID ou Nom/Email)
        if ($request->filled('seller')) {
             Log::info('Filtering by seller term: ' . $request->input('seller'));
             $sellerTerm = $request->input('seller');
             $query->whereHas('user', function ($q) use ($sellerTerm) {
                 // Vérifie si c'est un ID numérique
                 if (is_numeric($sellerTerm)) {
                    $q->where('id', $sellerTerm);
                 } else {
                     // Recherche par nom/prénom/email si ce n'est pas un ID
                     $q->where('nom', 'like', "%{$sellerTerm}%")
                       ->orWhere('prenom', 'like', "%{$sellerTerm}%")
                       ->orWhere('email', 'like', "%{$sellerTerm}%");
                 }
             });
        }

        // Recherche par Titre (nom de l'objet) ou Description
         if ($request->filled('search')) {
              Log::info('Searching for term: ' . $request->input('search'));
             $searchTerm = $request->input('search');
             $query->where(function ($q) use ($searchTerm) {
                 $q->whereHas('objet', function ($subQ) use ($searchTerm) {
                     $subQ->where('nom', 'like', "%{$searchTerm}%")
                          ->orWhere('description', 'like', "%{$searchTerm}%");
                 })
                 ->orWhere('adresse', 'like', "%{$searchTerm}%"); // Ajout recherche adresse annonce
             });
         }

        // Ordonner (par date de création/publication la plus récente par défaut)
        $query->latest('date_publication'); // ou 'created_at'

        // Paginer
        $listings = $query->paginate(15)->withQueryString(); // Garder les filtres dans la pagination

        // Récupérer les catégories pour le dropdown du filtre
        $categories = Categorie::orderBy('nom')->get();

        Log::info('Returning admin.annonces view with ' . $listings->count() . ' listings.');
        // Passer les données à la vue
        return view('admin.annonces', compact('listings', 'categories'));
    }

    // --- PLACEHOLDER pour les autres méthodes ---

    public function show(Annonce $listing) {
        // TODO: Créer une vue admin/annonces/show.blade.php et implémenter la logique
        Log::warning('AdminListingController@show not implemented yet for listing ID: ' . $listing->id);
        return redirect()->route('admin.listings.index')->with('info', 'Fonction Show Annonce (Admin) non implémentée.');
    }

    public function edit(Annonce $listing) {
        // TODO: Créer une vue admin/annonces/edit.blade.php et implémenter la logique
        // Récupérer les catégories, etc. pour les dropdowns
        $categories = Categorie::orderBy('nom')->get();
        Log::warning('AdminListingController@edit not implemented yet for listing ID: ' . $listing->id);
        return redirect()->route('admin.listings.index')->with('info', 'Fonction Edit Annonce (Admin) non implémentée.');
        // return view('admin.annonces.edit', compact('listing', 'categories'));
    }

    public function update(Request $request, Annonce $listing) {
        // TODO: Implémenter la logique de validation et de mise à jour de l'annonce et de l'objet lié
        // $validatedDataAnnonce = $request->validate([...]);
        // $validatedDataObjet = $request->validate([...]);
        // $listing->update($validatedDataAnnonce);
        // $listing->objet->update($validatedDataObjet);
        Log::warning('AdminListingController@update not implemented yet for listing ID: ' . $listing->id);
        return redirect()->route('admin.listings.index')->with('info', 'Fonction Update Annonce (Admin) non implémentée.');
    }

    public function destroy(Annonce $listing) {
        // TODO: Implémenter la logique de suppression (vérifier si réservations existent?)
        // try {
        //     $listing->delete();
        //     Log::info('Listing ID: ' . $listing->id . ' deleted successfully.');
        //     return redirect()->route('admin.listings.index')->with('success', 'Annonce supprimée avec succès.');
        // } catch (\Exception $e) {
        //     Log::error('Error deleting listing ID: ' . $listing->id . ' - ' . $e->getMessage());
        //     return redirect()->route('admin.listings.index')->with('error', 'Impossible de supprimer l\'annonce.');
        // }
        Log::warning('AdminListingController@destroy not implemented yet for listing ID: ' . $listing->id);
        return redirect()->route('admin.listings.index')->with('info', 'Fonction Delete Annonce (Admin) non implémentée.');
    }

    public function approve(Annonce $listing) {
        // TODO: Implémenter la logique d'approbation
        // if ($listing->statut === 'pending') {
        //     $listing->update(['statut' => 'active']);
        //     Log::info('Listing ID: ' . $listing->id . ' approved.');
        //     return redirect()->back()->with('success', 'Annonce approuvée.');
        // }
        Log::warning('AdminListingController@approve not implemented yet for listing ID: ' . $listing->id);
        return redirect()->back()->with('info', 'Fonction Approve Annonce (Admin) non implémentée.');
    }

    public function reject(Request $request, Annonce $listing) { // Ajouter Request si on veut une raison
         // TODO: Implémenter la logique de rejet
         // $reason = $request->input('reason', 'Raison non spécifiée'); // Si un motif est fourni
         // if ($listing->statut === 'pending') {
         //      $listing->update(['statut' => 'rejected'/*, 'rejection_reason' => $reason */]); // Ajouter colonne si besoin
         //      Log::info('Listing ID: ' . $listing->id . ' rejected.');
         //      return redirect()->back()->with('success', 'Annonce rejetée.');
         // }
        Log::warning('AdminListingController@reject not implemented yet for listing ID: ' . $listing->id);
        return redirect()->back()->with('info', 'Fonction Reject Annonce (Admin) non implémentée.');
    }

     public function archive(Annonce $listing) {
         // TODO: Implémenter la logique d'archivage
         // if ($listing->statut === 'active') {
         //     $listing->update(['statut' => 'archived']);
         //     Log::info('Listing ID: ' . $listing->id . ' archived.');
         //     return redirect()->back()->with('success', 'Annonce archivée.');
         // }
        Log::warning('AdminListingController@archive not implemented yet for listing ID: ' . $listing->id);
        return redirect()->back()->with('info', 'Fonction Archive Annonce (Admin) non implémentée.');
    }

     public function getRejectionDetails(Annonce $listing) {
        // TODO: Récupérer la raison du rejet si elle est stockée dans une colonne 'rejection_reason'
        // return response()->json(['reason' => $listing->rejection_reason ?? 'Aucune raison spécifiée']);
        Log::warning('AdminListingController@getRejectionDetails not implemented yet for listing ID: ' . $listing->id);
        return response()->json(['reason' => 'Fonction Détails Rejet (Admin) non implémentée.'], 404); // Retourner 404 si non implémenté
    }
}