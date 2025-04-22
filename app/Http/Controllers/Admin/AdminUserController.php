<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log; // Ajouter pour logging éventuel
use Illuminate\Support\Facades\Storage; // Pour suppression CIN

class AdminUserController extends Controller
{
    /**
     * Affiche la liste des utilisateurs avec filtres et pagination.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Recherche par nom, prénom ou email
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nom', 'like', "%{$searchTerm}%")
                  ->orWhere('prenom', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        // Ordonner
        $query->latest('created_at');

        // Paginer
        $users = $query->paginate(15)->withQueryString();

        // NOTE: Les filtres 'status' et 'cin_status' ont été retirés de la vue
        // car les colonnes correspondantes n'existent pas.

        return view('admin.users.index', compact('users'));
    }

    /**
     * Approuve un utilisateur (PLACEHOLDER LOGIC).
     * La vraie logique dépendra de ce que "approuvé" signifie (email vérifié, rôle, etc.).
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(User $user)
    {
        // TODO: Implémenter la vraie logique d'approbation.
        // Exemple Placeholder: Si l'utilisateur a un rôle spécifique 'pending_approval', le changer.
        // if($user->role === 'pending_approval') {
        //    $user->role = 'client'; // Ou 'proprietaire' selon le cas
        //    $user->save();
        //    return redirect()->back()->with('success', "Utilisateur {$user->nom} approuvé.");
        // }
        // Autre exemple: Marquer email comme vérifié (si MustVerifyEmail est utilisé)
        // if (!$user->hasVerifiedEmail()) {
        //     $user->markEmailAsVerified();
        //     return redirect()->back()->with('success', "Email de {$user->nom} marqué comme vérifié.");
        // }

        Log::info("Tentative d'approbation (logique placeholder) pour l'utilisateur ID: {$user->id}");
        return redirect()->back()->with('info', "Action 'Approuver' pour {$user->nom} : Logique à définir.");
    }

    /**
     * Rejette un utilisateur (PLACEHOLDER LOGIC).
     * La vraie logique dépendra de ce que "rejeté" signifie (suppression, rôle, etc.).
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(User $user)
    {
        // TODO: Implémenter la vraie logique de rejet.
        // Exemple Placeholder: Assigner un rôle 'rejected' (à ajouter aux rôles possibles).
        // $user->role = 'rejected';
        // $user->save();
        // return redirect()->back()->with('success', "Utilisateur {$user->nom} marqué comme rejeté.");
        // Autre exemple: Supprimer l'utilisateur (ATTENTION: irréversible)
        // $user->delete();
        // return redirect()->back()->with('success', "Utilisateur {$user->nom} supprimé.");

        Log::info("Tentative de rejet (logique placeholder) pour l'utilisateur ID: {$user->id}");
        return redirect()->back()->with('info', "Action 'Rejeter' pour {$user->nom} : Logique à définir.");
    }

    /**
     * Bloque un utilisateur (PLACEHOLDER LOGIC).
     * La vraie logique nécessitera probablement une colonne 'blocked_at'.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function block(User $user)
    {
        // TODO: Implémenter la vraie logique de blocage (ex: ajout colonne 'blocked_at').
        // Exemple: $user->update(['blocked_at' => now()]); -> nécessite migration
        // return redirect()->back()->with('success', "Utilisateur {$user->nom} bloqué.");

        // Placeholder: Changer le rôle (si un rôle 'blocked' existe)
        // $user->role = 'blocked';
        // $user->save();
        // return redirect()->back()->with('success', "Utilisateur {$user->nom} marqué comme bloqué (via rôle).");

        Log::info("Tentative de blocage (logique placeholder) pour l'utilisateur ID: {$user->id}");
        return redirect()->back()->with('info', "Action 'Bloquer' pour {$user->nom} : Logique à définir.");
    }

    /**
     * Débloque un utilisateur (PLACEHOLDER LOGIC).
     * La vraie logique nécessitera probablement une colonne 'blocked_at'.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function unblock(User $user)
    {
        // TODO: Implémenter la vraie logique de déblocage (ex: mettre 'blocked_at' à null).
        // Exemple: $user->update(['blocked_at' => null]); -> nécessite migration
        // return redirect()->back()->with('success', "Utilisateur {$user->nom} débloqué.");

        // Placeholder: Remettre un rôle actif si l'utilisateur était bloqué via rôle
        // if($user->role === 'blocked') {
        //    $user->role = 'client'; // Ou 'proprietaire'
        //    $user->save();
        //    return redirect()->back()->with('success', "Utilisateur {$user->nom} débloqué (via rôle).");
        // }

        Log::info("Tentative de déblocage (logique placeholder) pour l'utilisateur ID: {$user->id}");
        return redirect()->back()->with('info', "Action 'Débloquer' pour {$user->nom} : Logique à définir.");
    }

    /**
     * Affiche la page/interface de vérification de la CIN.
     * (La logique de base pour vérifier si les fichiers existent est OK)
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showVerifyCinForm(User $user)
    {
        if (!$user->cin_recto || !$user->cin_verso) {
             return redirect()->route('admin.users.index')->with('error', "Les images CIN recto et verso doivent être présentes pour la vérification.");
        }

        // TODO: Créer la vue 'admin.users.verify_cin'
        // Cette vue devrait afficher $user->nom, $user->prenom, $user->email
        // et les images $user->cin_recto, $user->cin_verso (via Storage::url())
        // et contenir deux boutons/formulaires postant vers admin.users.approve_cin et admin.users.reject_cin

        // return view('admin.users.verify_cin', compact('user'));

        // Placeholder actuel
         Log::info("Affichage formulaire vérification CIN (page non implémentée) pour user ID: {$user->id}");
         return redirect()->route('admin.users.index')->with('info', "La page de vérification CIN pour {$user->nom} n'est pas encore implémentée.");
    }

    /**
     * Approuve la CIN d'un utilisateur (PLACEHOLDER LOGIC).
     * Nécessite probablement une colonne 'cin_verified_at'.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approveCin(User $user) {
        // TODO: Implémenter la vraie logique (ex: ajout colonne 'cin_verified_at').
        // Exemple: $user->update(['cin_verified_at' => now()]); -> nécessite migration
        // return redirect()->route('admin.users.index')->with('success', "CIN de {$user->nom} approuvée.");

        Log::info("Tentative d'approbation CIN (logique placeholder) pour user ID: {$user->id}");
        return redirect()->route('admin.users.index')->with('info', "Action 'Approuver CIN' pour {$user->nom} : Logique à définir.");
    }

    /**
     * Rejette la CIN d'un utilisateur (PLACEHOLDER LOGIC).
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function rejectCin(User $user) {
         // TODO: Implémenter la vraie logique.
         // Mettre à jour un statut? Supprimer les images? Mettre cin_verified_at à null?
         // Exemple: $user->update(['cin_verified_at' => null, 'cin_rejection_reason' => 'Motif...']); -> nécessite migrations
         // Optionnel: Supprimer les fichiers
         // if ($user->cin_recto) Storage::disk('public')->delete($user->cin_recto);
         // if ($user->cin_verso) Storage::disk('public')->delete($user->cin_verso);
         // $user->update(['cin_recto' => null, 'cin_verso' => null]);
         // return redirect()->route('admin.users.index')->with('success', "CIN de {$user->nom} rejetée.");

        Log::info("Tentative de rejet CIN (logique placeholder) pour user ID: {$user->id}");
        return redirect()->route('admin.users.index')->with('info', "Action 'Rejeter CIN' pour {$user->nom} : Logique à définir.");
    }
}