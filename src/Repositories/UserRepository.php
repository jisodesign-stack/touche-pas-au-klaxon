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

    /** @param array<string, mixed> $params */
    private function fetchOne(string $sql, array $params): ?User
    {
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();

        if (!is_array($row)) {
            return null;
        }

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
