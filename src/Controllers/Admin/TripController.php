<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Repositories\TripRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Administration des trajets : liste de tous les trajets et suppression.
 */
final class TripController extends BaseController
{
    /**
     * Liste tous les trajets (y compris passés ou complets).
     */
    public function index(Response $response): Response
    {
        return $this->view($response, 'admin/trips', [
            'title' => 'Trajets',
            'trips' => (new TripRepository())->findAll(),
        ]);
    }

    /**
     * Supprime un trajet, quel que soit son auteur.
     *
     * @param string $id Identifiant du trajet (paramètre d'URL)
     */
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
