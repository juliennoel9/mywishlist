<?php

namespace mywishlist\controllers;

use mywishlist\models\Account;
use Slim\Http\Response;
use Slim\Http\Request;

class AccountController extends Controller {
    public function getRegister(Request $request, Response $response, array $args) {
        $args['title'] = 'MyWishList - Inscription';
        $this->container->view->render($response, 'register.phtml', $args);
        return $response;
    }

    public function postRegister(Request $request, Response $response, array $args) {
        $account = new Account();
        $account->username = htmlentities(trim($_POST['identifiant']));
        $account->email = htmlentities(strtolower(trim($_POST['email'])));
        $account->prenom = htmlentities(trim($_POST['prenom']));
        $account->nom = htmlentities(trim($_POST['nom']));
        $password = htmlentities($_POST['password']);
        $account->hash = password_hash($password, PASSWORD_DEFAULT);
        $account->save();
        $_SESSION['accountCreated']='accountCreated';
        $_SESSION['login'] = serialize(['email' => $account->email, 'username' => $account->username, 'prenom' => $account->prenom, 'nom' => $account->nom]);
        $args['account'] = $account;
        return $this->redirect($response, 'home', $args);
    }

    public function getLogin(Request $request, Response $response, array $args) {
        if (isset($_SERVER['HTTP_REFERER'])) {
            $_SESSION['previousPage'] = $_SERVER['HTTP_REFERER'];
        }
        $args['title'] = 'MyWishList - Connexion';
        $this->container->view->render($response, 'login.phtml', $args);
        return $response;
    }

    public function postLogin(Request $request, Response $response, array $args) {
        $id = htmlentities(trim($_POST['id']));
        $password = htmlentities($_POST['password']);

        $account = Account::where('email', '=', $id)->orwhere('username', '=', $id)->first();

        if (isset($account) and password_verify($password, $account->hash)) {
            $_SESSION['login'] = serialize(['email' => $account->email, 'username' => $account->username, 'prenom' => $account->prenom, 'nom' => $account->nom]);
            if (isset($_SESSION['previousPage'])) {
                if (pathinfo($_SESSION['previousPage'])['filename']=='inscription' or pathinfo($_SESSION['previousPage'])['filename']=='connexion') {
                    return $this->redirect($response, 'home');
                } else {
                    return $response->withStatus(302)->withHeader('Location', $_SESSION['previousPage']);
                }
            } else {
                return $this->redirect($response, 'home');
            }
        } else {
            $args['title'] = 'MyWishList - Connexion';
            $args['msg'] = "<div class=\"alert alert-danger\">Nom d'utilisateur ou mot de passe incorrect, réessayez.</div>";
            $args['id'] = $id;
            $this->container->view->render($response, 'login.phtml', $args);
            return $response;
        }
    }

    public function getAccount(Request $request, Response $response, array $args) {
        if (isset($_SESSION['login'])) {
            $account = Account::where('username', '=', unserialize($_SESSION['login'])['username'])->first();
            $args['account'] = $account;
        }
        $args['title'] = 'MyWishList - Mon compte';
        $this->container->view->render($response, 'account.phtml', $args);
        return $response;
    }

    public function postEditAccount(Request $request, Response $response, array $args) {
        $account = Account::where('username', '=', unserialize($_SESSION['login'])['username'])->first();
        $args['account'] = $account;
        if ($_POST['submit'] == 'deleteAccount') {
            $account->delete();
            unset($_SESSION['login']);
            return $this->redirect($response, 'home');
        } else {
            $account->email = htmlentities(strtolower(trim($_POST['email'])));
            $account->prenom = htmlentities(trim($_POST['prenom']));
            $account->nom = htmlentities(trim($_POST['nom']));
            if ($_POST['submit'] == 'editPassword') {
                if (isset($account) and password_verify(htmlentities($_POST['oldPassword']), $account->hash)) {
                    $account->hash = password_hash(htmlentities($_POST['newPassword']), PASSWORD_DEFAULT);
                } else {
                    $args['title'] = 'MyWishList - Mon compte';
                    $args['msg'] = '<div class="alert alert-danger">Ancien mot de passe incorrect, réessayez.</div>';
                    $this->container->view->render($response, 'account.phtml', $args);
                    return $response;
                }
            }
            $account->save();

            if ($_POST['submit'] == 'editPassword') {
                unset($_SESSION['login']);
                $args['title'] = 'MyWishList - Connexion';
                $args['msg'] = '<div class="alert alert-success">Le mot de passe a bien été modifié, veuillez vous reconnecter.</div>';
                return $this->container->view->render($response, 'login.phtml', $args);
            } else {
                unset($_SESSION['login']);
                $_SESSION['login'] = serialize(['email' => $account->email, 'username' => $account->username, 'prenom' => $account->prenom, 'nom' => $account->nom]);
                $args['title'] = 'MyWishList - Mon compte';
                $args['msg'] = '<div class="alert alert-success">Modifications enregistrées.</div>';
                return $this->redirect($response, 'account', $args);
            }
        }
    }

    public function getLogout(Request $request, Response $response, array $args) {
        unset($_SESSION['login']);
        session_destroy();
        return $this->redirect($response, 'home');
    }

}