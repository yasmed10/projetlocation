<?php

namespace Database\Factories;

use App\Models\User; // Pointer vers votre modèle User
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Le modèle associé à la factory.
     */
    protected $model = User::class;

    /**
     * Le mot de passe actuel utilisé par la factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Utiliser les noms de colonnes de la table 'utilisateurs'
            'nom' => fake()->lastName(),
            'prenom' => fake()->firstName(),
            'email' => fake()->unique()->safeEmail(),
            // 'email_verified_at' => now(), // Colonne non présente
            'mot_de_passe' => static::$password ??= Hash::make('password'), // Utiliser le champ correct
            'role' => fake()->randomElement(['client', 'proprietaire']), // Utiliser les rôles définis
            'image_profile' => null, // Ou chemin vers image factice si uploadé
            'cin_recto' => null,     // Ou chemin vers image factice
            'cin_verso' => null,     // Ou chemin vers image factice
            // 'remember_token' => Str::random(10), // Colonne non présente
        ];
    }

    // La fonction unverified() n'est plus pertinente sans 'email_verified_at'
}