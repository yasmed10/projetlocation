<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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

        // Ne montrer que les utilisateurs non archivés par défaut
        if (!$request->filled('show_archived')) {
            $query->whereNull('archived_at');
        }

        $query->latest('created_at');
        $users = $query->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Archive un utilisateur au lieu de le supprimer
     */
    public function archive(User $user)
    {
        try {
            $user->update(['archived_at' => now()]);
            Log::info("Utilisateur ID: {$user->id} ({$user->full_name}) archivé.");
            return redirect()->route('admin.users.index')->with('success', "Utilisateur {$user->full_name} a été archivé.");
        } catch (\Exception $e) {
            Log::error("Erreur lors de l'archivage de l'utilisateur ID: {$user->id} - " . $e->getMessage());
            return redirect()->back()->with('error', "Impossible d'archiver l'utilisateur.");
        }
    }

    /**
     * Restaure un utilisateur archivé
     */
    public function restore(User $user)
    {
        try {
            $user->update(['archived_at' => null]);
            Log::info("Utilisateur ID: {$user->id} ({$user->full_name}) restauré.");
            return redirect()->route('admin.users.index')->with('success', "Utilisateur {$user->full_name} a été restauré.");
        } catch (\Exception $e) {
            Log::error("Erreur lors de la restauration de l'utilisateur ID: {$user->id} - " . $e->getMessage());
            return redirect()->back()->with('error', "Impossible de restaurer l'utilisateur.");
        }
    }

    /**
     * Affiche la page/interface de vérification de la CIN.
     */
    public function showVerifyCinForm(User $user)
    {
        if (empty($user->cin_recto) || empty($user->cin_verso)) {
             return redirect()->route('admin.users.index')->with('error', "Les images CIN recto et verso doivent être présentes pour la vérification.");
        }
        if ($user->isCinConsideredVerified()) {
             return redirect()->route('admin.users.index')->with('info', "La CIN de {$user->nom} est déjà considérée comme vérifiée (rôle Propriétaire).");
        }
        return view('admin.users.verify_cin', compact('user'));
    }

    /**
     * Approuve la CIN d'un utilisateur (Change le rôle en 'proprietaire').
     */
    public function approveCin(User $user) {
        if ($user->role !== 'proprietaire') {
            $user->update(['role' => 'proprietaire']);
            Log::info("CIN pour Utilisateur ID: {$user->id} approuvée (rôle mis à 'proprietaire').");
            return redirect()->route('admin.users.index')->with('success', "CIN de {$user->nom} approuvée (rôle mis à jour).");
        }
        return redirect()->route('admin.users.index')->with('info', "L'utilisateur {$user->nom} a déjà le rôle Propriétaire.");
    }

    /**
     * Rejette la CIN d'un utilisateur (Supprime les fichiers CIN).
     * Pas de stockage de raison.
     */
    public function rejectCin(Request $request, User $user) {
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
                'role' => $user->role === 'proprietaire' ? 'client' : $user->role
            ]);
            Log::info("CIN pour Utilisateur ID: {$user->id} rejetée (fichiers supprimés, rôle vérifié).");
            return redirect()->route('admin.users.index')->with('success', "CIN de {$user->nom} rejetée (fichiers supprimés). L'utilisateur devra les soumettre à nouveau.");
        } else {
            Log::warning("Tentative de rejet CIN pour Utilisateur ID: {$user->id} mais aucun fichier à supprimer.");
            return redirect()->route('admin.users.index')->with('info', "Aucun fichier CIN à rejeter pour {$user->nom}.");
        }
    }
}