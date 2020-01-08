<?php

namespace mywishlist\controllers;

use Slim\Http\Response;
use Slim\Http\Request;

class HomeController extends Controller {
    public function displayHome(Request $request, Response $response, array $args) {
        $this->container->view->render($response, 'home.phtml', ["title" => "MyWishList - Accueil"]);
        return $response;
    }
}