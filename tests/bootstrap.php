<?php

declare(strict_types=1);

use Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

Dotenv::createImmutable(dirname(__DIR__))->safeLoad();

// Les tests d'intégration ne doivent jamais toucher à la base de développement.
$_ENV['DB_NAME'] = $_ENV['DB_NAME_TEST'] ?? 'touche_pas_au_klaxon_test';
