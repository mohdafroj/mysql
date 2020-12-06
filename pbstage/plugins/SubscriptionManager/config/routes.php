<?php
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\Routing\Route\DashedRoute;

Router::plugin(
    'SubscriptionManager',
    ['path' => '/admin/subscription'],
    function (RouteBuilder $routes) {
        $routes->connect('/dashboards', ['controller' => 'Dashboards']);
        $routes->fallbacks(DashedRoute::class);
    }
);
