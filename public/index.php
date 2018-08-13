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
$router->add('Project', ['controller' => 'ProjectC', 'action' => 'index']);
$router->add('Project/add', ['controller' => 'ProjectC', 'action' => 'add']);
$router->add('Project/list', ['controller' => 'ProjectC', 'action' => 'list']);

$router->add('Department', ['controller' => 'DepartmentC', 'action' => 'index']);
$router->add('Department/add', ['controller' => 'DepartmentC', 'action' => 'add']);
$router->add('Department/list', ['controller' => 'DepartmentC', 'action' => 'list']);


$router->add('Employee', ['controller' => 'EmployeeC', 'action' => 'index']);
$router->add('Employee/add', ['controller' => 'EmployeeC', 'action' => 'add']);
$router->add('Employee/edit', ['controller' => 'EmployeeC', 'action' => 'edit']);
$router->add('Employee/list', ['controller' => 'EmployeeC', 'action' => 'list']);
$router->add('{controller}/{action}');
$router->add('{controller}/{id:\d+}/{action}');

//je passe le $_POST au routeur afin qu'il le passe au controleur
try {
    $router->dispatch($_SERVER['QUERY_STRING'], $_POST);
}
catch (\Exception $e){
    //var_dump($e);
    View::renderTemplate('404.html', ['message'=>$e->getMessage()]);
}
