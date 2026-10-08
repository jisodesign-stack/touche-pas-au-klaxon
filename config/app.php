<?php

/**
 * Configuration générale de l'application, lue depuis le fichier .env.
 *
 * @return array{name: string, env: string, debug: bool}
 */

declare(strict_types=1);

return [
    'name' => $_ENV['APP_NAME'] ?? 'Touche pas au klaxon',
    'env' => $_ENV['APP_ENV'] ?? 'production',
    'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
];
