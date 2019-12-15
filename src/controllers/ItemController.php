<?php


namespace mywishlist\controllers;


use mysql_xdevapi\Exception;
use mywishlist\models\Item;
use mywishlist\models\Liste;

class ItemController extends Controller {
    public function displayItem($request, $response, $args) {
        try {
            $list = Liste::where('num', '=', $args['num'])->first();
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

    public function getNewItem($request, $response, $args) {
        $list = Liste::where('token', '=', $args['token'])->first();
        $this->container->view->render($response, 'newItem.phtml', ["title" => "MyWishList - Nouvel Item", "list" => $list]);
        return $response;
    }

    public function postNewItem($request, $response, $args) {
        $list = Liste::where('token', '=', $args['token'])->first();
        $item = new Item();
        $item->liste_id = $list->num;
        $item->nom = htmlentities($_POST['nom']);
        $item->descr = htmlentities($_POST['descr']);
        if (isset($_POST['url'])){
            $item->url = htmlentities($_POST['url']);
        }
        $item->tarif = htmlentities($_POST['tarif']);
        $item->save();
        return $this->redirect($response, 'home');
    }
}