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

        //Image upload
        $fileName = $_FILES['img']['name'];
        $fileTmpName = $_FILES['img']['tmp_name'];
        $fileSize = $_FILES['img']['size'];
        $fileError = $_FILES['img']['error'];

        $fileExt = explode('.', $fileName);
        $fileActualExt = strtolower(end($fileExt));

        $allowed = ['jpg', 'jpeg', 'png'];
        if (in_array($fileActualExt, $allowed)){
            if ($fileError === 0) {
                // 10 Mo
                if ($fileSize < 10000000){
                    $fileDestination = "./public/images/".$fileName;
                    move_uploaded_file($fileTmpName, $fileDestination);
                    $item->img = $fileName;
                }
            }
        }
        if($item->img != $fileName or $item->img == '') {
            $item->img = 'noimage.png';
        }

        if (isset($_POST['url'])){
            $item->url = htmlentities(trim($_POST['url']));
        }
        $item->tarif = htmlentities(trim($_POST['tarif']));
        $item->save();
        return $this->redirect($response, 'list', [
            'token' => $list->token
        ]);
    }

    public function getEditItem($request, $response, $args) {
        $list = Liste::where('token', '=', $args['token'])->first();
        $item = Item::where('id', '=', $args['id'])->first();
        $this->container->view->render($response, 'editItem.phtml', ["title" => "MyWishList - Modification Item", "list" => $list, "item" => $item]);
        return $response;
    }

    public function postEditItem($request, $response, $args) {
        $list = Liste::where('token', '=', $args['token'])->first();
        $item = Item::where('id', '=', $args['id'])->first();
        $item->nom = htmlentities(trim($_POST['nom']));
        $item->descr = htmlentities(trim($_POST['description']));

        //Image upload
        $fileName = $_FILES['img']['name'];
        $fileTmpName = $_FILES['img']['tmp_name'];
        $fileSize = $_FILES['img']['size'];
        $fileError = $_FILES['img']['error'];

        $fileExt = explode('.', $fileName);
        $fileActualExt = strtolower(end($fileExt));

        $allowed = ['jpg', 'jpeg', 'png'];
        if (in_array($fileActualExt, $allowed)){
            if ($fileError === 0) {
                // 10 Mo
                if ($fileSize < 10000000){
                    $fileDestination = "./public/images/".$fileName;
                    move_uploaded_file($fileTmpName, $fileDestination);
                    $item->img = $fileName;
                }
            }
        }
        if($item->img != $fileName) {
            $item->img = 'noimage.png';
        }

        $item->url = isset($_POST['url']) ? htmlentities(trim($_POST['url'])) : '';
        $item->save();
        return $this->redirect($response, 'item', [
            'token' => $list->token,
            'id' => $item->id,
        ]);
    }
}