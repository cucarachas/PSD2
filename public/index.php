<?php

require "../bootstrap.php";

// send some CORS headers so the API can be called from anywhere
//header("Access-Control-Allow-Origin: *");
//header("Content-Type: application/json; charset=UTF-8");
//header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
//header("Access-Control-Max-Age: 3600");
//header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER["REQUEST_METHOD"];
$uriParts = explode( '/', $uri );

// define all valid endpoints - this will act as a simple router
$routes = [
    'home' => [
        'method' => 'GET',
        'expression' => '/^\/?$/',
        'controller' => '\Src\Controllers\AppController',
        'controller_method' => 'index'
    ],
    'payments' => [
        'method' => 'GET',
        'expression' => '/^\/payments?$/',
        'controller' => '\Src\Controllers\AppController',
        'controller_method' => 'payments'
    ],
    'payment' => [
        'method' => 'POST',
        'expression' => '/^\/payment?$/',
        'controller' => '\Src\Controllers\AppController',
        'controller_method' => 'payment'
    ]
];

$routeFound = null;
foreach ($routes as $route) {
    if ($route['method'] == $requestMethod && preg_match($route['expression'], $uri))
    {
        $routeFound = $route;
        break;
    }
}

if (! $routeFound) {
    header("HTTP/1.1 404 Not Found");
    exit();
}

$methodName = $routeFound['controller_method'];

$controller = new $routeFound['controller']();
$controller->$methodName();
