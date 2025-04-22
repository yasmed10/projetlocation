<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// use App\Models\Setting; // <- Supprimé (OK)
// Correction: Utiliser le nom de modèle correct
use App\Models\Categorie; // ÉTAIT: Category

class AdminSettingsController extends Controller
{
    /**
     * Affiche la page des paramètres.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        // Répcupérer les paramètres depuis config/site.php - OK
        $settings = [
            'site_name'             => config('site.site_name', 'MeubleCuisine Défaut'),
            'contact_email'         => config('site.contact_email', 'contact@defaut.com'),
            'contact_phone'         => config('site.contact_phone', ''),
            'main_address'          => config('site.main_address', ''),
            'commission_rate'       => config('site.commission_rate', 0),
            'max_reservation_days'  => config('site.max_reservation_days', 1),
            'listing_approval'      => config('site.listing_approval', false),
        ];

        // Récupérer toutes les catégories
        // Correction: Utiliser le nom de modèle correct
        $categories = Categorie::orderBy('nom')->get(); // Utiliser 'nom' pour trier

        return view('admin.settings', compact('settings', 'categories'));
    }

    /**
     * Met à jour les paramètres du site (SECTION IGNORÉE POUR LES PARAMÈTRES DE CONFIG).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        // La validation peut rester pour vérifier le format, mais la sauvegarde sera ignorée pour ces clés de config.
        // Note: les clés de validation utilisent un point '.', ce qui est correct pour les tableaux en entrée.
        $validatedData = $request->validate([
            'settings.site_name' => 'sometimes|required|string|max:255',
            'settings.contact_email' => 'sometimes|required|email|max:255',
            'settings.contact_phone' => 'nullable|string|max:50',
            'settings.main_address' => 'nullable|string|max:1000',
            'settings.commission_rate' => 'sometimes|required|numeric|min:0|max:100',
            'settings.max_reservation_days' => 'sometimes|required|integer|min:1',
            // Correction: La validation pour 'boolean' est correcte pour 0/1 ou true/false
            'settings.listing_approval' => 'sometimes|required|boolean',
        ]);

        // AUCUNE sauvegarde des paramètres gérés par config/site.php n'est effectuée ici. - OK

        // Retourner avec un message indiquant que ces paramètres ne sont pas modifiés ici - OK
        return redirect()->route('admin.settings')
                     ->with('info', 'Les paramètres généraux et de vente sont définis dans la configuration et ne sont pas modifiables via ce formulaire.');
    }
}