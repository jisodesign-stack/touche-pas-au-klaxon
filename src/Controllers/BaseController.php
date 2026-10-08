<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use Buki\Router\Http\Controller;
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
}
