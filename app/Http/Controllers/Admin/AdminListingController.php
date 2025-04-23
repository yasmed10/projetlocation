<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Annonce;
use App\Models\Categorie;
use Illuminate\Support\Facades\Log;

class AdminListingController extends Controller
{
    // index() (inchangÃ©)
    public function index(Request $request) { /* ... mÃªme code que prÃ©cÃ©demment ... */
        $query = Annonce::query()->with(['objet.categorie', 'user']);
        // Filtres...
        if ($request->filled('status') && $request->input('status') !== 'all') { $query->where('statut', $request->input('status')); }
        if ($request->filled('category') && $request->input('category') !== 'all') { $categoryId = $request->input('category'); $query->whereHas('objet', fn($q) => $q->where('categorie_id', $categoryId)); }
        if ($request->filled('seller')) { $sellerTerm = $request->input('seller'); $query->whereHas('user', function ($q) use ($sellerTerm) { is_numeric($sellerTerm) ? $q->where('id', $sellerTerm) : $q->where('nom', 'like', "%{$sellerTerm}%")->orWhere('prenom', 'like', "%{$sellerTerm}%")->orWhere('email', 'like', "%{$sellerTerm}%"); }); }
        if ($request->filled('search')) { $searchTerm = $request->input('search'); $query->where(function ($q) use ($searchTerm) { $q->whereHas('objet', fn ($subQ) => $subQ->where('nom', 'like', "%{$searchTerm}%")->orWhere('description', 'like', "%{$searchTerm}%") )->orWhere('adresse', 'like', "%{$searchTerm}%"); }); }
        // Fin Filtres
        $query->latest('date_publication');
        $listings = $query->paginate(15)->withQueryString();
        $categories = Categorie::orderBy('nom')->get();
        return view('admin.annonces', compact('listings', 'categories'));
     }

    // show(), edit(), update(), destroy() (inchangÃ©s - restent avec TODOs ou implÃ©mentation basique)
    public function show(Annonce $listing) { return redirect()->route('admin.listings.index')->with('info', 'Fonction Voir DÃ©tail Annonce (Admin) non implÃ©mentÃ©e. ID: ' . $listing->id); }
    public function edit(Annonce $listing) { return redirect()->route('admin.listings.index')->with('info', 'Fonction Modifier Annonce (Admin) non implÃ©mentÃ©e. ID: ' . $listing->id); }
    public function update(Request $request, Annonce $listing) { return redirect()->route('admin.listings.index')->with('info', 'Fonction Mettre Ã  Jour Annonce (Admin) non implÃ©mentÃ©e.'); }
    public function destroy(Annonce $listing) {
        $title = $listing->title;
        try {
            $listing->delete(); Log::info("Annonce ID: {$listing->id} ('{$title}') supprimÃ©e par l'admin.");
            return redirect()->route('admin.listings.index')->with('success', "Annonce '{$title}' supprimÃ©e.");
        } catch (\Exception $e) { Log::error("Erreur suppression annonce ID: {$listing->id} - " . $e->getMessage());
            return redirect()->route('admin.listings.index')->with('error', 'Impossible de supprimer l\'annonce.');
        }
     }
    // --- FIN CRUD ---


    /**
     * Approuve une annonce en attente.
     */
    public function approve(Annonce $listing)
    {
        if ($listing->statut === 'pending') {
            // Pas besoin de toucher Ã  rejection_reason car elle n'existe pas
            $listing->update(['statut' => 'active']);
            Log::info('Listing ID: ' . $listing->id . ' approved.');
            // TODO: Notifier le propriÃ©taire
            return redirect()->back()->with('success', 'Annonce approuvÃ©e.');
        }
        return redirect()->back()->with('info', 'L\'annonce n\'est pas en attente d\'approbation.');
    }

    /**
     * Rejette une annonce en attente.
     * Pas de stockage de raison.
     */
    public function reject(Request $request, Annonce $listing) // Request n'est plus utile ici
    {
         // Pas de raison Ã  valider ou stocker
         if ($listing->statut === 'pending') {
              $listing->update(['statut' => 'rejected']);
              Log::info('Listing ID: ' . $listing->id . ' rejected (no reason stored).');
              // TODO: Notifier le propriÃ©taire (lui dire de contacter le support pour la raison?)
              return redirect()->back()->with('success', 'Annonce rejetÃ©e.');
         }
        return redirect()->back()->with('info', 'L\'annonce n\'est pas en attente d\'approbation.');
    }

    /**
     * Archive une annonce.
     */
     public function archive(Annonce $listing)
     {
         if (in_array($listing->statut, ['active', 'pending', 'rejected'])) {
             $listing->update(['statut' => 'archived']);
             Log::info('Listing ID: ' . $listing->id . ' archived.');
             return redirect()->back()->with('success', 'Annonce archivÃ©e.');
         }
         return redirect()->back()->with('info', 'Cette annonce ne peut pas Ãªtre archivÃ©e depuis son statut actuel.');
    }

     /**
      * RÃ©active une annonce archivÃ©e (la remet en 'pending').
      */
     public function unarchive(Annonce $listing)
     {
         if ($listing->statut === 'archived') {
             // Pas besoin de toucher Ã  rejection_reason
             $listing->update(['statut' => 'pending']);
             Log::info('Listing ID: ' . $listing->id . ' unarchived (set to pending).');
             return redirect()->back()->with('success', 'Annonce dÃ©sarchivÃ©e et remise en attente d\'approbation.');
         }
         return redirect()->back()->with('info', 'Cette annonce n\'est pas archivÃ©e.');
     }

     // !! MÃ‰THODE getRejectionDetails() RETIRÃ‰E car pas de colonne pour stocker la raison !!

}