<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Models\User;

/**
 * Accès aux utilisateurs. Les employés viennent du système RH :
 * seule la suppression est prévue (administration), pas de création ni de modification.
 */
final class UserRepository
{
    /**
     * Retrouve un utilisateur par son identifiant.
     *
     * @return User|null Null s'il n'existe pas
     */
    public function findById(int $id): ?User
    {
        return $this->fetchOne('SELECT * FROM utilisateurs WHERE id = :id', ['id' => $id]);
    }

    /**
     * Retrouve un utilisateur par son adresse email (identifiant de connexion).
     *
     * @return User|null Null s'il n'existe pas
     */
    public function findByEmail(string $email): ?User
    {
        return $this->fetchOne('SELECT * FROM utilisateurs WHERE email = :email', ['email' => $email]);
    }

    /**
     * Tous les utilisateurs, triés par nom puis prénom.
     *
     * @return list<User>
     */
    public function all(): array
    {
        $users = [];
        foreach (Database::connection()->query('SELECT * FROM utilisateurs ORDER BY nom, prenom') as $row) {
            $users[] = $this->hydrate($row);
        }

        return $users;
    }

    /**
     * Supprime un utilisateur ; ses trajets sont supprimés en cascade par la base.
     */
    public function delete(int $id): void
    {
        Database::connection()->prepare('DELETE FROM utilisateurs WHERE id = :id')->execute(['id' => $id]);
    }

    /**
     * Exécute une requête et retourne le premier utilisateur trouvé.
     *
     * @param array<string, mixed> $params */
    private function fetchOne(string $sql, array $params): ?User
    {
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();

        return is_array($row) ? $this->hydrate($row) : null;
    }

    /** @param array<string, mixed> $row */
    private function hydrate(array $row): User
    {
        return new User(
            (int) $row['id'],
            (string) $row['nom'],
            (string) $row['prenom'],
            (string) $row['email'],
            (string) $row['telephone'],
            (string) $row['role'],
            (string) $row['mot_de_passe'],
        );
    }
}
