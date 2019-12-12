<?php


namespace mywishlist\controllers;


use mysql_xdevapi\Exception;
use mywishlist\models\Liste;

class ListController extends Controller {

    public function displayLists($request, $response, $args) {
        $this->container->view->render($response, 'publicLists.phtml', ["title" => "MyWishList - Listes", "lists" => Liste::all()]);
        return $response;
    }

    public function getList($request, $response, $args) {
        try {
            $list = Liste::where('token', '=', $args['token'])->first();
            if (is_null($list)) {
                throw new Exception("Aucune liste correspondante");
            }
            $items = $list->items()->get();
            if (is_null($items)) {
                throw new Exception("Aucun item dans la liste");
            }
            $this->container->view->render($response, 'list.phtml', [
               "list" => $list,
               "items" => $items
            ]);
        } catch (\Exception $e) {

        }
        return $response;
    }

    public function getNewList($request, $response, $args) {
        $this->container->view->render($response, 'newList.phtml', ["title" => "MyWishList - Nouvelle Liste"]);
        return $response;
    }

    public function postNewList($request, $response, $args) {
        $list = new Liste();
        $list->user_id = $_POST['idUser'];
        $list->titre = $_POST['titre'];
        $list->description = $_POST['description'];
        $list->expiration = $_POST['expiration'];
        $list->token = Liste::generateToken();
        $list->public = 1;
        $list->save();

        //$this->view->render($response, 'home.phtml', ["title" => "MyWishList - Accueil"]);
        //return $response;
        return $this->redirect($response, 'home');
    }
}