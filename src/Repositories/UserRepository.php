<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Models\User;

final class UserRepository
{
    public function findById(int $id): ?User
    {
        return $this->fetchOne('SELECT * FROM utilisateurs WHERE id = :id', ['id' => $id]);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->fetchOne('SELECT * FROM utilisateurs WHERE email = :email', ['email' => $email]);
    }

    /** @return list<User> */
    public function all(): array
    {
        $users = [];
        foreach (Database::connection()->query('SELECT * FROM utilisateurs ORDER BY nom, prenom') as $row) {
            $users[] = $this->hydrate($row);
        }

        return $users;
    }

    public function delete(int $id): void
    {
        Database::connection()->prepare('DELETE FROM utilisateurs WHERE id = :id')->execute(['id' => $id]);
    }

    /** @param array<string, mixed> $params */
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
