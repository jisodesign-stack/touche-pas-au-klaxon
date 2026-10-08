<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

/**
 * Accès aux agences (villes) : lecture, création, modification, suppression.
 */
final class AgencyRepository
{
    /**
     * Toutes les agences, triées par nom.
     *
     * @return list<array{id: int, nom: string}>
     */
    public function all(): array
    {
        $agencies = [];
        foreach (Database::connection()->query('SELECT id, nom FROM agences ORDER BY nom') as $row) {
            $agencies[] = ['id' => (int) $row['id'], 'nom' => (string) $row['nom']];
        }

        return $agencies;
    }

    /**
     * Toutes les agences avec le nombre de trajets (au départ ou à l'arrivée) qui les utilisent.
     *
     * @return list<array{id: int, nom: string, trajets: int}>
     */
    public function allWithTripCount(): array
    {
        $sql = 'SELECT a.id, a.nom, COUNT(t.id) AS trajets
                FROM agences a
                LEFT JOIN trajets t ON a.id IN (t.agence_depart_id, t.agence_arrivee_id)
                GROUP BY a.id, a.nom
                ORDER BY a.nom';

        $agencies = [];
        foreach (Database::connection()->query($sql) as $row) {
            $agencies[] = ['id' => (int) $row['id'], 'nom' => (string) $row['nom'], 'trajets' => (int) $row['trajets']];
        }

        return $agencies;
    }

    /**
     * Retrouve une agence par son identifiant.
     *
     * @return array{id: int, nom: string}|null Null si l'agence n'existe pas
     */
    public function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT id, nom FROM agences WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return is_array($row) ? ['id' => (int) $row['id'], 'nom' => (string) $row['nom']] : null;
    }

    /**
     * Indique si une agence porte déjà ce nom.
     *
     * @param int|null $exceptId Agence à ignorer (celle en cours de modification)
     */
    public function nameExists(string $nom, ?int $exceptId = null): bool
    {
        $stmt = Database::connection()->prepare('SELECT COUNT(*) FROM agences WHERE nom = :nom AND id <> :id');
        $stmt->execute(['nom' => $nom, 'id' => $exceptId ?? 0]);

        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Crée une agence.
     *
     * @throws \PDOException Si le nom existe déjà (contrainte d'unicité)
     */
    public function create(string $nom): void
    {
        Database::connection()->prepare('INSERT INTO agences (nom) VALUES (:nom)')->execute(['nom' => $nom]);
    }

    /**
     * Renomme une agence.
     *
     * @throws \PDOException Si le nom existe déjà (contrainte d'unicité)
     */
    public function update(int $id, string $nom): void
    {
        Database::connection()
            ->prepare('UPDATE agences SET nom = :nom WHERE id = :id')
            ->execute(['nom' => $nom, 'id' => $id]);
    }

    /**
     * Supprime une agence.
     *
     * @throws \PDOException Si des trajets utilisent l'agence (clé étrangère)
     */
    public function delete(int $id): void
    {
        Database::connection()->prepare('DELETE FROM agences WHERE id = :id')->execute(['id' => $id]);
    }

    /**
     * Nombre de trajets qui partent de l'agence ou y arrivent.
     */
    public function tripCount(int $id): int
    {
        $stmt = Database::connection()->prepare(
            'SELECT COUNT(*) FROM trajets WHERE agence_depart_id = :id OR agence_arrivee_id = :id2',
        );
        $stmt->execute(['id' => $id, 'id2' => $id]);

        return (int) $stmt->fetchColumn();
    }
}
