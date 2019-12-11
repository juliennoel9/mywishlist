<?php


namespace mywishlist\controllers;


use mysql_xdevapi\Exception;
use mywishlist\models\Liste;

class ListeController extends Controller {

    public function displayLists($request, $response, $args) {
        $this->view->render($response, 'listes.phtml', ["title" => "MyWishList - Listes", "listes" => Liste::all()]);
        return $response;
    }

    public function getListe($request, $response, $args) {
        try {
            $liste = Liste::where('token', '=', $args['token'])->first();
            if (is_null($liste)) {
                throw new Exception("Aucune liste correspondante");
            }
            $items = $liste->items()->get();
            if (is_null($items)) {
                throw new Exception("Aucun item dans la liste");
            }
            $this->view->render($response, 'liste.phtml', [
               "liste" => $liste,
               "items" => $items
            ]);
        } catch (Exception $e) {

        }
        return $response;
    }
}