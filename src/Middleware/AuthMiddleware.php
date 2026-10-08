<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Security\Auth;
use Buki\Router\Http\Middleware;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Middleware réservé aux utilisateurs connectés : redirige les anonymes vers la connexion.
 */
final class AuthMiddleware extends Middleware
{
    /**
     * @return bool|RedirectResponse True pour laisser passer la requête, sinon la redirection à envoyer
     */
    public function handle(): bool|RedirectResponse
    {
        return Auth::check() ? true : new RedirectResponse('/connexion');
    }
}
