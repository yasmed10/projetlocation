<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage; // Reste utile pour supprimer CIN

class AdminUserController extends Controller
{
    /**
     * Affiche la liste des utilisateurs.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filtre Recherche
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nom', 'like', "%{$searchTerm}%")
                  ->orWhere('prenom', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        // TODO: Ajouter filtres RÃ´le, Statut CIN (basÃ© sur role/fichiers)

        $query->latest('created_at');
        $users = $query->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Approuve un utilisateur (Action Symbolique ou LiÃ©e Ã  Autre Logique).
     */
    public function approve(User $user)
    {
        // Sans colonne dÃ©diÃ©e, cette action est limitÃ©e.
        // Option 1: Juste un log et un message.
        Log::info("Action 'Approuver' cliquÃ©e pour l'utilisateur ID: {$user->id} (pas de changement de statut direct).");
        // Option 2: Si vous rÃ©activez email_verified_at dans la migration utilisateurs:
        // if (!$user->hasVerifiedEmail()) { $user->markEmailAsVerified(); }
        // Option 3: Si vous avez un rÃ´le 'pending_approval'
        // if ($user->role === 'pending_approval') { $user->update(['role' => 'client']); }

        return redirect()->back()->with('info', "Action 'Approuver' enregistrÃ©e pour {$user->nom}. Aucune colonne de statut direct Ã  mettre Ã  jour.");
    }

    /**
     * Rejette un utilisateur (Suppression - Attention!).
     */
    public function reject(User $user)
    {
        // ATTENTION: Ceci supprime l'utilisateur et potentiellement ses donnÃ©es liÃ©es.
        // Envisagez Soft Deletes sur le modÃ¨le User si vous voulez pouvoir restaurer.
        $userName = $user->full_name;
        try {
            $user->delete();
            Log::info("Utilisateur ID: {$user->id} ($userName) rejetÃ© (supprimÃ©).");
            return redirect()->route('admin.users.index')->with('success', "Utilisateur {$userName} rejetÃ© et supprimÃ©.");
        } catch (\Exception $e) {
            Log::error("Erreur lors de la suppression de l'utilisateur ID: {$user->id} - " . $e->getMessage());
            return redirect()->back()->with('error', "Impossible de rejeter/supprimer l'utilisateur. VÃ©rifier les dÃ©pendances.");
        }
    }

    // !! MÃ‰THODES block() ET unblock() RETIRÃ‰ES !!

    /**
     * Affiche la page/interface de vÃ©rification de la CIN.
     */
    public function showVerifyCinForm(User $user)
    {
        if (empty($user->cin_recto) || empty($user->cin_verso)) {
             return redirect()->route('admin.users.index')->with('error', "Les images CIN recto et verso doivent Ãªtre prÃ©sentes pour la vÃ©rification.");
        }
        // Si l'utilisateur est dÃ©jÃ  propriÃ©taire, on considÃ¨re vÃ©rifiÃ©
         if ($user->isCinConsideredVerified()) {
             return redirect()->route('admin.users.index')->with('info', "La CIN de {$user->nom} est dÃ©jÃ  considÃ©rÃ©e comme vÃ©rifiÃ©e (rÃ´le PropriÃ©taire).");
         }
        // CrÃ©er la vue: resources/views/admin/users/verify_cin.blade.php
        return view('admin.users.verify_cin', compact('user'));
    }

    /**
     * Approuve la CIN d'un utilisateur (Change le rÃ´le en 'proprietaire').
     */
    public function approveCin(User $user) {
        if ($user->role !== 'proprietaire') {
            // Mettre Ã  jour le rÃ´le pour indiquer que la CIN est approuvÃ©e
            $user->update(['role' => 'proprietaire']);
            Log::info("CIN pour Utilisateur ID: {$user->id} approuvÃ©e (rÃ´le mis Ã  'proprietaire').");
            // TODO: Notifier l'utilisateur
            return redirect()->route('admin.users.index')->with('success', "CIN de {$user->nom} approuvÃ©e (rÃ´le mis Ã  jour).");
        }
        return redirect()->route('admin.users.index')->with('info', "L'utilisateur {$user->nom} a dÃ©jÃ  le rÃ´le PropriÃ©taire.");
    }

    /**
     * Rejette la CIN d'un utilisateur (Supprime les fichiers CIN).
     * Pas de stockage de raison.
     */
    public function rejectCin(Request $request, User $user) { // Request n'est plus vraiment utile ici
         // On ne peut pas stocker la raison.
         // L'action la plus logique est de supprimer les fichiers pour que l'utilisateur doive les re-uploader.
         $deleted = false;
         if ($user->cin_recto) {
             Storage::disk('public')->delete($user->cin_recto);
             $deleted = true;
         }
         if ($user->cin_verso) {
             Storage::disk('public')->delete($user->cin_verso);
             $deleted = true;
         }

         if ($deleted) {
             $user->update([
                 'cin_recto' => null,
                 'cin_verso' => null,
                 // S'assurer que le rÃ´le n'est pas proprietaire si on rejette
                 'role' => $user->role === 'proprietaire' ? 'client' : $user->role
             ]);
             Log::info("CIN pour Utilisateur ID: {$user->id} rejetÃ©e (fichiers supprimÃ©s, rÃ´le vÃ©rifiÃ©).");
            // TODO: Notifier l'utilisateur qu'il doit re-soumettre ses documents
             return redirect()->route('admin.users.index')->with('success', "CIN de {$user->nom} rejetÃ©e (fichiers supprimÃ©s). L'utilisateur devra les soumettre Ã  nouveau.");
         } else {
              Log::warning("Tentative de rejet CIN pour Utilisateur ID: {$user->id} mais aucun fichier Ã  supprimer.");
              return redirect()->route('admin.users.index')->with('info', "Aucun fichier CIN Ã  rejeter pour {$user->nom}.");
         }
    }
}