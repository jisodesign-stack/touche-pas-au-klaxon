<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Flash;
use App\Models\Trip;
use App\Repositories\AgencyRepository;
use App\Repositories\TripRepository;
use App\Security\Auth;
use App\Security\Csrf;
use App\Validation\TripValidator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class TripController extends BaseController
{
    private const FIELDS = ['agence_depart_id', 'agence_arrivee_id', 'date_depart', 'date_arrivee', 'places_total', 'places_disponibles'];

    public function mine(Response $response): Response
    {
        $trips = (new TripRepository())->findByAuthor($this->userId());

        return $this->view($response, 'trips/mine', ['title' => 'Mes trajets', 'trips' => $trips]);
    }

    public function create(Response $response): Response
    {
        return $this->form($response, 'Proposer un trajet', '/trajets', ['places_total' => '4', 'places_disponibles' => '4']);
    }

    public function store(Request $request, Response $response): Response|RedirectResponse
    {
        if (!Csrf::isValid($request->request->get('_csrf'))) {
            return $this->abort($response, 419);
        }

        $input = $this->input($request);
        $result = (new TripValidator())->validate($input, $this->agencyIds());

        if ($result['data'] === null) {
            return $this->form($response, 'Proposer un trajet', '/trajets', $input, $result['errors'], 422);
        }

        (new TripRepository())->create($result['data'], $this->userId());
        Flash::set('success', 'Trajet créé.');

        return new RedirectResponse('/mes-trajets');
    }

    public function edit(Response $response, string $id): Response
    {
        $trip = $this->ownedTrip((int) $id);
        if (!$trip instanceof Trip) {
            return $this->abort($response, $trip);
        }

        return $this->form($response, 'Modifier le trajet', "/trajets/{$trip->id}/modifier", [
            'agence_depart_id' => (string) $trip->agenceDepartId,
            'agence_arrivee_id' => (string) $trip->agenceArriveeId,
            'date_depart' => $trip->dateDepart->format('Y-m-d\TH:i'),
            'date_arrivee' => $trip->dateArrivee->format('Y-m-d\TH:i'),
            'places_total' => (string) $trip->placesTotal,
            'places_disponibles' => (string) $trip->placesDisponibles,
        ]);
    }

    public function update(Request $request, Response $response, string $id): Response|RedirectResponse
    {
        $trip = $this->ownedTrip((int) $id);
        if (!$trip instanceof Trip) {
            return $this->abort($response, $trip);
        }
        if (!Csrf::isValid($request->request->get('_csrf'))) {
            return $this->abort($response, 419);
        }

        $input = $this->input($request);
        $result = (new TripValidator())->validate($input, $this->agencyIds(), $trip->dateDepart);

        if ($result['data'] === null) {
            return $this->form($response, 'Modifier le trajet', "/trajets/{$trip->id}/modifier", $input, $result['errors'], 422);
        }

        (new TripRepository())->update($trip->id, $this->userId(), $result['data']);
        Flash::set('success', 'Trajet modifié.');

        return new RedirectResponse('/mes-trajets');
    }

    public function delete(Request $request, Response $response, string $id): Response|RedirectResponse
    {
        $trip = $this->ownedTrip((int) $id);
        if (!$trip instanceof Trip) {
            return $this->abort($response, $trip);
        }
        if (!Csrf::isValid($request->request->get('_csrf'))) {
            return $this->abort($response, 419);
        }

        (new TripRepository())->delete($trip->id, $this->userId());
        Flash::set('success', 'Trajet supprimé.');

        return new RedirectResponse('/mes-trajets');
    }

    /** Retourne le trajet de l'utilisateur connecté, ou le code HTTP d'erreur (404 / 403). */
    private function ownedTrip(int $id): Trip|int
    {
        $trip = (new TripRepository())->findById($id);

        if ($trip === null) {
            return 404;
        }

        return $trip->auteurId === $this->userId() ? $trip : 403;
    }

    private function userId(): int
    {
        return Auth::user()->id ?? 0;
    }

    /** @return list<int> */
    private function agencyIds(): array
    {
        return array_map(static fn (array $agency): int => $agency['id'], (new AgencyRepository())->all());
    }

    /** @return array<string, string> */
    private function input(Request $request): array
    {
        $input = [];
        foreach (self::FIELDS as $field) {
            $input[$field] = (string) $request->request->get($field, '');
        }

        return $input;
    }

    /**
     * @param array<string, string> $values
     * @param array<string, string> $errors
     */
    private function form(Response $response, string $title, string $action, array $values, array $errors = [], int $status = 200): Response
    {
        return $this->view($response, 'trips/form', [
            'title' => $title,
            'action' => $action,
            'values' => $values,
            'errors' => $errors,
            'agencies' => (new AgencyRepository())->all(),
        ], $status);
    }
}
