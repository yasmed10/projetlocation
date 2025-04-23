<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin; // Importer le modèle Admin
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth; // Importer Auth
use Illuminate\Validation\Rules;
use Illuminate\Auth\Events\Registered; // Optionnel: pour les événements

class AdminRegisterController extends Controller
{
    /**
     * Appliquer le middleware 'guest' pour le guard 'admin'.
     * Redirige les admins déjà connectés vers le dashboard admin.
     */
    public function __construct()
    {
        $this->middleware('guest:admin');
    }

    /**
     * Affiche le formulaire d'inscription admin.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        // Créer la vue: resources/views/auth/admin-register.blade.php
        return view('auth.admin-register');
    }

    /**
     * Gère la requête d'inscription admin.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function register(Request $request)
    {
        $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:admins,email'], // Vérifier l'unicité dans la table admins
            'mot_pass' => ['required', 'confirmed', Rules\Password::defaults()], // Utilise 'mot_pass' et 'mot_pass_confirmation' dans le formulaire
        ]);

        // Créer l'admin en utilisant le modèle Admin
        $admin = Admin::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'mot_pass' => Hash::make($request->mot_pass), // Hasher le mot de passe
        ]);

        // Optionnel: Déclencher un événement si nécessaire
        // event(new Registered($admin));

        // Connecter l'admin nouvellement créé en utilisant le guard 'admin'
        Auth::guard('admin')->login($admin);

        // Rediriger vers le dashboard admin après l'inscription réussie
        return redirect()->route('admin.dashboard');
    }
}