<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Repositories\AgencyRepository;
use App\Repositories\TripRepository;
use App\Repositories\UserRepository;
use Symfony\Component\HttpFoundation\Response;

final class DashboardController extends BaseController
{
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
