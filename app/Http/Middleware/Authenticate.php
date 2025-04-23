<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request; // <-- VÃ©rifiez que ceci est bien importÃ©

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * Redirige vers la page de connexion appropriÃ©e en fonction du guard demandÃ©
     * ou du prÃ©fixe de l'URL.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo(Request $request): ?string
{
    if (! $request->expectsJson()) {
        // Toujours rediriger vers admin.login, quelles que soient les routes
        return route('admin.login');
    }
    
    return null;
}
}