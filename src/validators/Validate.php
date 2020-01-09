<?php

namespace mywishlist\validators;

use mywishlist\models\Account;
use Slim\Http\Response;

class Validate {

    public static function username(string $input) : array {
        if (!self::matchRegex('/[a-zA-Z0-9_-]{1,20}/', $input)) {
            return ['valid' => false, 'error' => 'Utilisez uniquement des lettres (sans accents), chiffres, tirets et underscores.'];
        }
        if (strlen($input) < 3) {
            return ['valid' => false, 'error' => "Votre nom d'utilisateur doit faire entre 3 et 20 caractères."];
        }
        if (Account::select('username')->where('username', '=', $input)->first()) {
            return ['valid' => false, 'error' => "Ce nom d'utilisateur est déjà utilisé."];
        }
        return ['valid' => true, 'error' => ''];
    }

    public static function email(string $input) : array {
        if (!filter_var($input, FILTER_VALIDATE_EMAIL)) {
            return ['valid' => false, 'error' => "Cet email n'est pas valide."];
        }
        if (Account::select('email')->where('email', '=', $input)->first()) {
            return ['valid' => false, 'error' => 'Cet email est déjà utilisé.'];
        }
        return ['valid' => true, 'error' => ''];
    }

    public static function login(string $input) : array {
        if (!Account::select('id')->where('email', '=', $input)->orwhere('username', '=', $input)->first()) {
            return ['valid' => false, 'error' => 'Utilisateur inconnu.'];
        }
        return ['valid' => true, 'error' => ''];
    }

    public static function matchRegex($pattern, $str) {
        return preg_match($pattern, $str, $matches) === 1 && $matches[0] === $str;
    }

    /**
     * API pour checker la validité des inputs en live avec un script JS.
     * Route: /live_check?{check_method}={input}
     *
     * @param Response $response Slim Response
     * @return Response JSON response like ['valid' => true|false, 'error' => 'error message if invalid']
     */
    public static function api(Response $response) {
        $response = $response->withHeader('Content-type', 'application/json');
        $type = array_key_first($_GET);
        if (!$type) {
            return $response->write(json_encode(['valid' => false, 'error' => 'Incorrect API use.']));
        }
        if (!method_exists(Validate::class, $type)) {
            return $response->write(json_encode(['valid' => false, 'error' => "Method '$type' doesn't exist."]));
        }
        $input = isset($_GET[$type]) ? trim($_GET[$type]) : '';
        $response->write(json_encode(Validate::$type($input)));
        return $response;
    }
}