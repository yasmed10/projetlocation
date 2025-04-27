<?php

use Illuminate\Support\Facades\Route;

// --- Contrôleurs Admin ---
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminSettingsController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminStatsController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminListingController;
// --- Contrôleurs Admin (Commentés car inexistants) ---
 use App\Http\Controllers\Admin\AdminReservationController; // Commenté car le contrôleur n'existe pas
 use App\Http\Controllers\Admin\AdminLitigeController;      // Commenté car le contrôleur n'existe pas

// --- Contrôleurs Auth Admin ---
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\AdminRegisterController; // <-- Ajouté pour l'inscription

// --- Contrôleur Auth Utilisateur (MANUEL - si nécessaire) ---
// !! IMPORTANT : Assurez-vous d'avoir créé ce contrôleur si vous décommentez !!
// use App\Http\Controllers\Auth\UserLoginController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// --- Routes Publiques ---
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Simple route de redirection pour éviter l'erreur "Route [login] not defined"
Route::get('/login', function() {
    return redirect()->route('admin.login');
})->name('login');

// ... Ajoutez d'autres routes publiques ici si nécessaire ...


// =======================================================================
// --- Routes Authentification Utilisateur (MANUELLEMENT DÉFINIES) ---
// =======================================================================
// Requis pour résoudre l'erreur "Route [login] not defined." si le middleware standard est utilisé.
// Décommentez et implémentez si vous avez besoin d'une connexion utilisateur séparée.
/*
Route::get('/login', [UserLoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [UserLoginController::class, 'login'])->name('login.attempt');
Route::post('/user/logout', [UserLoginController::class, 'logout'])->name('user.logout'); // Si nécessaire
*/
// =======================================================================


// =======================================================================
// --- Routes Authentification Administrateur (NON Protégées par auth:admin) ---
// =======================================================================
// Ces routes doivent être accessibles même si l'admin n'est pas connecté.
Route::prefix('admin')->name('admin.')->group(function () {

    // Connexion Admin
    Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminLoginController::class, 'login'])->name('login.submit');

    // Déconnexion Admin
    Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout');

    // --- NOUVEAU : Inscription Admin ---
    // ATTENTION : Pour la sécurité, envisagez de désactiver ces routes en production.
    //             Voir les commentaires dans AdminRegisterController ou utilisez une variable d'environnement.
    Route::get('/register', [AdminRegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AdminRegisterController::class, 'register'])->name('register.submit');
    // --- FIN NOUVEAU ---

}); // --- Fin du groupe admin public ---


// =======================================================================
// --- Routes Administration (Protégées par Middleware 'auth:admin') ---
// =======================================================================
// Seuls les administrateurs authentifiés peuvent accéder à ces routes.
Route::prefix('admin')->name('admin.')->middleware(['auth:admin'])->group(function () {

    // Tableau de Bord Principal Admin
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Paramètres Généraux & Gestion des Catégories
    Route::get('/settings', [AdminSettingsController::class, 'index'])->name('settings');
    Route::patch('/settings', [AdminSettingsController::class, 'update'])->name('settings.update');
    Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
    Route::delete('/categories/{categorie}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

    // Section Statistiques
    Route::get('/stats', [AdminStatsController::class, 'index'])->name('stats');

    // Gestion des Utilisateurs (Clients/Propriétaires)
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}/approve', [AdminUserController::class, 'approve'])->name('users.approve'); // Action symbolique ou logique spécifique
    Route::patch('/users/{user}/reject', [AdminUserController::class, 'reject'])->name('users.reject'); // Supprime l'utilisateur
    Route::patch('/users/{user}/archive', [AdminUserController::class, 'archive'])->name('users.archive');
    Route::patch('/users/{user}/restore', [AdminUserController::class, 'restore'])->name('users.restore');
    Route::get('/users/{user}/verify-cin', [AdminUserController::class, 'showVerifyCinForm'])->name('users.verify_cin');
    Route::patch('/users/{user}/approve-cin', [AdminUserController::class, 'approveCin'])->name('users.approve_cin'); // Change rôle en 'proprietaire'
    Route::patch('/users/{user}/reject-cin', [AdminUserController::class, 'rejectCin'])->name('users.reject_cin'); // Supprime les fichiers CIN

    // Gestion des Annonces (Listings)
    Route::get('/listings', [AdminListingController::class, 'index'])->name('listings.index');
    Route::get('/listings/{listing}', [AdminListingController::class, 'show'])->name('listings.show'); // Non implémenté
    Route::get('/listings/{listing}/edit', [AdminListingController::class, 'edit'])->name('listings.edit'); // Non implémenté
    Route::put('/listings/{listing}', [AdminListingController::class, 'update'])->name('listings.update'); // Non implémenté
    Route::delete('/listings/{listing}', [AdminListingController::class, 'destroy'])->name('listings.destroy');
    Route::patch('/listings/{listing}/approve', [AdminListingController::class, 'approve'])->name('listings.approve');
    Route::patch('/listings/{listing}/reject', [AdminListingController::class, 'reject'])->name('listings.reject');
    Route::patch('/listings/{listing}/archive', [AdminListingController::class, 'archive'])->name('listings.archive');
    Route::patch('/listings/{listing}/unarchive', [AdminListingController::class, 'unarchive'])->name('listings.unarchive');

    // --- Routes Commentées car Contrôleurs Inexistants ---
    // Si vous créez ces contrôleurs, décommentez les imports et ces routes.
    
    // Supervision des Réservations
    Route::get('/reservations', [AdminReservationController::class, 'index'])->name('reservations.index');
    Route::get('/reservations/{reservation}', [AdminReservationController::class, 'show'])->name('reservations.show');
    Route::patch('/reservations/{reservation}/cancel', [AdminReservationController::class, 'cancel'])->name('reservations.cancel');

    // Gestion des Litiges (Réclamations)
    Route::get('/litiges', [AdminLitigeController::class, 'index'])->name('litiges.index');
    Route::get('/litiges/{litige}', [AdminLitigeController::class, 'show'])->name('litiges.show');
    Route::patch('/litiges/{litige}/resolve', [AdminLitigeController::class, 'resolve'])->name('litiges.resolve');
    
    // --- Fin des Routes Commentées ---

}); // --- Fin du groupe admin protégé ---


// =======================================================================
// --- Autres Routes (Exemple: Espace Utilisateur Connecté 'web') ---
// =======================================================================
/*
Route::middleware(['auth:web'])->group(function () { // Ou juste 'auth' si 'web' est le guard par défaut
    Route::get('/mon-compte', [App\Http\Controllers\AccountController::class, 'dashboard'])->name('account.dashboard');
    // ... autres routes pour l'utilisateur connecté ...
});
*/

// Fallback ou autres routes générales si nécessaire