<?php

declare(strict_types=1);

use Buki\Router\Router;

/** @var Router $router */

$router->get('/', 'HomeController@index');

$router->get('/connexion', 'AuthController@showLogin');
$router->post('/connexion', 'AuthController@login');
$router->post('/deconnexion', 'AuthController@logout', ['before' => 'AuthMiddleware']);

$auth = ['before' => 'AuthMiddleware'];
$router->get('/mes-trajets', 'TripController@mine', $auth);
$router->get('/trajets/nouveau', 'TripController@create', $auth);
$router->post('/trajets', 'TripController@store', $auth);
$router->get('/trajets/:id/modifier', 'TripController@edit', $auth);
$router->post('/trajets/:id/modifier', 'TripController@update', $auth);
$router->post('/trajets/:id/supprimer', 'TripController@delete', $auth);
