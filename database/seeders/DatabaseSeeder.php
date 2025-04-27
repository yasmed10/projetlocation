<?php

namespace Database\Seeders;

use App\Models\User; // OK
use App\Models\Admin; // Ajouter pour seeder Admin
use App\Models\Categorie; // Ajouter pour seeder Catégories
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // Importer Hash
use Database\Seeders\UserSeeder; // Ajouter pour seeder User

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Créer des Catégories par défaut
        Categorie::firstOrCreate(['nom' => 'Meubles Hauts']);
        Categorie::firstOrCreate(['nom' => 'Meubles Bas']);
        Categorie::firstOrCreate(['nom' => 'Plans de travail']);
        Categorie::firstOrCreate(['nom' => 'Accessoires']);
        Categorie::firstOrCreate(['nom' => 'Électroménager']);

        // 2. Créer un utilisateur de test spécifique
        User::firstOrCreate(
            ['email' => 'test@example.com'], // Critère de recherche
            [
                'nom' => 'Utilisateur', // Utiliser 'nom'
                'prenom' => 'Test',      // Utiliser 'prenom'
                'mot_de_passe' => Hash::make('password'), // Utiliser 'mot_de_passe' et hasher
                'role' => 'client',      // Utiliser 'role'
            ]
        );

         // 3. Créer un admin de test (dans la table 'admins')
         Admin::firstOrCreate(
             ['email' => 'admin@example.com'],
             [
                 'nom' => 'Admin',
                 'prenom' => 'Principal',
                 'mot_pass' => Hash::make('adminpassword'), // Utiliser 'mot_pass' et hasher
             ]
         );

        // 4. Optionnel: Créer des utilisateurs factices via la factory
        // User::factory(10)->create();

        // Ajout du UserSeeder dans le DatabaseSeeder
        $this->call([
            UserSeeder::class,
        ]);

        // Vous pouvez appeler d'autres seeders spécifiques ici si vous en créez
        // $this->call([
        //     ObjetSeeder::class,
        //     AnnonceSeeder::class,
        // ]);
    }
}