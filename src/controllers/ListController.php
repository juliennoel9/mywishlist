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
            $list = Liste::where('num', '=', $args['num'])->first();
            if (is_null($list)) {
                throw new \Exception("Aucune liste correspondante");
            }
            $items = $list->items()->get();
            if (is_null($items)) {
                throw new \Exception("Aucun item dans la liste");
            }
            $this->container->view->render($response, 'list.phtml', [
               "list" => $list,
               "items" => $items
            ]);
        } catch (\Exception $e) {
            $response->write($e->getMessage());
        }
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
        $endPath = '?token='.$newList->token;

        return $this->redirect($response, 'list', [
            'num' => $newList->num,
            'endPath' => $endPath
        ]);
    }

    public function getEditList($request, $response, $args) {
        try {
            $list = Liste::where('token', '=', $args['token'])->first();
            $this->container->view->render($response, 'editList.phtml', ["title" => "MyWishList - Modification Liste", "list" => $list]);
        } catch (\Exception $e) {
            $response->write($e->getMessage());
        }
        return $response;
    }

    public function postEditList($request, $response, $args) {
        try {
            $list = Liste::where('token', '=', $args['token'])->first();
            $list->titre = htmlentities($_POST['titre']);
            $list->description = htmlentities($_POST['description']);
            $list->expiration = htmlentities($_POST['expiration']);
            $list->public = isset($_POST['public']) ? 1 : 0;
            $list->save();
        } catch (\Exception $e) {
            $response->write($e->getMessage());
        }
        $endPath = '?token='.$list->token;
        return $this->redirect($response, 'list', [
            'num' => $list->num,
            'endPath' => $endPath
        ]);
    }
}