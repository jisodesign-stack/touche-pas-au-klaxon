<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Security\Auth;
use App\Security\Csrf;
use Buki\Router\Http\Controller;
use PDOException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class AuthController extends Controller
{
    public function showLogin(): RedirectResponse|string
    {
        if (Auth::check()) {
            return new RedirectResponse('/');
        }

        return View::render('auth/login', ['title' => 'Connexion', 'error' => null, 'email' => '']);
    }

    public function login(Request $request, Response $response): RedirectResponse|Response
    {
        $email = (string) $request->request->get('email', '');
        $password = (string) $request->request->get('password', '');

        if (!Csrf::isValid($request->request->get('_csrf'))) {
            return $this->loginError($response, 419, 'Session expirée, veuillez réessayer.', $email);
        }

        try {
            $authenticated = Auth::attempt($email, $password);
        } catch (PDOException $e) {
            error_log($e->getMessage());

            return $this->loginError($response, 503, 'Service momentanément indisponible, veuillez réessayer.', $email);
        }

        if (!$authenticated) {
            return $this->loginError($response, 401, 'Email ou mot de passe incorrect.', $email);
        }

        return new RedirectResponse('/');
    }

    public function logout(Request $request): RedirectResponse
    {
        if (Csrf::isValid($request->request->get('_csrf'))) {
            Auth::logout();
        }

        return new RedirectResponse('/');
    }

    private function loginError(Response $response, int $status, string $error, string $email): Response
    {
        $response->setStatusCode($status);
        $response->setContent(View::render('auth/login', ['title' => 'Connexion', 'error' => $error, 'email' => $email]));

        return $response;
    }
}
