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
        $lists = Liste::select('*')->where('expiration', '>=', date('Y-m-d H:i:s', strtotime("-1 days")))->where('public', true)->orderBy('expiration', 'asc')->get();
        $args['title'] = 'MyWishList - Listes';
        $args['lists'] = $lists;
        $this->container->view->render($response, 'publicLists.phtml', $args);
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
            $args['account'] = $account;
        }
        $args['title'] = "MyWishList - Liste n°$list->num";
        $args['list'] = $list;
        $args['items'] = $items;
        $args['messages'] = $messages;
        $this->container->view->render($response, 'list.phtml', $args);
        return $response;
    }

    public function getNewList(Request $request, Response $response, array $args) {
        $args['title'] = 'MyWishList - Nouvelle Liste';
        $this->container->view->render($response, 'newList.phtml', $args);
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
        $list->expiration = date("Y-m-d", strtotime($_POST['expiration']));
        $token = Liste::generateToken();
        $list->token = $token;
        $list->public = isset($_POST['public']) ? 1 : 0;
        $list->save();

        $newList = Liste::where('token', '=', $token)->first();
        $args['token'] = $newList->token;
        return $this->redirect($response, 'list', $args);
    }

    public function getEditList(Request $request, Response $response, array $args) {
        $list = Liste::where('token', '=', $args['token'])->first();
        $items = $list->items();
        if (isset($_SESSION['login'])) {
            $account = Account::where('username', '=', unserialize($_SESSION['login'])['username'])->first();
            $args['account'] = $account;
        }
        $args['title'] = 'Modification Liste';
        $args['list'] = $list;
        $args['items'] = $items;
        $this->container->view->render($response, 'editList.phtml', $args);
        return $response;
    }

    public function postEditList(Request $request, Response $response, array $args) {
        $list = Liste::where('token', '=', $args['token'])->first();
        if ($_POST['submit'] == 'edit') {
            if (strtotime($list->expiration) > strtotime("-1 days")) {
                $list->titre = htmlentities(trim($_POST['titre']));
                $list->description = htmlentities(trim($_POST['description']));
                $list->expiration = date("Y-m-d", strtotime($_POST['expiration']));
                $list->public = isset($_POST['public']) ? 1 : 0;
                $list->save();
            } else {
                $list->public = isset($_POST['public']) ? 1 : 0;
                $list->save();
            }
            $args['token'] = $list->token;
            return $this->redirect($response, 'list', $args);
        } elseif ($_POST['submit'] == 'delete') {
            $list->delete();
            return $this->redirect($response, 'accountLists', $args);
        }
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
        $args['token'] = $list->token;
        return $this->redirect($response, 'list', $args);
    }

    public function displayAccountLists(Request $request, Response $response, array $args) {
        if (isset($_SESSION['login'])) {
            $account = Account::where('username', '=', unserialize($_SESSION['login'])['username'])->first();
            $lists = Liste::where('user_id', '=', $account->id)->orderBy('expiration', 'asc')->get();
            $args['account'] = $account;
            $args['lists'] = $lists;

        }
        $args['title'] = 'MyWishList - Mes listes';
        $this->container->view->render($response, 'accountLists.phtml', $args);
        return $response;
    }

    public function displayCreators(Request $request, Response $response, array $args) {
        $lists = Liste::select('*')->where('expiration', '>=', date('Y-m-d H:i:s', strtotime("-1 days")))->orderBy('expiration', 'asc')->where('public', '=', 1)->get();
        $accounts = array();
        foreach ($lists as $list) {
            $account = Account::where('id', '=', $list->user_id)->first();
            $accounts += [$account->username => $account];
        }
        $args['title'] = 'MyWishList - Créateurs';
        $args['lists'] = $lists;
        $args['accounts'] = $accounts;
        $this->container->view->render($response, 'creators.phtml', $args);
        return $response;
    }

    public function displayCreatorPublicLists(Request $request, Response $response, array $args) {
        $account = Account::where('username', '=', $args['creator'])->first();
        $args['account'] = $account;
        if ($account != null) {
            $lists = Liste::select('*')->where('expiration', '>=', date('Y-m-d H:i:s', strtotime("-1 days")))->orderBy('expiration', 'asc')->where('user_id', '=', $account->id)->where('public', '=', 1)->get();
            $args['title'] = "MyWishList - Listes publiques de $account->username";
            $args['lists'] = $lists;
        } else {
            $args['title'] = 'MyWishList - Listes publiques';
        }
        $this->container->view->render($response, 'creatorPublicLists.phtml', $args);
        return $response;
    }
}