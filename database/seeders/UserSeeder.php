<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'email' => 'client1@example.com',
            'mot_de_passe' => Hash::make('password123'),
            'role' => 'client'
        ]);

        User::create([
            'nom' => 'Martin',
            'prenom' => 'Marie',
            'email' => 'client2@example.com',
            'mot_de_passe' => Hash::make('password123'),
            'role' => 'client'
        ]);
    }
}
