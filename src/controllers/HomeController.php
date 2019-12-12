<?php


namespace mywishlist\controllers;


class HomeController extends Controller {
    public function displayHome($request, $response, $args) {
        $this->container->view->render($response, 'home.phtml', ["title" => "MyWishList - Accueil"]);
        return $response;
    }
}