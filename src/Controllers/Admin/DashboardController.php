<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Repositories\AgencyRepository;
use App\Repositories\TripRepository;
use App\Repositories\UserRepository;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tableau de bord de l'administration : compteurs et accès aux listes.
 */
final class DashboardController extends BaseController
{
    /**
     * Affiche le nombre d'utilisateurs, d'agences et de trajets.
     */
    public function index(Response $response): Response
    {
        return $this->view($response, 'admin/dashboard', [
            'title' => 'Administration',
            'users' => count((new UserRepository())->all()),
            'agencies' => count((new AgencyRepository())->all()),
            'trips' => count((new TripRepository())->findAll()),
        ]);
    }
}
