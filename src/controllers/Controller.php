<?php

namespace mywishlist\controllers;
use Slim\Http\Response;

class Controller {
    protected  $container;

    public function __construct($container) {
        $this->container = $container;
    }

    public function redirect(Response $response, $name, array $argsURI = []) {
        return $response->withRedirect($this->container->router->pathFor($name, $argsURI));
    }
}