<?php

namespace mywishlist\controllers;

use mywishlist\models\Liste;

class ListController extends Controller {

    public function displayPublicLists($request, $response, $args) {
        $this->container->view->render($response, 'publicLists.phtml', ["title" => "MyWishList - Listes", "lists" => Liste::all()]);
        return $response;
    }

    public function displayList($request, $response, $args) {
        $list = Liste::where('token', '=', $args['token'])->first();
        if (is_null($list)) {

        }
        $items = $list->items()->get();
        if (is_null($items)) {

        }
        $this->container->view->render($response, 'list.phtml', [
           "list" => $list,
           "items" => $items
        ]);
        return $response;
    }

    public function getNewList($request, $response, $args) {
        $this->container->view->render($response, 'newList.phtml', ["title" => "MyWishList - Nouvelle Liste"]);
        return $response;
    }

    public function postNewList($request, $response, $args) {
        $list = new Liste();
        $list->user_id = null;
        $list->titre = htmlentities($_POST['titre']);
        $list->description = htmlentities($_POST['description']);
        $list->expiration = htmlentities($_POST['expiration']);
        $token = Liste::generateToken();
        $list->token = $token;
        $list->public = isset($_POST['public']) ? 1 : 0;
        $list->save();

        $newList = Liste::where('token', '=', $token)->first();

        return $this->redirect($response, 'list', [
            'token' => $newList->token
        ]);
    }

    public function getEditList($request, $response, $args) {
        $list = Liste::where('token', '=', $args['token'])->first();
        $this->container->view->render($response, 'editList.phtml', ["title" => "MyWishList - Modification Liste", "list" => $list]);
        return $response;
    }

    public function postEditList($request, $response, $args) {
        $list = Liste::where('token', '=', $args['token'])->first();
        $list->titre = htmlentities($_POST['titre']);
        $list->description = htmlentities($_POST['description']);
        $list->expiration = htmlentities($_POST['expiration']);
        $list->public = isset($_POST['public']) ? 1 : 0;
        $list->save();
        return $this->redirect($response, 'list', [
            'token' => $list->token
        ]);
    }
}