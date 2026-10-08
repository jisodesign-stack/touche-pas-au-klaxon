<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

final class Database
{
    private static ?PDO $pdo = null;

    public static function connection(): PDO
    {
        return self::$pdo ??= self::connect(true);
    }

    /** Connexion au serveur, avec ou sans sélection de la base. */
    public static function connect(bool $withDatabase): PDO
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;charset=utf8mb4%s',
            $_ENV['DB_HOST'] ?? '127.0.0.1',
            $_ENV['DB_PORT'] ?? '3306',
            $withDatabase ? ';dbname=' . ($_ENV['DB_NAME'] ?? 'touche_pas_au_klaxon') : '',
        );

        return new PDO($dsn, $_ENV['DB_USER'] ?? 'root', $_ENV['DB_PASSWORD'] ?? '', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
}
