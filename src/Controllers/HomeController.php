<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Repositories\TripRepository;
use Buki\Router\Http\Controller;
use PDOException;
use Symfony\Component\HttpFoundation\Response;

final class HomeController extends Controller
{
    public function index(Response $response): Response
    {
        try {
            $trips = (new TripRepository())->findUpcomingAvailable();
            $error = null;
        } catch (PDOException) {
            $trips = [];
            $error = 'Les trajets sont momentanément indisponibles.';
            $response->setStatusCode(503);
        }

        $response->setContent(View::render('home', [
            'title' => 'Trajets disponibles',
            'trips' => $trips,
            'error' => $error,
        ]));

        return $response;
    }
}
