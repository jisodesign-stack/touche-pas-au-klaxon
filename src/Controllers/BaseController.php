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

abstract class BaseController extends Controller
{
    /** @param array<string, mixed> $data */
    protected function view(Response $response, string $view, array $data = [], int $status = 200): Response
    {
        $response->setStatusCode($status);
        $response->setContent(View::render($view, $data));

        return $response;
    }

    protected function abort(Response $response, int $status): Response
    {
        return $this->view($response, 'errors/' . $status, ['title' => 'Erreur'], $status);
    }

    protected function csrfValid(Request $request): bool
    {
        return Csrf::isValid($request->request->get('_csrf'));
    }

    protected function redirect(string $to, ?string $type = null, ?string $message = null): RedirectResponse
    {
        if ($type !== null && $message !== null) {
            Flash::set($type, $message);
        }

        return new RedirectResponse($to);
    }
}
