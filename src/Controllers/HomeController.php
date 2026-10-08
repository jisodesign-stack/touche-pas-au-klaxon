<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Repositories\TripRepository;
use App\Security\Auth;
use Buki\Router\Http\Controller;
use PDOException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Page d'accueil : liste publique des trajets à venir ayant encore des places.
 */
final class HomeController extends Controller
{
    /**
     * Affiche les trajets disponibles ; répond 503 si la base est indisponible.
     */
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
            'currentUser' => Auth::user(),
        ]));

        return $response;
    }
}
