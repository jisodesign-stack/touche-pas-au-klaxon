<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Models\Trip;
use DateTimeImmutable;

final class TripRepository
{
    /**
     * Trajets à venir ayant encore des places, du plus proche au plus lointain.
     *
     * @return list<Trip>
     */
    public function findUpcomingAvailable(): array
    {
        $sql = <<<'SQL'
            SELECT t.id, d.nom AS agence_depart, a.nom AS agence_arrivee,
                   t.date_depart, t.date_arrivee, t.places_total, t.places_disponibles,
                   CONCAT(u.prenom, ' ', u.nom) AS auteur_nom, u.telephone AS auteur_telephone, u.email AS auteur_email
            FROM trajets t
            JOIN agences d ON d.id = t.agence_depart_id
            JOIN agences a ON a.id = t.agence_arrivee_id
            JOIN utilisateurs u ON u.id = t.auteur_id
            WHERE t.date_depart > NOW() AND t.places_disponibles > 0
            ORDER BY t.date_depart ASC
            SQL;

        $trips = [];
        foreach (Database::connection()->query($sql) as $row) {
            $trips[] = new Trip(
                (int) $row['id'],
                (string) $row['agence_depart'],
                (string) $row['agence_arrivee'],
                new DateTimeImmutable((string) $row['date_depart']),
                new DateTimeImmutable((string) $row['date_arrivee']),
                (int) $row['places_total'],
                (int) $row['places_disponibles'],
                (string) $row['auteur_nom'],
                (string) $row['auteur_telephone'],
                (string) $row['auteur_email'],
            );
        }

        return $trips;
    }
}
