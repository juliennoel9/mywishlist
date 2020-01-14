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
        $account->username = trim($_POST['identifiant']);
        $account->email = strtolower(trim($_POST['email']));
        $account->prenom = trim($_POST['prenom']);
        $account->nom = trim($_POST['nom']);
        $account->hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
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
        $id = trim($_POST['id']);
        $account = Account::where('email', '=', $id)->orwhere('username', '=', $id)->first();

        if (isset($account) and password_verify($_POST['password'], $account->hash)) {
            $_SESSION['login'] = serialize(['email' => $account->email, 'username' => $account->username, 'prenom' => $account->prenom, 'nom' => $account->nom]);
            $_SESSION['user'] = $account->toArray();

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
            $_SESSION['redirect']['msg'] = '<div class="alert alert-danger">Nom d\'utilisateur ou mot de passe incorrect, réessayez.</div>';
            $_SESSION['redirect']['username'] = $account->username;
            return $this->redirect($response, 'login');
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
        if ($account->email != $_POST['email'] || $account->prenom != $_POST['prenom'] || $account->nom != $_POST['nom']) {
            $account->email = strtolower(trim($_POST['email']));
            $account->prenom = trim($_POST['prenom']);
            $account->nom = trim($_POST['nom']);
            $account->save();
            unset($_SESSION['login']);
            $_SESSION['login'] = serialize(['email' => $account->email, 'username' => $account->username, 'prenom' => $account->prenom, 'nom' => $account->nom]);
            $_SESSION['redirect']['msg'] = '<div class="alert alert-success">Les modifications ont bien été enregistrées.</div>';
        }
        return $this->redirect($response, 'account');
    }

    public function postChangePassword(Request $request, Response $response, array $args) {
        $account = Account::where('username', '=', unserialize($_SESSION['login'])['username'])->first();
        if (password_verify($_POST['oldPassword'], $account->hash)) {
            $account->hash = password_hash($_POST['newPassword'], PASSWORD_DEFAULT);
            $account->save();
            unset($_SESSION['login']);
            $_SESSION['redirect']['msg'] = '<div class="alert alert-success">Le mot de passe a bien été modifié, veuillez vous reconnecter.</div>';
            $_SESSION['redirect']['username'] = $account->username;
            return $this->redirect($response, 'login');
        } else {
            $_SESSION['redirect']['msg'] = '<div class="alert alert-danger">Ancien mot de passe incorrect, réessayez.</div>';
            return $this->redirect($response, 'account');
        }
    }

    public function postDeleteAccount(Request $request, Response $response, array $args) {
        $account = Account::where('username', '=', unserialize($_SESSION['login'])['username'])->first();
        if (password_verify($_POST['password'], $account->hash)) {
            $account->delete();
            unset($_SESSION['login']);
            $_SESSION['redirect']['msg'] = '<div class="alert alert-success">Votre compte a bien été supprimé.</div>';
            return $this->redirect($response, 'home');
        } else {
            $_SESSION['redirect']['msg'] = '<div class="alert alert-danger">Mot de passe incorrect, réessayez.</div>';
            return $this->redirect($response, 'account');
        }
    }

    public function getLogout(Request $request, Response $response, array $args) {
        unset($_SESSION['login']);
        unset($_SESSION['user']);
        session_destroy();
        return $this->redirect($response, 'home');
    }

}