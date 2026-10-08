<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Repositories\AgencyRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class AgencyController extends BaseController
{
    public function index(Response $response): Response
    {
        return $this->view($response, 'admin/agencies', [
            'title' => 'Agences',
            'agencies' => (new AgencyRepository())->allWithTripCount(),
        ]);
    }

    public function create(Response $response): Response
    {
        return $this->form($response, "Nouvelle agence", '/admin/agences', '');
    }

    public function store(Request $request, Response $response): Response|RedirectResponse
    {
        if (!$this->csrfValid($request)) {
            return $this->abort($response, 419);
        }

        $nom = trim((string) $request->request->get('nom', ''));
        $error = $this->validateName($nom);

        if ($error !== null) {
            return $this->form($response, 'Nouvelle agence', '/admin/agences', $nom, $error, 422);
        }

        (new AgencyRepository())->create($nom);

        return $this->redirect('/admin/agences', 'success', 'Agence créée.');
    }

    public function edit(Response $response, string $id): Response
    {
        $agency = (new AgencyRepository())->find((int) $id);

        return $agency === null
            ? $this->abort($response, 404)
            : $this->form($response, "Modifier l'agence", "/admin/agences/{$agency['id']}/modifier", $agency['nom']);
    }

    public function update(Request $request, Response $response, string $id): Response|RedirectResponse
    {
        $agency = (new AgencyRepository())->find((int) $id);
        if ($agency === null) {
            return $this->abort($response, 404);
        }
        if (!$this->csrfValid($request)) {
            return $this->abort($response, 419);
        }

        $nom = trim((string) $request->request->get('nom', ''));
        $error = $this->validateName($nom, $agency['id']);

        if ($error !== null) {
            return $this->form($response, "Modifier l'agence", "/admin/agences/{$agency['id']}/modifier", $nom, $error, 422);
        }

        (new AgencyRepository())->update($agency['id'], $nom);

        return $this->redirect('/admin/agences', 'success', 'Agence modifiée.');
    }

    public function delete(Request $request, Response $response, string $id): Response|RedirectResponse
    {
        if (!$this->csrfValid($request)) {
            return $this->abort($response, 419);
        }

        $agencies = new AgencyRepository();
        $agency = $agencies->find((int) $id);
        if ($agency === null) {
            return $this->abort($response, 404);
        }

        if ($agencies->tripCount($agency['id']) > 0) {
            return $this->redirect('/admin/agences', 'danger', 'Cette agence est utilisée par des trajets : suppression impossible.');
        }

        $agencies->delete($agency['id']);

        return $this->redirect('/admin/agences', 'success', 'Agence supprimée.');
    }

    private function validateName(string $nom, ?int $exceptId = null): ?string
    {
        if ($nom === '' || mb_strlen($nom) > 100) {
            return "Le nom est obligatoire (100 caractères maximum).";
        }

        return (new AgencyRepository())->nameExists($nom, $exceptId) ? 'Une agence porte déjà ce nom.' : null;
    }

    private function form(Response $response, string $title, string $action, string $nom, ?string $error = null, int $status = 200): Response
    {
        return $this->view($response, 'admin/agency_form', [
            'title' => $title,
            'action' => $action,
            'nom' => $nom,
            'error' => $error,
        ], $status);
    }
}
