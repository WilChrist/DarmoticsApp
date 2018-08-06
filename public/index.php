<?php

/**
 * Front controller
 *
 * PHP version 7.0
 */

/**
 * Composer
 */
require dirname(__DIR__) . '/vendor/autoload.php';


/**
 * Error and Exception handling
 */
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

/**
 * Routing
 */
use \Core\View;
$router = new Core\Router();

// Add the routes
$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('Home', ['controller' => 'Home', 'action' => 'index']);
$router->add('Home/login', ['controller' => 'Home', 'action' => 'login']);

$router->add('Shareholder', ['controller' => 'ShareholderC', 'action' => 'index']);
$router->add('Shareholder/index', ['controller' => 'ShareholderC', 'action' => 'index']);
$router->add('Shareholder/add', ['controller' => 'ShareholderC', 'action' => 'add']);
$router->add('Shareholder/list', ['controller' => 'ShareholderC', 'action' => 'list']);
$router->add('{controller}/{action}');

//je passe le $_POST au routeur afin qu'il le passe au controleur
try {
    $router->dispatch($_SERVER['QUERY_STRING'], $_POST);
}
catch (\Exception $e){
    View::renderTemplate('404.html', ['message'=>$e->getMessage()]);
}
