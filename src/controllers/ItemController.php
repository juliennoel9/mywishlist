<?php


namespace mywishlist\controllers;


use mysql_xdevapi\Exception;
use mywishlist\models\Item;
use mywishlist\models\Liste;

class ItemController extends Controller {
    public function displayItem($request, $response, $args) {
        try {
            $list = Liste::where('token', '=', $args['token'])->first();
            if (is_null($list)) {
                throw new Exception("Aucune liste correspondante");
            }
            $item = Item::where('id', '=', $args['id'])->first();
            if (is_null($item)) {
                throw new Exception("Aucun item correspondant dans la liste");
            }
            $this->container->view->render($response, 'item.phtml', [
                "list" => $list,
                "item" => $item
            ]);
        } catch (\Exception $e) {

        }
        return $response;
    }
}