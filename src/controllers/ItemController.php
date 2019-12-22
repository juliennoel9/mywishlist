<?php

namespace mywishlist\controllers;

use mywishlist\models\Item;
use mywishlist\models\Liste;

class ItemController extends Controller {
    public function displayItem($request, $response, $args) {
        $list = Liste::where('token', '=', $args['token'])->first();
        if (is_null($list)) {

        }
        $item = Item::where('id', '=', $args['id'])->first();
        if (is_null($item)) {

        }
        $this->container->view->render($response, 'item.phtml', [
            "list" => $list,
            "item" => $item
        ]);
        return $response;
    }

    public function getNewItem($request, $response, $args) {
        $list = Liste::where('token', '=', $args['token'])->first();
        $this->container->view->render($response, 'newItem.phtml', ["title" => "MyWishList - Nouvel Item", "list" => $list]);
        return $response;
    }

    public function postNewItem($request, $response, $args) {
        $list = Liste::where('token', '=', $args['token'])->first();
        $item = new Item();
        $item->liste_id = $list->num;
        $item->nom = htmlentities(trim($_POST['nom']));
        $item->descr = htmlentities(trim($_POST['descr']));
        $item->img = 'noimage.png';
        if (isset($_POST['url'])){
            $item->url = htmlentities(trim($_POST['url']));
        }
        $item->tarif = htmlentities(trim($_POST['tarif']));
        $item->save();
        return $this->redirect($response, 'list', [
            'token' => $list->token
        ]);
    }
}