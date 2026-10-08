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

/**
 * Connexion et déconnexion des utilisateurs.
 *
 * Les identifiants sont vérifiés par Auth ; chaque formulaire est protégé par un jeton CSRF.
 */
final class AuthController extends Controller
{
    /**
     * Affiche le formulaire de connexion (ou redirige vers l'accueil si déjà connecté).
     */
    public function showLogin(): RedirectResponse|string
    {
        if (Auth::check()) {
            return new RedirectResponse('/');
        }

        return View::render('auth/login', ['title' => 'Connexion', 'error' => null, 'email' => '']);
    }

    /**
     * Traite le formulaire de connexion.
     *
     * Répond 419 (jeton CSRF invalide), 401 (identifiants incorrects) ou 503 (base indisponible),
     * ou redirige vers l'accueil en cas de succès.
     */
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

    /**
     * Ferme la session de l'utilisateur (si le jeton CSRF est valide) et redirige vers l'accueil.
     */
    public function logout(Request $request): RedirectResponse
    {
        if (Csrf::isValid($request->request->get('_csrf'))) {
            Auth::logout();
        }

        return new RedirectResponse('/');
    }

    /**
     * Réaffiche le formulaire de connexion avec un message d'erreur et le code HTTP donné.
     */
    private function loginError(Response $response, int $status, string $error, string $email): Response
    {
        $response->setStatusCode($status);
        $response->setContent(View::render('auth/login', ['title' => 'Connexion', 'error' => $error, 'email' => $email]));

        return $response;
    }
}
