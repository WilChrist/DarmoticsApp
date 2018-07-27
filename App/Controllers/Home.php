<?php

namespace App\Controllers;

use Core\Controller;
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
        //Le POST existe déjà donc on peut créer un USer directement
        $user =new User();
        $user->setEmail($this->post["email"]);
        $user->setPassword($this->post["password"]);
        var_dump($user);

        //passage du user à la vue
        View::renderTemplate('Home/login.html', ['user'=>$user]);
    }
}
