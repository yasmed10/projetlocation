<?php

use Illuminate\Support\Facades\Route;
// use Illuminate\Support\Facades\Auth;

// --- Contrôleurs Admin ---
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminSettingsController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminStatsController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminListingController; // Importer le nouveau contrôleur

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- Routes Publiques ---
Route::get('/', function () {
    // Assurez-vous que la vue 'welcome.blade.php' existe bien
    return view('welcome');
})->name('home');
// ... autres routes publiques ...


// --- Routes d'Authentification ---
// TODO: Décommenter et configurer si nécessaire
// Auth::routes(['verify' => true]);


// --- Routes Administration ---
// =========================================================================
// !! SÉCURITÉ !! : TODO: Rétablir les middlewares ['auth', 'admin'] dès que l'auth fonctionne
// =========================================================================
Route::prefix('admin')->name('admin.')->/*middleware(['auth', 'admin'])->*/group(function () {

    // Tableau de Bord
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Paramètres & Catégories
    Route::get('/settings', [AdminSettingsController::class, 'index'])->name('settings');
    Route::patch('/settings', [AdminSettingsController::class, 'update'])->name('settings.update');
    Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
    // Utiliser {categorie} pour correspondre au paramètre du contrôleur et au Route Model Binding
    Route::delete('/categories/{categorie}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

    // Statistiques
    Route::get('/stats', [AdminStatsController::class, 'index'])->name('stats');

    // Gestion des Utilisateurs
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}/approve', [AdminUserController::class, 'approve'])->name('users.approve');
    Route::patch('/users/{user}/reject', [AdminUserController::class, 'reject'])->name('users.reject');
    Route::patch('/users/{user}/block', [AdminUserController::class, 'block'])->name('users.block');
    Route::patch('/users/{user}/unblock', [AdminUserController::class, 'unblock'])->name('users.unblock');
    Route::get('/users/{user}/verify-cin', [AdminUserController::class, 'showVerifyCinForm'])->name('users.verify_cin');
    // Routes pour CIN Approve/Reject (si implémentées)
    Route::patch('/users/{user}/approve-cin', [AdminUserController::class, 'approveCin'])->name('users.approve_cin');
    Route::patch('/users/{user}/reject-cin', [AdminUserController::class, 'rejectCin'])->name('users.reject_cin');

    // Gestion des Annonces (Listings)
    Route::get('/listings', [AdminListingController::class, 'index'])->name('listings.index');
    Route::get('/listings/{listing}', [AdminListingController::class, 'show'])->name('listings.show'); // Utilise Route Model Binding pour Annonce
    Route::get('/listings/{listing}/edit', [AdminListingController::class, 'edit'])->name('listings.edit');
    Route::put('/listings/{listing}', [AdminListingController::class, 'update'])->name('listings.update'); // PUT pour remplacement complet
    Route::delete('/listings/{listing}', [AdminListingController::class, 'destroy'])->name('listings.destroy');
    // Actions spécifiques avec PATCH
    Route::patch('/listings/{listing}/approve', [AdminListingController::class, 'approve'])->name('listings.approve');
    Route::patch('/listings/{listing}/reject', [AdminListingController::class, 'reject'])->name('listings.reject');
    Route::patch('/listings/{listing}/archive', [AdminListingController::class, 'archive'])->name('listings.archive');
    // Route pour les détails du rejet (utilisée par le JS)
    Route::get('/listings/{listing}/rejection-details', [AdminListingController::class, 'getRejectionDetails'])->name('listings.rejection_details');

    // TODO: Ajouter d'autres groupes de routes admin si nécessaire (Réservations, Réclamations...)

}); // Fin du groupe admin

// --- Autres Routes ---
// TODO: Rétablir middleware 'auth' si nécessaire pour les routes utilisateur
// Route::middleware('auth')->group(function () {
//     // Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     // Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
// });