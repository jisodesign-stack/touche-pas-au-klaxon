<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

/**
 * Accès à la base de données MySQL / MariaDB via PDO.
 *
 * Les paramètres de connexion viennent du fichier .env (DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASSWORD).
 */
final class Database
{
    /** Connexion partagée, créée à la première utilisation. */
    private static ?PDO $pdo = null;

    /**
     * Retourne la connexion unique à la base de l'application.
     *
     * @throws \PDOException Si le serveur est injoignable ou refuse la connexion
     */
    public static function connection(): PDO
    {
        return self::$pdo ??= self::connect(true);
    }

    /**
     * Ouvre une nouvelle connexion, avec ou sans sélection de la base (utile pour la créer).
     *
     * @param bool $withDatabase Sélectionne la base DB_NAME si vrai
     * @throws \PDOException Si le serveur est injoignable ou refuse la connexion
     */
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
