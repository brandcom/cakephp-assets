<?php

use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;

return static function (RouteBuilder $routes) {
    $routes->plugin(
        'Assets',
        ['path' => '/assets'],
        function (RouteBuilder $routes) {
            $routes->setRouteClass(DashedRoute::class);

            $routes->fallbacks(DashedRoute::class);
        });
};

