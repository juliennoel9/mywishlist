<?php


namespace mywishlist\controllers;


class Controller {
    protected $view;

    public function __construct($container){
        $this->view = $container->view;
    }
}