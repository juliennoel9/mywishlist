<?php

require_once 'vendor/autoload.php';

session_start();

\mywishlist\config\Database::connect();

/**
 * Instanciation of Slim
 */
$app = new Slim\App();



/**
 * Run of Slim
 */
$app->run();