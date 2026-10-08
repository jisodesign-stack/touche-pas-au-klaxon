<?php

declare(strict_types=1);

use Buki\Router\Router;

/** @var Router $router */

$router->get('/', 'HomeController@index');

$router->get('/connexion', 'AuthController@showLogin');
$router->post('/connexion', 'AuthController@login');
$router->post('/deconnexion', 'AuthController@logout', ['before' => 'AuthMiddleware']);

$auth = ['before' => 'AuthMiddleware'];
$router->get('/trajets/nouveau', 'TripController@create', $auth);
$router->post('/trajets', 'TripController@store', $auth);
$router->get('/trajets/:id/modifier', 'TripController@edit', $auth);
$router->post('/trajets/:id/modifier', 'TripController@update', $auth);
$router->post('/trajets/:id/supprimer', 'TripController@delete', $auth);

$router->group('/admin', function () use ($router) {
    $router->get('', 'Admin\DashboardController@index');

    $router->get('/utilisateurs', 'Admin\UserController@index');
    $router->post('/utilisateurs/:id/supprimer', 'Admin\UserController@delete');

    $router->get('/agences', 'Admin\AgencyController@index');
    $router->get('/agences/nouveau', 'Admin\AgencyController@create');
    $router->post('/agences', 'Admin\AgencyController@store');
    $router->get('/agences/:id/modifier', 'Admin\AgencyController@edit');
    $router->post('/agences/:id/modifier', 'Admin\AgencyController@update');
    $router->post('/agences/:id/supprimer', 'Admin\AgencyController@delete');

    $router->get('/trajets', 'Admin\TripController@index');
    $router->post('/trajets/:id/supprimer', 'Admin\TripController@delete');
}, ['before' => 'AdminMiddleware']);
