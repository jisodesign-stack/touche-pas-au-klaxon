<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Flash;
use App\Core\View;
use App\Security\Csrf;
use Buki\Router\Http\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Contrôleur de base : fournit aux contrôleurs de l'application les réponses communes
 * (rendu d'une vue, page d'erreur, redirection avec message flash, contrôle CSRF).
 */
abstract class BaseController extends Controller
{
    /**
     * Rend une vue dans le layout et l'écrit dans la réponse.
     *
     * @param array<string, mixed> $data Variables transmises à la vue
     */
    protected function view(Response $response, string $view, array $data = [], int $status = 200): Response
    {
        $response->setStatusCode($status);
        $response->setContent(View::render($view, $data));

        return $response;
    }

    /**
     * Affiche la page d'erreur correspondant au code HTTP (vue « errors/{code} »).
     */
    protected function abort(Response $response, int $status): Response
    {
        return $this->view($response, 'errors/' . $status, ['title' => 'Erreur'], $status);
    }

    /**
     * Indique si le jeton CSRF soumis dans le formulaire correspond à celui de la session.
     */
    protected function csrfValid(Request $request): bool
    {
        return Csrf::isValid($request->request->get('_csrf'));
    }

    /**
     * Redirige vers une URL, avec un message flash optionnel affiché à la page suivante.
     *
     * @param string|null $type Type d'alerte Bootstrap (success, danger…)
     */
    protected function redirect(string $to, ?string $type = null, ?string $message = null): RedirectResponse
    {
        if ($type !== null && $message !== null) {
            Flash::set($type, $message);
        }

        return new RedirectResponse($to);
    }
}
