<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Repositories\TripRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class TripController extends BaseController
{
    public function index(Response $response): Response
    {
        return $this->view($response, 'admin/trips', [
            'title' => 'Trajets',
            'trips' => (new TripRepository())->findAll(),
        ]);
    }

    public function delete(Request $request, Response $response, string $id): Response|RedirectResponse
    {
        if (!$this->csrfValid($request)) {
            return $this->abort($response, 419);
        }

        $trips = new TripRepository();
        if ($trips->findById((int) $id) === null) {
            return $this->abort($response, 404);
        }

        $trips->deleteById((int) $id);

        return $this->redirect('/admin/trajets', 'success', 'Trajet supprimé.');
    }
}
