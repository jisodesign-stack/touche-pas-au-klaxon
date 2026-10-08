<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\View;
use App\Security\Auth;
use Buki\Router\Http\Middleware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware réservé aux administrateurs : redirige les anonymes vers la connexion
 * et répond 403 aux utilisateurs sans le rôle admin.
 */
final class AdminMiddleware extends Middleware
{
    /**
     * @return bool|Response True pour laisser passer la requête, sinon la réponse à envoyer
     */
    public function handle(Response $response): bool|Response
    {
        $user = Auth::user();

        if ($user === null) {
            return new RedirectResponse('/connexion');
        }

        if (!$user->isAdmin()) {
            $response->setStatusCode(403);
            $response->setContent(View::render('errors/403', ['title' => 'Accès refusé']));

            return $response;
        }

        return true;
    }
}
