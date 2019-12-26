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
        $account->username = htmlentities(trim($_POST['identifiant']));
        $account->email = htmlentities(strtolower(trim($_POST['email'])));
        $account->prenom = htmlentities(trim($_POST['prenom']));
        $account->nom = htmlentities(trim($_POST['nom']));

        $accountTest = Account::where('email', '=', $account->email)->orwhere('username', '=', $account->username)->first();
        if (empty($accountTest)) {
            $password = htmlentities($_POST['password']);
            $account->hash = password_hash($password, PASSWORD_DEFAULT);
            $account->save();
        } else {
            $this->container->view->render($response, 'register.phtml', ["title" => "MyWishList - Inscription", "msg" => "Identifiant ou email déjà utilisé, réessayez."]);
            return $response;
        }
        setcookie("login", serialize(['email' => $account->email, 'username' => $account->username]), time()+60*60*24, "/");
        return $this->redirect($response, 'home');
    }

    public function getLogin($request, $response, $args) {
        $this->container->view->render($response, 'login.phtml', ["title" => "MyWishList - Connexion"]);
        return $response;
    }

    public function postLogin($request, $response, $args) {
        $id = htmlentities(trim($_POST['id']));
        $password = htmlentities($_POST['password']);

        $account = Account::where('email', '=', $id)->orwhere('username', '=', $id)->first();

        if (isset($account) and password_verify($password, $account->hash)) {
            setcookie("login", serialize(['email' => $account->email, 'username' => $account->username]), time()+60*60*24, "/");
            return $this->redirect($response, 'home');
        }else {
            $this->container->view->render($response, 'login.phtml', ["title" => "MyWishList - Connexion", "msg" => "Identifiant ou mot de passe incorrect, réessayez."]);
            return $response;
        }
    }
}