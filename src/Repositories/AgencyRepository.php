<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class AgencyRepository
{
    /** @return list<array{id: int, nom: string}> */
    public function all(): array
    {
        $agencies = [];
        foreach (Database::connection()->query('SELECT id, nom FROM agences ORDER BY nom') as $row) {
            $agencies[] = ['id' => (int) $row['id'], 'nom' => (string) $row['nom']];
        }

        return $agencies;
    }
}
