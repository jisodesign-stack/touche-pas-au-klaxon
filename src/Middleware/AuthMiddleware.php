<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Security\Auth;
use Buki\Router\Http\Middleware;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class AuthMiddleware extends Middleware
{
    public function handle(): bool|RedirectResponse
    {
        return Auth::check() ? true : new RedirectResponse('/connexion');
    }
}
