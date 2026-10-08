<?php

declare(strict_types=1);

use Buki\Router\Router;

/** @var Router $router */

$router->get('/', 'HomeController@index');

$router->get('/connexion', 'AuthController@showLogin');
$router->post('/connexion', 'AuthController@login');
$router->post('/deconnexion', 'AuthController@logout', ['before' => 'AuthMiddleware']);
