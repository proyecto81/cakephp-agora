<?php

declare(strict_types=1);

use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;

return static function (RouteBuilder $routes) {
    $routes->setRouteClass(DashedRoute::class);

    // Dashboard
    $routes->connect('/', ['controller' => 'Backend', 'action' => 'index']);
    $routes->connect('/backend', ['controller' => 'Backend', 'action' => 'index']);

    $routes->fallbacks(DashedRoute::class);
};
