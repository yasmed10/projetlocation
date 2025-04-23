<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException; // <-- NÃ©cessaire pour la gestion de l'authentification
use Illuminate\Http\Request;                   // <-- NÃ©cessaire pour la mÃ©thode redirectTo/renderable

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        // Vous pouvez ajouter des types d'exceptions Ã  ne pas logger ici
        // \Illuminate\Auth\AuthenticationException::class, // Par exemple
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
        'mot_pass', // Ajout du mot de passe admin pour ne pas le flasher
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // Optionnel: Logique de rapport personnalisÃ©e
        });

        // --- GESTION SPÃ‰CIFIQUE DE L'AUTHENTIFICATION EXCEPTION ---
        // C'est ici qu'on personnalise la redirection en cas de non-authentification
        $this->renderable(function (AuthenticationException $e, Request $request) {
            // Si la requÃªte ne s'attend pas Ã  du JSON (navigateur)
            if (! $request->expectsJson()) {
                // DÃ©termine le guard qui a Ã©chouÃ©
                $guard = data_get($e->guards(), 0);

                // Redirige vers la page de connexion appropriÃ©e
                switch ($guard) {
                    case 'admin': // Si l'Ã©chec vient du guard 'admin'
                        $loginRoute = 'admin.login'; // Nom de la route de connexion admin
                        break;
                    default: // Pour 'web' ou tout autre guard
                        $loginRoute = 'login'; // Nom de la route de connexion par dÃ©faut
                        break;
                }
                // Effectue la redirection vers la route de connexion dÃ©terminÃ©e
                return redirect()->guest(route($loginRoute));
            }

            // Pour les requÃªtes API, retourne une erreur 401 JSON
            return response()->json(['message' => $e->getMessage()], 401);
        });
        // --- FIN DE LA GESTION AUTHENTIFICATION EXCEPTION ---
    }


    /* --- Alternative avec la mÃ©thode unauthenticated() (si vous prÃ©fÃ©rez) ---
       --- Commentez ou supprimez le bloc $this->renderable(...) ci-dessus et utilisez ceci: ---

    /**
     * Convert an authentication exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     * /
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $exception->getMessage()], 401);
        }

        $guard = data_get($exception->guards(), 0);

        switch ($guard) {
            case 'admin':
                $loginRoute = 'admin.login';
                break;
            default:
                $loginRoute = 'login';
                break;
        }

        return redirect()->guest(route($loginRoute));
    }
    --- Fin de l'alternative unauthenticated() --- */


}