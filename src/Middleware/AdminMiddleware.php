<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\View;
use App\Security\Auth;
use Buki\Router\Http\Middleware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

final class AdminMiddleware extends Middleware
{
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
