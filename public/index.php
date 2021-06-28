<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

use components\Router;
use components\View;

try {
    chdir(dirname(__DIR__));
    require 'vendor/autoload.php';

    define('PROJECT_ROOT', dirname(__DIR__));
    define('APPLICATION_ROOT', dirname(__DIR__) . '/src');

    $router = new Router();
    $router->run();
} catch (Throwable $e) {
    $view = new View();
    $view->render('/views/site/error', [$e->getMessage()]);
}
