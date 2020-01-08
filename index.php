<?php

use mywishlist\controllers\AccountController;
use mywishlist\controllers\HomeController;
use mywishlist\controllers\ItemController;
use mywishlist\controllers\ListController;
use Slim\Views\PhpRenderer;
use Slim\Http\Response;
use Slim\Http\Request;

require_once 'vendor/autoload.php';

session_start();
date_default_timezone_set('Europe/Paris');

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
 * Middleware HTTPS
 */
$app->add(function (Request $request, Response $response, $next) {
    // redirect with https if not on localhost
    if ($request->getUri()->getScheme() !== 'https' && $request->getUri()->getHost() !== 'localhost') {
        $uri = $request->getUri()->withScheme("https");
        return $response->withRedirect((string)$uri);
    } else {
        return $next($request, $response);
    }
});

$container['notFoundHandler'] = function ($container) {
    return function ($request, Response $response) use ($container) {
        $container->view->render($response, 'errors/404.phtml', ["title" => "404 Not Found"]);
        return $response->withStatus(404);
    };
};


/**
 * Main pages
 */
$app->get('/', function (Request $request, Response $response, array $args) {
    $controller = new HomeController($this);
    return $controller->displayHome($request, $response, $args);
})->setName('home');

$app->get('/apropos[/]', function (Request $request, Response $response, array $args) {
    $this->view->render($response, 'about.phtml', ["title" => "MyWishList - A Propos"]);
})->setName('about');
$app->redirect('/about[/]', $container->router->pathFor('about'));

$app->get('/deconnexion', function (Request $request, Response $response, array $args) {
    $controller = new AccountController($this);
    return $controller->getLogout($request, $response, $args);
})->setName('logout');


/**
 * Account
 */
$app->get('/inscription[/]', function (Request $request, Response $response, array $args){
    $controller = new AccountController($this);
    return $controller->getRegister($request, $response, $args);
})->setName('register');

$app->post('/inscription[/]', function (Request $request, Response $response, array $args) {
    $controller = new AccountController($this);
    return $controller->postRegister($request, $response, $args);
});

$app->get('/connexion[/]', function (Request $request, Response $response, array $args){
    $controller = new AccountController($this);
    return $controller->getLogin($request, $response, $args);
})->setName('login');

$app->post('/connexion[/]', function (Request $request, Response $response, array $args) {
    $controller = new AccountController($this);
    return $controller->postLogin($request, $response, $args);
});

$app->get('/moncompte[/]', function (Request $request, Response $response, array $args) {
    $controller = new AccountController($this);
    return $controller->getAccount($request, $response, $args);
})->setName('account');

$app->post('/moncompte[/]', function (Request $request, Response $response, array $args) {
    $controller = new AccountController($this);
    return $controller->postEditAccount($request, $response, $args);
});


/**
 * Lists
 */
$app->get('/listesPubliques[/]', function (Request $request, Response $response, array $args) {
    $controller = new ListController($this);
    return $controller->displayPublicLists($request, $response, $args);
})->setName('publicLists');

$app->get('/l/{token:[a-zA-Z0-9]+}[/]', function (Request $request, Response $response, array $args) {
    $controller = new ListController($this);
    return $controller->displayList($request, $response, $args);
})->setName('list');

$app->get('/nouvelleListe[/]', function (Request $request, Response $response, array $args) {
    $controller = new ListController($this);
    return $controller->getNewList($request, $response, $args);
})->setName('newList');

$app->post('/nouvelleListe[/]', function (Request $request, Response $response, array $args) {
    $controller = new ListController($this);
    return $controller->postNewList($request, $response, $args);
});

$app->get('/modifierListe/{token:[a-zA-Z0-9]+}[/]', function (Request $request, Response $response, array $args) {
    $controller = new ListController($this);
    return $controller->getEditList($request, $response, $args);
})->setName('editList');

$app->post('/modifierListe/{token:[a-zA-Z0-9]+}[/]', function (Request $request, Response $response, array $args) {
    $controller = new ListController($this);
    return $controller->postEditList($request, $response, $args);
});

$app->post('/l/{token:[a-zA-Z0-9]+}[/]', function (Request $request, Response $response, array $args) {
    $controller = new ListController($this);
    return $controller->postNewListMessage($request, $response, $args);
})->setName('newListMessage');

$app->get('/mesListes[/]', function (Request $request, Response $response, array $args) {
    $controller = new ListController($this);
    return $controller->displayAccountLists($request, $response, $args);
})->setName('accountLists');

$app->get('/createurs[/]', function (Request $request, Response $response, array $args) {
    $controller = new ListController($this);
    return $controller->displayCreators($request, $response, $args);
})->setName('creators');

$app->get('/{creator:[a-zA-Z0-9_-]+}/listes[/]', function (Request $request, Response $response, array $args) {
    $controller = new ListController($this);
    return $controller->displayCreatorPublicLists($request, $response, $args);
})->setName('creatorPublicLists');


/**
 * Items
 */
$app->get('/l/{token:[a-zA-Z0-9]+}/i/{id:[0-9]+}[/]', function (Request $request, Response $response, array $args) {
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

$app->post('/ajouterItem/{token:[a-zA-Z0-9]+}[/]', function (Request $request, Response $response, array $args) {
    $controller = new ItemController($this);
    return $controller->postNewItem($request, $response, $args);
});

$app->get('/modifierItem/l/{token:[a-zA-Z0-9]+}/i/{id:[0-9]+}[/]', function (Request $request, Response $response, array $args) {
    $controller = new ItemController($this);
    return $controller->getEditItem($request, $response, $args);
})->setName('editItem');

$app->post('/modifierItem/l/{token:[a-zA-Z0-9]+}/i/{id:[0-9]+}[/]', function (Request $request, Response $response, array $args) {
    $controller = new ItemController($this);
    return $controller->postEditItem($request, $response, $args);
});


/**
 * Validator
 */
$app->get('/check_username', function (Request $request, Response $response, array $args) {
    $controller = new AccountController($this);
    return $controller->liveCheckUsername($request, $response, $args);
});

$app->get('/check_email', function (Request $request, Response $response, array $args) {
    $controller = new AccountController($this);
    return $controller->liveCheckEmail($request, $response, $args);
});

$app->get('/check_login', function (Request $request, Response $response, array $args) {
    $controller = new AccountController($this);
    return $controller->liveCheckLogin($request, $response, $args);
});

/**
 * Run of Slim
 */
$app->run();
