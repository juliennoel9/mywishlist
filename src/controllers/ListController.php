<?php


namespace mywishlist\controllers;


use mysql_xdevapi\Exception;
use mywishlist\models\Liste;

class ListController extends Controller {

    public function displayLists($request, $response, $args) {
        $this->view->render($response, 'publicLists.phtml', ["title" => "MyWishList - Listes", "lists" => Liste::all()]);
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
            $this->view->render($response, 'list.phtml', [
               "list" => $list,
               "items" => $items
            ]);
        } catch (\Exception $e) {

        }
        return $response;
    }

}