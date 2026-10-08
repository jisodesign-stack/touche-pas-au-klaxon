<?php

declare(strict_types=1);

namespace App\Core;

use Buki\Router\Router;
use Dotenv\Dotenv;

/**
 * Point d'entrée applicatif : charge la configuration (.env), démarre la session,
 * déclare le routeur et exécute la requête.
 *
 * Pour réutiliser le socle dans un autre site, il suffit de fournir un autre
 * répertoire de base contenant config/, src/ et public/.
 */
final class Application
{
    /** @var array{name: string, env: string, debug: bool} */
    private array $config;

    /**
     * @param string $basePath Racine du projet (contient config/, src/, public/)
     */
    public function __construct(private readonly string $basePath)
    {
        Dotenv::createImmutable($this->basePath)->safeLoad();
        date_default_timezone_set('Europe/Paris');

        /** @var array{name: string, env: string, debug: bool} $config */
        $config = require $this->basePath . '/config/app.php';
        $this->config = $config;

        View::configure($this->basePath . '/src/Views', $this->config['name']);
        $this->startSession();
    }

    /**
     * Démarre la session avec des cookies HttpOnly, SameSite=Lax (et Secure en HTTPS).
     */
    private function startSession(): void
    {
        ini_set('session.use_strict_mode', '1');
        session_set_cookie_params([
            'httponly' => true,
            'samesite' => 'Lax',
            'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        ]);
        session_start();
    }

    /**
     * Configure le routeur (contrôleurs, middlewares, pages 404 et 500), charge config/routes.php
     * et traite la requête courante.
     */
    public function run(): void
    {
        $router = new Router([
            'debug' => $this->config['debug'],
            'paths' => [
                'controllers' => $this->basePath . '/src/Controllers',
                'middlewares' => $this->basePath . '/src/Middleware',
            ],
            'namespaces' => [
                'controllers' => 'App\\Controllers',
                'middlewares' => 'App\\Middleware',
            ],
            'base_folder' => $this->basePath . '/public',
        ]);

        $router->notFound(function ($request, $response) {
            $response->setStatusCode(404);
            $response->setContent(View::render('errors/404', ['title' => 'Page introuvable']));

            return $response;
        });

        $router->error(function ($request, $response, $exception) {
            error_log((string) $exception);
            $response->setStatusCode(500);
            $response->setContent(View::render('errors/500', ['title' => 'Erreur']));

            return $response;
        });

        require $this->basePath . '/config/routes.php';

        $router->run();
    }
}
