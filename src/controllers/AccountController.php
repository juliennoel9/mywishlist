<?php

namespace mywishlist\controllers;

use mywishlist\models\Account;
use mywishlist\models\Message;
use Slim\Http\Response;
use Slim\Http\Request;

class AccountController extends Controller {
    public function getRegister(Request $request, Response $response, array $args) {
        $this->container->view->render($response, 'register.phtml', ["title" => "MyWishList - Inscription"]);
        return $response;
    }

    public function postRegister(Request $request, Response $response, array $args) {
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
        $_SESSION['login'] = serialize(['email' => $account->email, 'username' => $account->username, 'prenom' => $account->prenom, 'nom' => $account->nom]);
        return $this->redirect($response, 'account', ["account" => $account]);
    }

    public function getLogin(Request $request, Response $response, array $args) {
        if (isset($_SERVER['HTTP_REFERER'])) {
            $_SESSION['previousPage'] = $_SERVER['HTTP_REFERER'];
        }
        $this->container->view->render($response, 'login.phtml', ["title" => "MyWishList - Connexion"]);
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
            $this->container->view->render($response, 'login.phtml', ["title" => "MyWishList - Connexion", "msg" => "<div class=\"alert alert-danger\">Nom d'utilisateur ou mot de passe incorrect, réessayez.</div>", "id" => $id]);
            return $response;
        }
    }

    public function getAccount(Request $request, Response $response, array $args) {
        if (isset($_SESSION['login'])) {
            $account = Account::where('username', '=', unserialize($_SESSION['login'])['username'])->first();
            $this->container->view->render($response, 'account.phtml', ["title" => "MyWishList - Mon compte", "account" => $account]);
            return $response;
        } else {
            $this->container->view->render($response, 'account.phtml', ["title" => "MyWishList - Mon compte"]);
            return $response;
        }

    }

    public function postEditAccount(Request $request, Response $response, array $args) {
        $account = Account::where('username', '=', unserialize($_SESSION['login'])['username'])->first();
        if ($_POST['submit'] == 'deleteAccount') {
            $lists = $account->lists();
            foreach ($lists as $list) {
                $items = $list->items();
                foreach ($items as $item) {
                    $item->delete();
                }
                $messages = $list->messages();
                foreach ($messages as $message) {
                    $message->delete();
                }
                $list->delete();
            }
            $allMessages = Message::all();
            foreach ($allMessages as $message) {
                if ($message->account_id == $account->id) {
                    $message->account_id = null;
                    $message->save();
                }
            }
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
                    $this->container->view->render($response, 'account.phtml', ["title" => "MyWishList - Mon compte", "account" => $account, "msg" => "<div class=\"alert alert-danger\">Ancien mot de passe incorrect, réessayez.</div>"]);
                    return $response;
                }
            }
            $account->save();

            if ($_POST['submit'] == 'editPassword') {
                unset($_SESSION['login']);
                $this->container->view->render($response, 'login.phtml', ["title" => "MyWishList - Mon compte", "account" => $account, "msg" => "<div class=\"alert alert-success\">Le mot de passe a bien été modifié, veuillez vous reconnecter.</div>"]);
                return $response;
            } else {
                unset($_SESSION['login']);
                $_SESSION['login'] = serialize(['email' => $account->email, 'username' => $account->username, 'prenom' => $account->prenom, 'nom' => $account->nom]);
                return $this->redirect($response, 'account', ["title" => "MyWishList - Mon compte", "account" => $account, "msg" => "<div class=\"alert alert-success\">Modifications enregistrées.</div>"]);
            }
        }
    }

    public function getLogout(Request $request, Response $response, array $args) {
        unset($_SESSION['login']);
        session_destroy();
        return $this->redirect($response, 'home');
    }

    public function liveCheckUsername($request, $response, array $args) {
        $response = $response->withHeader('Content-type', 'application/json');
        $res = ['valide' => true, 'msg' => ''];
        if (isset($_GET['username'])) {
            $username = trim($_GET['username']);
            if ($this->fullMatch('/[a-zA-Z0-9_-]{1,20}/', $username)) {
                $user = Account::select('username')->where('username', '=', $username)->first();
                if ($user != null) {
                    $res['valide'] = false;
                    $res['msg'] = 'Ce nom d\'utilisateur est déjà utilisé.';
                }
            } else {
                $res['valide'] = false;
                $res['msg'] = 'Utilisez uniquement des lettres (sans accents), des chiffres, tirets et underscores.';
            }
        } else {
            $res['valide'] = false;
        }
        $response->write(json_encode($res));
        return $response;
    }

    public function liveCheckEmail($request, $response, array $args) {
        $response = $response->withHeader('Content-type', 'application/json');
        $res = ['valide' => true, 'msg' => ''];
        if (isset($_GET['email'])) {
            $email = trim($_GET['email']);
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $user = Account::select('email')->where('email', '=', $email)->first();
                if ($user != null) {
                    $res['valide'] = false;
                    $res['msg'] = 'Email déjà utilisé.';
                }
            } else {
                $res['valide'] = false;
                $res['msg'] = 'Cet email n\'est pas valide.';
            }
        } else {
            $res['valide'] = false;
        }
        $response->write(json_encode($res));
        return $response;
    }

    public function fullMatch($pattern, $str) {
        return preg_match($pattern, $str, $matches) === 1 && $matches[0] === $str;
    }

    public function liveCheckLogin($request, $response, array $args) {
        $response = $response->withHeader('Content-type', 'application/json');
        $res = ['valide' => false, 'msg' => 'Utilisateur inexistant.'];
        if (isset($_GET['login'])) {
            $login = trim($_GET['login']);
            $user = Account::select('id')->where('email', '=', $login)->orwhere('username', '=', $login)->first();
            if (isset($user)) {
                $res['valide'] = true;
                $res['msg'] = '';
            }
        }
        $response->write(json_encode($res));
        return $response;
    }
}