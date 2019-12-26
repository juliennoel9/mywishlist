<?php

use mywishlist\controllers\AccountController;
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
 * Main pages
 */
$app->get('/', function ($request, $response, array $args) {
    $controller = new HomeController($this);
    return $controller->displayHome($request, $response, $args);
})->setName('home');

$app->get('/apropos[/]', function ($request, $response, array $args) {
    $this->view->render($response, 'about.phtml', ["title" => "MyWishList - A Propos"]);
})->setName('about');
$app->redirect('/about[/]', $container->router->pathFor('about'));

/**
 * Account
 */
$app->get('/inscription[/]', function ($request, $response, array $args){
    $controller = new AccountController($this);
    return $controller->getRegister($request, $response, $args);
})->setName('register');

$app->post('/inscription[/]', function ($request, $response, array $args) {
    $controller = new AccountController($this);
    return $controller->postRegister($request, $response, $args);
});

$app->get('/connexion[/]', function ($request, $response, array $args){
    $controller = new AccountController($this);
    return $controller->getLogin($request, $response, $args);
})->setName('login');

$app->post('/connexion[/]', function ($request, $response, array $args) {
    $controller = new AccountController($this);
    return $controller->postLogin($request, $response, $args);
});

/**
 * Lists
 */
$app->get('/listes[/]', function ($request, $response, array $args) {
    $controller = new ListController($this);
    return $controller->displayPublicLists($request, $response, $args);
})->setName('publicLists');

$app->get('/l/{token:[a-zA-Z0-9]+}[/]', function ($request, $response, array $args) {
    $controller = new ListController($this);
    return $controller->displayList($request, $response, $args);
})->setName('list');

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

$app->post('/l/{token:[a-zA-Z0-9]+}[/]', function ($request, $response, array $args) {
    $controller = new ListController($this);
    return $controller->postNewListMessage($request, $response, $args);
})->setName('newListMessage');

/**
 * Items
 */
$app->get('/l/{token:[a-zA-Z0-9]+}/i/{id:[0-9]+}[/]', function ($request, $response, array $args) {
    $controller = new ItemController($this);
    return $controller->displayItem($request, $response, $args);
})->setName('item');

$app->post('/l/{token:[a-zA-Z0-9]+}/i/{id:[0-9]+}[/]', function($request, $response, array $args){
    $controller = new ItemController($this);
    return $controller->postReserveItem($request, $response, $args);
})->setName('reserveItem');

$app->get('/ajouterItem/{token:[a-zA-Z0-9]+}[/]', function($request, $response, array $args) {
    $controller = new ItemController($this);
    return $controller->getNewItem($request, $response, $args);
})->setName('newItem');

$app->post('/ajouterItem/{token:[a-zA-Z0-9]+}[/]', function ($request, $response, array $args) {
    $controller = new ItemController($this);
    return $controller->postNewItem($request, $response, $args);
});

$app->get('/modifierItem/l/{token:[a-zA-Z0-9]+}/i/{id:[0-9]+}[/]', function ($request, $response, array $args) {
    $controller = new ItemController($this);
    return $controller->getEditItem($request, $response, $args);
})->setName('editItem');

$app->post('/modifierItem/l/{token:[a-zA-Z0-9]+}/i/{id:[0-9]+}[/]', function ($request, $response, array $args) {
    $controller = new ItemController($this);
    return $controller->postEditItem($request, $response, $args);
});

/**
 * Run of Slim
 */
$app->run();
