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
        'displayErrorDetails' => true,
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
    $controller = new HomeController($this);
    return $controller->displayHome($request, $response, $args);
})->setName('home');

$app->get('/apropos', function ($request, $response, array $args) {
    $this->view->render($response, 'about.phtml', ["title" => "MyWishList - A Propos"]);
})->setName('about');

$app->get('/listes', function ($request, $response, array $args) {
    $controller = new ListeController($this);
    return $controller->displayLists($request, $response, $args);
})->setName('publicList');

$app->get('/l/{token:[a-zA-Z0-9]+}', function ($request, $response, array $args) {
    $c = new ListeController($this);
    return $c->getListe($request, $response, $args);
})->setName('list');


/**
 * Run of Slim
 */
$app->run();
