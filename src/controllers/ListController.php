<?php

namespace mywishlist\controllers;

use mywishlist\models\Account;
use mywishlist\models\Liste;
use mywishlist\models\Message;

class ListController extends Controller {

    public function displayPublicLists($request, $response, $args) {
        $lists = Liste::select('*')->where('expiration', '>=', date('Y-m-d H:i:s', time()+3600))->orderBy('expiration', 'asc')->get();;
        $this->container->view->render($response, 'publicLists.phtml', ["title" => "MyWishList - Listes", "lists" => $lists]);
        return $response;
    }

    public function displayList($request, $response, $args) {
        $list = Liste::where('token', '=', $args['token'])->first();
        $items = $list->items();
        $messages = $list->messages();
        if (isset($_SESSION['login'])){
            $account = Account::where('username', '=', unserialize($_SESSION['login'])['username'])->first();
            $this->container->view->render($response, 'list.phtml', [
                "title" => 'MyWishList - Liste n°'.$list->num,
                "list" => $list,
                "items" => $items,
                "messages" => $messages,
                "account" => $account
            ]);
            return $response;
        } else {
            $this->container->view->render($response, 'list.phtml', [
                "title" => 'MyWishList - Liste n°'.$list->num,
                "list" => $list,
                "items" => $items,
                "messages" => $messages
            ]);
            return $response;
        }
    }

    public function getNewList($request, $response, $args) {
        $this->container->view->render($response, 'newList.phtml', ["title" => "MyWishList - Nouvelle Liste"]);
        return $response;
    }

    public function postNewList($request, $response, $args) {
        $list = new Liste();
        if (isset($_SESSION['login'])){
            $account = Account::where('username', '=', unserialize($_SESSION['login'])['username'])->first();
            $list->user_id = $account->id;
        }else {
            $list->user_id = null;
        }
        $list->titre = htmlentities(trim($_POST['titre']));
        $list->description = htmlentities(trim($_POST['description']));
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
        $list->titre = htmlentities(trim($_POST['titre']));
        $list->description = htmlentities(trim($_POST['description']));
        $list->expiration = htmlentities($_POST['expiration']);
        $list->public = isset($_POST['public']) ? 1 : 0;
        $list->save();
        return $this->redirect($response, 'list', [
            'token' => $list->token
        ]);
    }

    public function postNewListMessage($request, $response, $args) {
        $list = Liste::where('token', '=', $args['token'])->first();
        $message = new Message();
        $message->liste_id = $list->num;
        if (isset($_SESSION['login'])){
            $account = Account::where('username', '=', unserialize($_SESSION['login'])['username'])->first();
            $message->account_id = $account->id;
        }
        $message->nomMessage = htmlentities(trim($_POST['nom']));
        $message->message = htmlentities(trim($_POST['message']));
        date_default_timezone_set('Europe/Paris');
        $message->date = date('Y-m-d H:i:s');
        $message->save();
        return $this->redirect($response, 'list', [
            'token' => $list->token
        ]);
    }

    public function displayAccountLists($request, $response, $args) {
        $account = Account::where('username', '=', unserialize($_SESSION['login'])['username'])->first();
        $lists = Liste::where('user_id', '=', $account->id)->orderBy('expiration', 'asc')->get();

        $this->container->view->render($response, 'accountLists.phtml', [
            "title" => 'MyWishList - Mes listes',
            "lists" => $lists
        ]);
        return $response;
    }

    public function displayCreators($request, $response, $args) {
        $lists = Liste::select('*')->where('expiration', '>=', date('Y-m-d H:i:s', time()+3600))->orderBy('expiration', 'asc')->where('public', '=', 1)->get();
        $accounts = array();
        foreach ($lists as $list) {
            $account = Account::where('id', '=', $list->user_id)->first();
            $accounts += [$account->username => $account];
        }

        $this->container->view->render($response, 'creators.phtml', [
            "title" => 'MyWishList - Créateurs',
            "lists" => $lists,
            "accounts" => $accounts
        ]);
        return $response;
    }

    public function displayCreatorPublicLists($request, $response, $args) {
        $account = Account::where('username', '=', $args['creator'])->first();
        $lists = Liste::select('*')->where('expiration', '>=', date('Y-m-d H:i:s', time()+3600))->orderBy('expiration', 'asc')->where('user_id', '=', $account->id)->get();
        $this->container->view->render($response, 'creatorPublicLists.phtml', ["title" => "MyWishList - Listes", "lists" => $lists, "account" => $account]);
        return $response;
    }
}