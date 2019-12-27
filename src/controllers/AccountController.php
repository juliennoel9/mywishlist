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
            $this->container->view->render($response, 'register.phtml', ["title" => "MyWishList - Inscription", "msg" => "<div class=\"alert alert-danger\">Nom d'utilisateur ou email déjà utilisé, réessayez.</div>"]);
            return $response;
        }
        setcookie("login", serialize(['email' => $account->email, 'username' => $account->username, 'prenom' => $account->prenom, 'nom' => $account->nom]), time()+60*60*24, "/");
        return $this->redirect($response, 'account', ["title" => "MyWishList - Mon compte", "account" => $account]);
    }

    public function getLogin($request, $response, $args) {
        $_SESSION['previousPage'] = $_SERVER['HTTP_REFERER'];
        $this->container->view->render($response, 'login.phtml', ["title" => "MyWishList - Connexion"]);
        return $response;
    }

    public function postLogin($request, $response, $args) {
        $id = htmlentities(trim($_POST['id']));
        $password = htmlentities($_POST['password']);

        $account = Account::where('email', '=', $id)->orwhere('username', '=', $id)->first();

        if (isset($account) and password_verify($password, $account->hash)) {
            setcookie("login", serialize(['email' => $account->email, 'username' => $account->username, 'prenom' => $account->prenom, 'nom' => $account->nom]), time()+60*60*24, "/");

            if (pathinfo($_SESSION['previousPage'])['filename']=='inscription') {
                return $this->redirect($response, 'home');
            }else {
                return $response->withStatus(302)->withHeader('Location', $_SESSION['previousPage']);
            }
        }else {
            $this->container->view->render($response, 'login.phtml', ["title" => "MyWishList - Connexion", "msg" => "<div class=\"alert alert-danger\">Nom d'utilisateur ou mot de passe incorrect, réessayez.</div>"]);
            return $response;
        }
    }

    public function getAccount($request, $response, $args) {
        $account = Account::where('username', '=', unserialize($_COOKIE['login'])['username'])->first();
        $this->container->view->render($response, 'account.phtml', ["title" => "MyWishList - Mon compte", "account" => $account]);
        return $response;
    }

    public function postEditAccount($request, $response, $args) {
        $account = Account::where('username', '=', unserialize($_COOKIE['login'])['username'])->first();
        $account->email = htmlentities(strtolower(trim($_POST['email'])));
        $account->prenom = htmlentities(trim($_POST['prenom']));
        $account->nom = htmlentities(trim($_POST['nom']));
        if ($_POST['submit'] == 'editPassword') {
            if (isset($account) and password_verify(htmlentities($_POST['oldPassword']), $account->hash)){
                $account->hash = password_hash(htmlentities($_POST['newPassword']), PASSWORD_DEFAULT);
            }else {
                $this->container->view->render($response, 'account.phtml', ["title" => "MyWishList - Mon compte", "account" => $account, "msg" => "<div class=\"alert alert-danger\">Ancien mot de passe incorrect, réessayez.</div>"]);
                return $response;
            }
        }
        $account->save();

        if ($_POST['submit'] == 'editPassword'){
            setcookie("login", "", time() - 1000, "/");
            unset($_COOKIE['login']);
            $this->container->view->render($response, 'login.phtml', ["title" => "MyWishList - Mon compte", "account" => $account, "msg" => "<div class=\"alert alert-success\">Le mot de passe a bien été modifié, veuillez vous reconnecter.</div>"]);
            return $response;
        }else {
            setcookie("login", "", time() - 1000, "/");
            unset($_COOKIE['login']);
            setcookie("login", serialize(['email' => $account->email, 'username' => $account->username, 'prenom' => $account->prenom, 'nom' => $account->nom]), time() + 60 * 60 * 24, "/");
            //$this->container->view->render($response, 'account.phtml', ["title" => "MyWishList - Mon compte", "account" => $account, "msg" => "<div class=\"alert alert-success\">Modifications enregistrées.</div>"]);
            //return $response;
            return $this->redirect($response, 'account', ["title" => "MyWishList - Mon compte", "account" => $account, "msg" => "<div class=\"alert alert-success\">Modifications enregistrées.</div>"]);
        }
    }
}