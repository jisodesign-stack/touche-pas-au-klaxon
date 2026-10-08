<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Repositories\AgencyRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Administration des agences (villes) : liste, création, modification, suppression.
 *
 * Réservé aux administrateurs (AdminMiddleware).
 */
final class AgencyController extends BaseController
{
    /**
     * Liste les agences avec le nombre de trajets qui les utilisent.
     */
    public function index(Response $response): Response
    {
        return $this->view($response, 'admin/agencies', [
            'title' => 'Agences',
            'agencies' => (new AgencyRepository())->allWithTripCount(),
        ]);
    }

    /**
     * Affiche le formulaire de création d'une agence.
     */
    public function create(Response $response): Response
    {
        return $this->form($response, "Nouvelle agence", '/admin/agences', '');
    }

    /**
     * Valide et enregistre une nouvelle agence.
     */
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

    /**
     * Affiche le formulaire de modification d'une agence.
     *
     * @param string $id Identifiant de l'agence (paramètre d'URL)
     */
    public function edit(Response $response, string $id): Response
    {
        $agency = (new AgencyRepository())->find((int) $id);

        return $agency === null
            ? $this->abort($response, 404)
            : $this->form($response, "Modifier l'agence", "/admin/agences/{$agency['id']}/modifier", $agency['nom']);
    }

    /**
     * Valide et enregistre le nouveau nom d'une agence.
     *
     * @param string $id Identifiant de l'agence (paramètre d'URL)
     */
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
            return $this->form(
                $response,
                "Modifier l'agence",
                "/admin/agences/{$agency['id']}/modifier",
                $nom,
                $error,
                422,
            );
        }

        (new AgencyRepository())->update($agency['id'], $nom);

        return $this->redirect('/admin/agences', 'success', 'Agence modifiée.');
    }

    /**
     * Supprime une agence, sauf si des trajets l'utilisent.
     *
     * @param string $id Identifiant de l'agence (paramètre d'URL)
     */
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
            return $this->redirect(
                '/admin/agences',
                'danger',
                'Cette agence est utilisée par des trajets : suppression impossible.',
            );
        }

        $agencies->delete($agency['id']);

        return $this->redirect('/admin/agences', 'success', 'Agence supprimée.');
    }

    /**
     * Contrôle le nom d'une agence (obligatoire, 100 caractères max, unique).
     *
     * @param int|null $exceptId Agence en cours de modification, ignorée pour l'unicité
     * @return string|null Message d'erreur, ou null si le nom est valide
     */
    private function validateName(string $nom, ?int $exceptId = null): ?string
    {
        if ($nom === '' || mb_strlen($nom) > 100) {
            return "Le nom est obligatoire (100 caractères maximum).";
        }

        return (new AgencyRepository())->nameExists($nom, $exceptId) ? 'Une agence porte déjà ce nom.' : null;
    }

    /**
     * Affiche le formulaire d'agence (création ou modification).
     */
    private function form(
        Response $response,
        string $title,
        string $action,
        string $nom,
        ?string $error = null,
        int $status = 200,
    ): Response {
        return $this->view($response, 'admin/agency_form', [
            'title' => $title,
            'action' => $action,
            'nom' => $nom,
            'error' => $error,
        ], $status);
    }
}
