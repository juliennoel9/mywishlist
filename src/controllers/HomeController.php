<?php

namespace mywishlist\controllers;

use Slim\Http\Response;
use Slim\Http\Request;

class HomeController extends Controller {
    public function displayHome(Request $request, Response $response, array $args) {
        $args['title'] = 'MyWishList - Accueil';
        $this->container->view->render($response, 'home.phtml', $args);
        return $response;
    }
}