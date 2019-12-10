<?php


namespace mywishlist\controllers;


use mywishlist\models\Liste;

class ListeController extends Controller {
    public function displayList($request, $response, $args) {
        $this->view->render($response, 'listes.phtml', ["title" => "MyWishList - Listes", "listes" => Liste::all()]);
        return $response;
    }
}