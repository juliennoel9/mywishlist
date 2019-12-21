<?php


namespace mywishlist\controllers;


use mywishlist\models\Account;

class AccountController extends Controller {
    public function getRegister($request, $response, $args) {
        $this->container->view->render($response, 'register.phtml', ["title" => "MyWishList - Inscription"]);
        return $response;
    }

    public function postRegister($request, $response, $args) {
        $account = new Account();
        $account->id = htmlentities($_POST['identifiant']);
        $account->email = htmlentities($_POST['email']);
        $account->password = htmlentities($_POST['password']);
        $account->save();

        return $this->redirect($response, 'home');
    }
}