<?php

use Cake\Routing\Route\DashedRoute;
use Cake\Routing\Router;
use Cake\Routing\RouteBuilder;

Router::plugin(
    'Assets',
    ['path' => '/assets'],
    function (RouteBuilder $routes) {
        $routes->setRouteClass(DashedRoute::class);
        $routes->fallbacks(DashedRoute::class);
    }
);

Router::prefix(
    "Admin",
    ["path" => "/assets"],
    function (RouteBuilder $routes) {
        $routes->fallbacks(DashedRoute::class);
        $routes->plugin('Assets',
            ['path' => '/assets'],
            function (RouteBuilder $routes) {
                $routes->setRouteClass(DashedRoute::class);

                $routes->fallbacks(DashedRoute::class);
            });
    }
);
