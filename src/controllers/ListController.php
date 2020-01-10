<?php

namespace mywishlist\controllers;

use mywishlist\models\Account;
use mywishlist\models\Liste;
use mywishlist\models\Message;
use Slim\Exception\NotFoundException;
use Slim\Http\Response;
use Slim\Http\Request;

class ListController extends Controller {

    public function displayPublicLists(Request $request, Response $response, array $args) {
        $lists = Liste::select('*')->where('expiration', '>=', date('Y-m-d H:i:s', strtotime("-1 days")))->where('public', true)->orderBy('expiration', 'asc')->get();;
        $this->container->view->render($response, 'publicLists.phtml', ["title" => "MyWishList - Listes", "lists" => $lists]);
        return $response;
    }

    public function displayList(Request $request, Response $response, array $args) {
        $list = Liste::where('token', '=', $args['token'])->first();
        if (is_null($list))
            throw new NotFoundException($request, $response);
        $items = $list->items();
        $messages = $list->messages();
        if (isset($_SESSION['login'])) {
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

    public function getNewList(Request $request, Response $response, array $args) {
        $this->container->view->render($response, 'newList.phtml', ["title" => "MyWishList - Nouvelle Liste"]);
        return $response;
    }

    public function postNewList(Request $request, Response $response, array $args) {
        $list = new Liste();
        if (isset($_SESSION['login'])) {
            $account = Account::where('username', '=', unserialize($_SESSION['login'])['username'])->first();
            $list->user_id = $account->id;
        } else {
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

    public function getEditList(Request $request, Response $response, array $args) {
        if (isset($_SESSION['login'])) {
            $account = Account::where('username', '=', unserialize($_SESSION['login'])['username'])->first();
            $list = Liste::where('token', '=', $args['token'])->first();
            $this->container->view->render($response, 'editList.phtml', ["title" => "MyWishList - Modification Liste", "list" => $list, "account" => $account]);
            return $response;
        } else {
            $list = Liste::where('token', '=', $args['token'])->first();
            $this->container->view->render($response, 'editList.phtml', ["title" => "MyWishList - Modification Liste", "list" => $list]);
            return $response;
        }
    }

    public function postEditList(Request $request, Response $response, array $args) {
        $list = Liste::where('token', '=', $args['token'])->first();
        if (strtotime($list->expiration) > strtotime("-1 days")) {
            $list->titre = htmlentities(trim($_POST['titre']));
            $list->description = htmlentities(trim($_POST['description']));
            $list->expiration = htmlentities($_POST['expiration']);
            $list->public = isset($_POST['public']) ? 1 : 0;
            $list->save();
        } else {
            $list->public = isset($_POST['public']) ? 1 : 0;
            $list->save();
        }
        return $this->redirect($response, 'list', [
            'token' => $list->token
        ]);
    }

    public function postNewListMessage(Request $request, Response $response, array $args) {
        $list = Liste::where('token', '=', $args['token'])->first();
        $message = new Message();
        $message->liste_id = $list->num;
        if (isset($_SESSION['login'])) {
            $account = Account::where('username', '=', unserialize($_SESSION['login'])['username'])->first();
            $message->account_id = $account->id;
        }
        $message->nomMessage = htmlentities(trim($_POST['nom']));
        $message->message = htmlentities(trim($_POST['message']));
        $message->date = date('Y-m-d H:i:s');
        $message->save();
        return $this->redirect($response, 'list', [
            'token' => $list->token
        ]);
    }

    public function displayAccountLists(Request $request, Response $response, array $args) {
        if (isset($_SESSION['login'])) {
            $account = Account::where('username', '=', unserialize($_SESSION['login'])['username'])->first();
            $lists = Liste::where('user_id', '=', $account->id)->orderBy('expiration', 'asc')->get();

            $this->container->view->render($response, 'accountLists.phtml', [
                "title" => 'MyWishList - Mes listes',
                "lists" => $lists,
                "account" => $account
            ]);
            return $response;
        } else {
            $this->container->view->render($response, 'accountLists.phtml', [
                "title" => 'MyWishList - Mes listes'
            ]);
            return $response;
        }
    }

    public function displayCreators(Request $request, Response $response, array $args) {
        $lists = Liste::select('*')->where('expiration', '>=', date('Y-m-d H:i:s', strtotime("-1 days")))->orderBy('expiration', 'asc')->where('public', '=', 1)->get();
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

    public function displayCreatorPublicLists(Request $request, Response $response, array $args) {
        $account = Account::where('username', '=', $args['creator'])->first();
        if ($account != null) {
            $lists = Liste::select('*')->where('expiration', '>=', date('Y-m-d H:i:s', strtotime("-1 days")))->orderBy('expiration', 'asc')->where('user_id', '=', $account->id)->where('public', '=', 1)->get();
            $this->container->view->render($response, 'creatorPublicLists.phtml', ["title" => "MyWishList - Listes publiques de ".$account->username, "lists" => $lists, "account" => $account]);
            return $response;
        } else {
            $this->container->view->render($response, 'creatorPublicLists.phtml', ["title" => "MyWishList - Listes publiques", "account" => $account]);
            return $response;
        }
    }
}