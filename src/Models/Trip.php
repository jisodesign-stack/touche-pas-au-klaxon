<?php

declare(strict_types=1);

namespace App\Models;

use DateTimeImmutable;

final class Trip
{
    public function __construct(
        public readonly int $id,
        public readonly int $agenceDepartId,
        public readonly string $agenceDepart,
        public readonly int $agenceArriveeId,
        public readonly string $agenceArrivee,
        public readonly DateTimeImmutable $dateDepart,
        public readonly DateTimeImmutable $dateArrivee,
        public readonly int $placesTotal,
        public readonly int $placesDisponibles,
        public readonly int $auteurId,
        public readonly string $auteurNom,
        public readonly string $auteurTelephone,
        public readonly string $auteurEmail,
    ) {
    }
}
