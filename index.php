<?php

use mywishlist\controllers\HomeController;
use mywishlist\controllers\ItemController;
use mywishlist\controllers\ListController;
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

$app->get('/apropos[/]', function ($request, $response, array $args) {
    $this->view->render($response, 'about.phtml', ["title" => "MyWishList - A Propos"]);
})->setName('about');
$app->redirect('/about[/]', $container->router->pathFor('about'));


$app->get('/listes[/]', function ($request, $response, array $args) {
    $controller = new ListController($this);
    return $controller->displayLists($request, $response, $args);
})->setName('publicList');

$app->get('/l/{num:[0-9]+}[/]', function ($request, $response, array $args) {
    $controller = new ListController($this);
    return $controller->getList($request, $response, $args);
})->setName('list');

$app->get('/l/{num:[0-9]+}/i/{id:[0-9]+}[/]', function ($request, $response, array $args) {
    $controller = new ItemController($this);
    return $controller->displayItem($request, $response, $args);
})->setName('item');

$app->get('/nouvelleListe[/]', function ($request, $response, array $args) {
    $controller = new ListController($this);
    return $controller->getNewList($request, $response, $args);
})->setName('newList');

$app->post('/nouvelleListe[/]', function ($request, $response, array $args) {
    $controller = new ListController($this);
    return $controller->postNewList($request, $response, $args);
});

$app->get('/modifierListe/{token:[a-zA-Z0-9]+}[/]', function ($request, $response, array $args) {
    $controller = new ListController($this);
    return $controller->getEditList($request, $response, $args);
})->setName('editList');

$app->post('/modifierListe/{token:[a-zA-Z0-9]+}[/]', function ($request, $response, array $args) {
    $controller = new ListController($this);
    return $controller->postEditList($request, $response, $args);
});

$app->get('/ajouterItem/{token:[a-zA-Z0-9]+}[/]', function($request, $response, array $args) {
    $controller = new ItemController($this);
    return $controller->getNewItem($request, $response, $args);
})->setName('newItem');

$app->post('/ajouterItem/{token:[a-zA-Z0-9]+}[/]', function ($request, $response, array $args) {
    $controller = new ItemController($this);
    return $controller->postNewItem($request, $response, $args);
});

/**
 * Run of Slim
 */
$app->run();
