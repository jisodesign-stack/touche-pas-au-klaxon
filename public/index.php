<?php

declare(strict_types=1);

/**
 * Point d'entrée unique de l'application (contrôleur frontal) : toutes les requêtes passent ici.
 */

use App\Core\Application;

require dirname(__DIR__) . '/vendor/autoload.php';

(new Application(dirname(__DIR__)))->run();
