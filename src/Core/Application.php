<?php

declare(strict_types=1);

namespace App\Core;

use Buki\Router\Router;
use Dotenv\Dotenv;

final class Application
{
    /** @var array{name: string, env: string, debug: bool} */
    private array $config;

    public function __construct(private readonly string $basePath)
    {
        Dotenv::createImmutable($this->basePath)->safeLoad();

        /** @var array{name: string, env: string, debug: bool} $config */
        $config = require $this->basePath . '/config/app.php';
        $this->config = $config;

        View::configure($this->basePath . '/src/Views', $this->config['name']);
    }

    public function run(): void
    {
        $router = new Router([
            'debug' => $this->config['debug'],
            'paths' => ['controllers' => $this->basePath . '/src/Controllers'],
            'namespaces' => ['controllers' => 'App\\Controllers'],
            'base_folder' => $this->basePath . '/public',
        ]);

        $router->notFound(function ($request, $response) {
            $response->setStatusCode(404);
            $response->setContent(View::render('errors/404'));

            return $response;
        });

        require $this->basePath . '/config/routes.php';

        $router->run();
    }
}
