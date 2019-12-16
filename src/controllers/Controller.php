<?php


namespace mywishlist\controllers;


class Controller {
    protected  $container;

    public function __construct($container){
        $this->container = $container;
    }

    public function redirect($response, $name, array $args = []) {
        if (isset($args['endPath'])){
            return $response->withStatus(302)->withHeader('Location', $this->container->router->pathFor($name, $args).$args['endPath']);
        }else {
            return $response->withStatus(302)->withHeader('Location', $this->container->router->pathFor($name, $args));
        }
    }
}