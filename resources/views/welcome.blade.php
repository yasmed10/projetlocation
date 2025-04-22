<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>MeubleCuisine</title>
    </head>
    <body>
        <h1>Bienvenue sur MeubleCuisine !</h1>
        <p>Le site est en cours de construction.</p>

        {{-- Optionnel: Lien vers l'admin (si non sécurisé pour l'instant) --}}
        <p><a href="{{ route('admin.dashboard') }}">Accéder à l'administration (Dev)</a></p>
    </body>
</html>