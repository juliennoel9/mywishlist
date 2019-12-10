<?php


namespace mywishlist\controllers;


class HomeController extends Controller {
    public function displayHome($request, $response, $args) {
        $this->view->render($response, 'home.html', ["title" => "MyWishList - Home"]);
        return $response;
    }
}