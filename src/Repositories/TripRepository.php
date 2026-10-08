<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Models\Trip;
use DateTimeImmutable;

final class TripRepository
{
    private const SELECT = <<<'SQL'
        SELECT t.id, t.agence_depart_id, d.nom AS agence_depart, t.agence_arrivee_id, a.nom AS agence_arrivee,
               t.date_depart, t.date_arrivee, t.places_total, t.places_disponibles, t.auteur_id,
               CONCAT(u.prenom, ' ', u.nom) AS auteur_nom, u.telephone AS auteur_telephone, u.email AS auteur_email
        FROM trajets t
        JOIN agences d ON d.id = t.agence_depart_id
        JOIN agences a ON a.id = t.agence_arrivee_id
        JOIN utilisateurs u ON u.id = t.auteur_id
        SQL;

    /**
     * Trajets à venir ayant encore des places, du plus proche au plus lointain.
     *
     * @return list<Trip>
     */
    public function findUpcomingAvailable(): array
    {
        return $this->fetchAll(
            'WHERE t.date_depart > NOW() AND t.places_disponibles > 0 ORDER BY t.date_depart ASC',
        );
    }

    /** @return list<Trip> */
    public function findByAuthor(int $authorId): array
    {
        return $this->fetchAll('WHERE t.auteur_id = :auteur ORDER BY t.date_depart DESC', ['auteur' => $authorId]);
    }

    public function findById(int $id): ?Trip
    {
        return $this->fetchAll('WHERE t.id = :id', ['id' => $id])[0] ?? null;
    }

    /** @param array{depart_id: int, arrivee_id: int, date_depart: DateTimeImmutable, date_arrivee: DateTimeImmutable, places_total: int, places_disponibles: int} $data */
    public function create(array $data, int $authorId): int
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'INSERT INTO trajets (agence_depart_id, agence_arrivee_id, date_depart, date_arrivee, places_total, places_disponibles, auteur_id)
             VALUES (:depart, :arrivee, :date_depart, :date_arrivee, :total, :dispo, :auteur)',
        );
        $stmt->execute($this->params($data) + ['auteur' => $authorId]);

        return (int) $pdo->lastInsertId();
    }

    /** @param array{depart_id: int, arrivee_id: int, date_depart: DateTimeImmutable, date_arrivee: DateTimeImmutable, places_total: int, places_disponibles: int} $data */
    public function update(int $id, int $authorId, array $data): bool
    {
        $stmt = Database::connection()->prepare(
            'UPDATE trajets SET agence_depart_id = :depart, agence_arrivee_id = :arrivee, date_depart = :date_depart,
                    date_arrivee = :date_arrivee, places_total = :total, places_disponibles = :dispo
             WHERE id = :id AND auteur_id = :auteur',
        );
        $stmt->execute($this->params($data) + ['id' => $id, 'auteur' => $authorId]);

        return $stmt->rowCount() > 0;
    }

    public function delete(int $id, int $authorId): bool
    {
        $stmt = Database::connection()->prepare('DELETE FROM trajets WHERE id = :id AND auteur_id = :auteur');
        $stmt->execute(['id' => $id, 'auteur' => $authorId]);

        return $stmt->rowCount() > 0;
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function params(array $data): array
    {
        return [
            'depart' => $data['depart_id'],
            'arrivee' => $data['arrivee_id'],
            'date_depart' => $data['date_depart']->format('Y-m-d H:i:s'),
            'date_arrivee' => $data['date_arrivee']->format('Y-m-d H:i:s'),
            'total' => $data['places_total'],
            'dispo' => $data['places_disponibles'],
        ];
    }

    /**
     * @param array<string, mixed> $params
     * @return list<Trip>
     */
    private function fetchAll(string $clause, array $params = []): array
    {
        $stmt = Database::connection()->prepare(self::SELECT . ' ' . $clause);
        $stmt->execute($params);

        $trips = [];
        foreach ($stmt as $row) {
            $trips[] = new Trip(
                (int) $row['id'],
                (int) $row['agence_depart_id'],
                (string) $row['agence_depart'],
                (int) $row['agence_arrivee_id'],
                (string) $row['agence_arrivee'],
                new DateTimeImmutable((string) $row['date_depart']),
                new DateTimeImmutable((string) $row['date_arrivee']),
                (int) $row['places_total'],
                (int) $row['places_disponibles'],
                (int) $row['auteur_id'],
                (string) $row['auteur_nom'],
                (string) $row['auteur_telephone'],
                (string) $row['auteur_email'],
            );
        }

        return $trips;
    }
}
