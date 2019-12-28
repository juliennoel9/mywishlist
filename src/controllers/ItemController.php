<?php

namespace mywishlist\controllers;

use mywishlist\models\Account;
use mywishlist\models\Item;
use mywishlist\models\Liste;

class ItemController extends Controller {
    public function displayItem($request, $response, $args) {
        $list = Liste::where('token', '=', $args['token'])->first();
        $item = Item::where('id', '=', $args['id'])->first();

        if (isset($_SESSION['login'])){
            $account = Account::where('username', '=', unserialize($_SESSION['login'])['username'])->first();
            $this->container->view->render($response, 'item.phtml', [
                "title" => "MyWishList - Item n°".$item->id,
                "list" => $list,
                "item" => $item,
                "account" => $account
            ]);
            return $response;
        }else{
            $this->container->view->render($response, 'item.phtml', [
                "title" => "MyWishList - Item n°".$item->id,
                "list" => $list,
                "item" => $item
            ]);
            return $response;
        }
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

        $NewFileName = $fileExt[0].'-'.$list->num.'-'.Liste::generateToken().'.'.$fileActualExt;

        $allowed = ['jpg', 'jpeg', 'png'];
        if (in_array($fileActualExt, $allowed)){
            if ($fileError === 0) {
                // 10 Mo
                if ($fileSize < 10000000){
                    $fileDestination = "./public/images/".$NewFileName;
                    move_uploaded_file($fileTmpName, $fileDestination);
                    $item->img = $NewFileName;
                }
            }
        }
        if($item->img != $NewFileName or $item->img == '') {
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

        if ($_POST['submit'] == 'edit'){
            $item->nom = htmlentities(trim($_POST['nom']));
            $item->descr = htmlentities(trim($_POST['description']));
            $item->tarif = htmlentities(trim($_POST['tarif']));

            if (isset($_POST['delete'])){
                $item->img = 'noimage.png';
            }else{
                //Image upload
                $fileName = $_FILES['img']['name'];
                $fileTmpName = $_FILES['img']['tmp_name'];
                $fileSize = $_FILES['img']['size'];
                $fileError = $_FILES['img']['error'];

                $fileExt = explode('.', $fileName);
                $fileActualExt = strtolower(end($fileExt));

                $NewFileName = $fileExt[0].'-'.$list->num.'-'.Liste::generateToken().'.'.$fileActualExt;

                $allowed = ['jpg', 'jpeg', 'png'];
                if (in_array($fileActualExt, $allowed)){
                    if ($fileError === 0) {
                        // 10 Mo
                        if ($fileSize < 10000000){
                            $fileDestination = "./public/images/".$NewFileName;
                            move_uploaded_file($fileTmpName, $fileDestination);
                            $item->img = $NewFileName;
                        }
                    }
                }
            }

            $item->url = isset($_POST['url']) ? htmlentities(trim($_POST['url'])) : '';
            $item->save();
            return $this->redirect($response, 'item', [
                'token' => $list->token,
                'id' => $item->id,
            ]);
        }elseif ($_POST['submit'] == 'delete'){
            $item->delete();
            return $this->redirect($response, 'list', [
                'token' => $list->token
            ]);
        }
    }

    public function postReserveItem($request, $response, $args) {
        $list = Liste::where('token', '=', $args['token'])->first();
        $item = Item::where('id', '=', $args['id'])->first();

        $item->nomReservation = htmlentities(trim($_POST['nom']));
        $item->messageReservation = htmlentities(trim($_POST['message']));

        $item->save();
        return $this->redirect($response, 'item', [
            'token' => $list->token,
            'id' => $item->id,
        ]);
    }
}