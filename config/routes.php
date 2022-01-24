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
    function (RouteBuilder $routes) {
        $routes->plugin(
            "Assets",
            ["path" => "/assets"],
            function (RouteBuilder $routes) {
                $routes->setRouteClass(DashedRoute::class);
                $routes->fallbacks(DashedRoute::class);
            }
        );
        $routes->fallbacks(DashedRoute::class);
    }
);
