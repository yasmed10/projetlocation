<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Administrateur - MeubleCuisine</title>
    {{-- Link vers votre CSS ou styles inline (copié depuis admin-login) --}}
    <style>
        body { font-family: sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; padding: 20px 0; } /* Ajout padding vertical */
        .register-container { background-color: #fff; padding: 30px 40px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 100%; max-width: 450px; } /* Largeur augmentée */
        .register-container h1 { text-align: center; color: #333; margin-bottom: 25px; font-size: 1.8em; }
        .form-group { margin-bottom: 15px; } /* Espacement réduit */
        .form-group label { display: block; margin-bottom: 6px; color: #555; font-weight: bold; font-size: 0.9em; } /* Taille label réduite */
        .form-control { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; font-size: 1em; } /* Padding réduit */
        .form-control:focus { border-color: #007bff; outline: none; box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25); }
        .btn-submit { display: block; width: 100%; padding: 12px; background-color: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 1.1em; transition: background-color 0.3s; margin-top: 20px; } /* Couleur verte, marge sup */
        .btn-submit:hover { background-color: #218838; }
        .alert-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px 15px; border-radius: 5px; margin-bottom: 20px; font-size: 0.9em; list-style: none; }
        .login-link { text-align: center; margin-top: 20px; font-size: 0.9em; }
        .login-link a { color: #007bff; text-decoration: none; }
        .login-link a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="register-container">
        <h1>Inscription Admin</h1>

        {{-- Affichage des erreurs de validation --}}
        @if ($errors->any())
            <ul class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        {{-- Utiliser la route nommée pour l'action --}}
        <form method="POST" action="{{ route('admin.register.submit') }}">
            @csrf

            {{-- Champ Nom --}}
            <div class="form-group">
                <label for="nom">Nom</label>
                <input id="nom" type="text" class="form-control" name="nom" value="{{ old('nom') }}" required autofocus>
            </div>

            {{-- Champ Prénom --}}
            <div class="form-group">
                <label for="prenom">Prénom</label>
                <input id="prenom" type="text" class="form-control" name="prenom" value="{{ old('prenom') }}" required>
            </div>

            {{-- Champ Email --}}
            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autocomplete="email">
            </div>

            {{-- Champ Mot de Passe --}}
            <div class="form-group">
                <label for="mot_pass">Mot de Passe</label>
                <input id="mot_pass" type="password" class="form-control" name="mot_pass" required autocomplete="new-password">
            </div>

            {{-- Champ Confirmation Mot de Passe --}}
            <div class="form-group">
                <label for="mot_pass_confirmation">Confirmer le Mot de Passe</label>
                <input id="mot_pass_confirmation" type="password" class="form-control" name="mot_pass_confirmation" required autocomplete="new-password">
            </div>

            <div class="form-group">
                <button type="submit" class="btn-submit">
                    S'inscrire
                </button>
            </div>

            <div class="login-link">
                Déjà un compte ? <a href="{{ route('admin.login') }}">Connectez-vous</a>
            </div>
        </form>
    </div>
</body>
</html>