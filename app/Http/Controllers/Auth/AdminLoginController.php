<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AdminLoginController extends Controller
{
    /**
     * Appliquer le middleware 'guest' pour le guard 'admin'.
     */
    public function __construct()
    {
        // Redirige les admins déjà connectés vers le dashboard admin
        $this->middleware('guest:admin')->except('logout');
    }

    /**
     * Affiche le formulaire de connexion admin.
     */
    public function showLoginForm()
    {
        // Créer la vue : resources/views/auth/admin-login.blade.php
        return view('auth.admin-login');
    }

    /**
     * Gère la tentative de connexion admin.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'mot_pass' => ['required'], // Doit correspondre au name="" du formulaire
        ]);

        // Tenter la connexion via le guard 'admin'
        // `attempt` utilise 'password' mais le modèle Admin::getAuthPassword()
        // le fait correspondre à 'mot_pass'
        if (Auth::guard('admin')->attempt(['email' => $credentials['email'], 'password' => $credentials['mot_pass']], $request->filled('remember'))) {
            $request->session()->regenerate();

            // --- MODIFICATION ICI ---
            // Redirige TOUJOURS vers le dashboard admin après succès
            // au lieu de ->intended(...)
            return redirect()->route('admin.dashboard');
            // --- FIN MODIFICATION ---

        }

        // Echec de la connexion
        throw ValidationException::withMessages([
            'email' => [trans('auth.failed')],
        ]);
    }

    /**
     * Déconnecte l'admin.
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        // Redirige vers la page de connexion admin
        return redirect()->route('admin.login');
    }
}