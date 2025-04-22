<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// Correction: Utiliser le nom de modèle correct
use App\Models\Categorie; // ÉTAIT: Category

class AdminCategoryController extends Controller
{
    /**
     * Enregistre une nouvelle catégorie.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            // Correction: Table 'categories'
            'name' => 'required|string|max:255|unique:categories,nom', // Assure l'unicité (colonne 'nom')
        ]);

        // Correction: Utiliser le nom de modèle correct
        Categorie::create(['nom' => $validatedData['name']]); // Assurer que le champ est bien 'nom'

        return redirect()->route('admin.settings') // Redirige vers la page des paramètres où se trouve le formulaire
                     ->with('success', 'Catégorie ajoutée avec succès.');
    }

    /**
     * Supprime une catégorie spécifique.
     *
     * // Correction: Utiliser le nom de modèle correct pour Route Model Binding
     * @param  \App\Models\Categorie  $categorie // ÉTAIT: Category $category
     * @return \Illuminate\Http\RedirectResponse
     */
    // Correction: Utiliser le nom de modèle correct et le nom de variable correspondant
    public function destroy(Categorie $categorie) // ÉTAIT: Category $category
    {
        // Vérifier si la catégorie est utilisée avant de supprimer (optionnel mais recommandé)
        // NOTE: La vérification est complexe : Categorie -> Objet -> Annonce
        // if ($categorie->objets()->whereHas('annonces')->count() > 0) {
        //     return redirect()->route('admin.settings')
        //                  ->with('error', 'Impossible de supprimer une catégorie utilisée par des annonces.');
        // }

        // Correction: Utiliser la variable correcte
        $categorie->delete();

        return redirect()->route('admin.settings') // Redirige vers la page des paramètres
                     // Correction: Utiliser la variable correcte dans le message
                     ->with('success', "Catégorie '{$categorie->nom}' supprimée avec succès.");
    }
}