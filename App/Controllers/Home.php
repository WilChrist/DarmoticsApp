<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
/**
 * Home controller
 *
 * PHP version 7.0
 */
class Home extends \Core\Controller
{

    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
        View::renderTemplate('Home/index.html');
    }

    /**
     * Show the login page
     *
     * @return void
     */
    public function loginAction()
    {
        return 5;
        $user =new User();
        $user->email= $_POST["email"];
        $user->password=$_POST["password"];
        View::renderTemplate('Home/login.html');
    }
}
