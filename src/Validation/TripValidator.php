<?php

declare(strict_types=1);

namespace App\Validation;

use DateTimeImmutable;

/**
 * Contrôles de cohérence d'un trajet : agences existantes et différentes, dates valides,
 * départ dans le futur, arrivée après le départ, nombres de places cohérents.
 *
 * @phpstan-type TripData array{
 *     depart_id: int,
 *     arrivee_id: int,
 *     date_depart: DateTimeImmutable,
 *     date_arrivee: DateTimeImmutable,
 *     places_total: int,
 *     places_disponibles: int
 * }
 * @phpstan-type ValidationResult array{errors: array<string, string>, data: TripData|null}
 */
final class TripValidator
{
    /** Format envoyé par les champs HTML datetime-local. */
    private const DATE_FORMAT = 'Y-m-d\TH:i';

    /** Nombre maximal de places d'un véhicule. */
    private const MAX_PLACES = 9;

    /**
     * Valide les données du formulaire.
     *
     * @param array<string, mixed> $input Données brutes du formulaire
     * @param list<int> $agencyIds Identifiants des agences existantes
     * @param DateTimeImmutable|null $currentDeparture Départ actuel du trajet modifié :
     *        une date passée inchangée reste acceptée
     * @return ValidationResult Messages d'erreur par champ, et données typées si tout est valide
     */
    public function validate(array $input, array $agencyIds, ?DateTimeImmutable $currentDeparture = null): array
    {
        $errors = [];

        $departId = $this->integer($input['agence_depart_id'] ?? null);
        $arriveeId = $this->integer($input['agence_arrivee_id'] ?? null);
        if ($departId === null || !in_array($departId, $agencyIds, true)) {
            $errors['agence_depart_id'] = "Choisissez une agence de départ valide.";
        }
        if ($arriveeId === null || !in_array($arriveeId, $agencyIds, true)) {
            $errors['agence_arrivee_id'] = "Choisissez une agence d'arrivée valide.";
        }
        if (!isset($errors['agence_depart_id']) && !isset($errors['agence_arrivee_id']) && $departId === $arriveeId) {
            $errors['agence_arrivee_id'] = "L'agence d'arrivée doit être différente de l'agence de départ.";
        }

        $dateDepart = $this->date($input['date_depart'] ?? null);
        $dateArrivee = $this->date($input['date_arrivee'] ?? null);
        if ($dateDepart === null) {
            $errors['date_depart'] = 'Date de départ invalide.';
        } elseif ($dateDepart <= new DateTimeImmutable() && $dateDepart != $currentDeparture) {
            $errors['date_depart'] = 'La date de départ doit être dans le futur.';
        }
        if ($dateArrivee === null) {
            $errors['date_arrivee'] = "Date d'arrivée invalide.";
        } elseif ($dateDepart !== null && $dateArrivee <= $dateDepart) {
            $errors['date_arrivee'] = "L'arrivée doit être postérieure au départ.";
        }

        $total = $this->integer($input['places_total'] ?? null);
        $dispo = $this->integer($input['places_disponibles'] ?? null);
        if ($total === null || $total < 1 || $total > self::MAX_PLACES) {
            $errors['places_total'] = 'Le nombre de places doit être compris entre 1 et ' . self::MAX_PLACES . '.';
        }
        if ($dispo === null || $dispo < 0) {
            $errors['places_disponibles'] = 'Le nombre de places disponibles est invalide.';
        } elseif ($total !== null && $dispo > $total) {
            $errors['places_disponibles'] = 'Les places disponibles ne peuvent pas dépasser le total.';
        }

        if (
            $errors !== []
            || $departId === null
            || $arriveeId === null
            || $dateDepart === null
            || $dateArrivee === null
            || $total === null
            || $dispo === null
        ) {
            return ['errors' => $errors, 'data' => null];
        }

        return [
            'errors' => [],
            'data' => [
                'depart_id' => $departId,
                'arrivee_id' => $arriveeId,
                'date_depart' => $dateDepart,
                'date_arrivee' => $dateArrivee,
                'places_total' => $total,
                'places_disponibles' => $dispo,
            ],
        ];
    }

    /**
     * Convertit une saisie en entier positif, ou null si elle n'est pas un entier valide.
     */
    private function integer(mixed $value): ?int
    {
        if (is_string($value) && preg_match('/^\d{1,9}$/', trim($value)) === 1) {
            return (int) $value;
        }

        return null;
    }

    /**
     * Convertit une saisie datetime-local en date, ou null si le format est invalide.
     */
    private function date(mixed $value): ?DateTimeImmutable
    {
        if (!is_string($value)) {
            return null;
        }

        $date = DateTimeImmutable::createFromFormat(self::DATE_FORMAT, $value);
        $errors = DateTimeImmutable::getLastErrors();
        $isClean = $errors === false || ($errors['warning_count'] === 0 && $errors['error_count'] === 0);

        return $date !== false && $isClean ? $date : null;
    }
}
