<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Administrateur - MeubleCuisine</title>
    {{-- Link vers votre CSS ou styles inline --}}
    <style>
        body { font-family: sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .login-container { background-color: #fff; padding: 30px 40px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        .login-container h1 { text-align: center; color: #333; margin-bottom: 25px; font-size: 1.8em; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; color: #555; font-weight: bold; }
        .form-control { width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; font-size: 1em; }
        .form-control:focus { border-color: #007bff; outline: none; box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25); }
        .btn-submit { display: block; width: 100%; padding: 12px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 1.1em; transition: background-color 0.3s; }
        .btn-submit:hover { background-color: #0056b3; }
        .alert-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px 15px; border-radius: 5px; margin-bottom: 20px; font-size: 0.9em; list-style: none; }
        .form-check { display: flex; align-items: center; margin-bottom: 20px; }
        .form-check-input { margin-right: 8px; width: auto; }
        .form-check-label { color: #555; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Administration</h1>

        {{-- Affichage des erreurs --}}
        @if ($errors->any())
            <ul class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}">
            @csrf

            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
            </div>

            <div class="form-group">
                {{-- Le name="mot_pass" doit correspondre Ã  la validation dans AdminLoginController --}}
                <label for="mot_pass">Mot de Passe</label>
                <input id="mot_pass" type="password" class="form-control" name="mot_pass" required autocomplete="current-password">
            </div>

            <div class="form-group form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label" for="remember">
                    Se souvenir de moi
                </label>
            </div>

            <div class="form-group">
                <button type="submit" class="btn-submit">
                    Se Connecter
                </button>
            </div>
        </form>
    </div>
</body>
</html>