<?php

declare(strict_types=1);

/**
 * Script d'installation de la base : (re)crée la base DB_NAME, le schéma et le jeu de données de démo.
 *
 * Attention : la base existante portant ce nom est supprimée.
 *
 * Usage : php bin/db-install.php [--no-seed]
 */

use App\Core\Database;
use Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

Dotenv::createImmutable(dirname(__DIR__))->safeLoad();

$name = $_ENV['DB_NAME'] ?? 'touche_pas_au_klaxon';
if (!preg_match('/^[A-Za-z0-9_]+$/', $name)) {
    fwrite(STDERR, "DB_NAME invalide.\n");
    exit(1);
}

$pdo = Database::connect(false);
$pdo->exec("DROP DATABASE IF EXISTS `$name`");
$pdo->exec("CREATE DATABASE `$name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$pdo->exec("USE `$name`");

$files = ['schema.sql'];
if (!in_array('--no-seed', $argv, true)) {
    $files[] = 'seed.sql';
}

foreach ($files as $file) {
    $sql = file_get_contents(dirname(__DIR__) . '/database/' . $file);
    $pdo->exec($sql);
    echo "OK : $file\n";
}

echo "Base `$name` prête.\n";
