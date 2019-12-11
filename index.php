<?php

use mywishlist\controllers\HomeController;
use mywishlist\controllers\ListeController;
use Slim\Views\PhpRenderer;

require_once 'vendor/autoload.php';

session_start();

\mywishlist\config\Database::connect();

/**
 * Dev. mode to show errors in details
 */
$config = [
    'settings' => [
        'displayErrorDetails' => 1,
    ],
];

/**
 * Instanciation of Slim
 */
$app = new Slim\App($config);
$container = $app->getContainer();

$container['view'] = function($container) {
    $vars = [
        "rootUri" => $container->request->getUri()->getBasePath(),
        "router" => $container->router,
        "title" => "MyWishList"
    ];
    $renderer = new PhpRenderer(__DIR__.'/src/views', $vars);
    $renderer->setLayout('layout.phtml');
    return $renderer;
};

/**
 * Routes
 */
$app->get('/', function ($request, $response, array $args) {
    global $container;
    $controller = new HomeController($container);
    return $controller->displayHome($request, $response, $args);
})->setName('home');

$app->get('/apropos', function ($request, $response, array $args) {
    $this->view->render($response, 'about.phtml', ["title" => "MyWishList - A Propos"]);
})->setName('about');

$app->get('/listes', function ($request, $response, array $args) {
    global $container;
    $controller = new ListeController($container);
    return $controller->displayList($request, $response, $args);
})->setName('listes');

/**
 * Run of Slim
 */
$app->run();