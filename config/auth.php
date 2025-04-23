<?php // config/auth.php

return [
    'defaults' => [
        'guard' => 'web', // Garde 'web' par dÃ©faut pour les utilisateurs normaux
        'passwords' => 'users',
    ],

    'guards' => [
        'web' => [ // Pour Utilisateurs (client/partenaire)
            'driver' => 'session',
            'provider' => 'users',
        ],
        // AJOUTÃ‰: Guard pour Admins
        'admin' => [
            'driver' => 'session',
            'provider' => 'admins', // Pointe vers le provider 'admins'
        ],
    ],

    'providers' => [
        'users' => [ // Pour ModÃ¨le User (table utilisateurs)
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],
        // AJOUTÃ‰: Provider pour Admins
        'admins' => [
            'driver' => 'eloquent',
            'model' => App\Models\Admin::class, // Pointe vers le modÃ¨le Admin (table admins)
        ],
    ],

    'passwords' => [
        'users' => [ /* ... configuration existante ... */ ],
         // Optionnel: Ajoutez 'admins' si besoin de rÃ©initialisation mdp admin
    ],

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),
];