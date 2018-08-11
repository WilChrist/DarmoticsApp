<?php

namespace App\Controllers;

use Core\Controller;
use \Core\View;

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

        $directorRepository = $this->db->getRepository('App\Models\Director');

        $user = $directorRepository->findOneBy(array("email"=>$this->getpost("email"),"password"=> sha1($this->getpost("password"))));

        if($user!==null){
            $_SESSION["user"]=$user;
            $this->logger->info( 'Login',["email"=>$user->getEmail()]);
            header("Location:/DarmoticsApp/public/Shareholder");
        }
        else{
            View::renderTemplate('Home/index.html',["error"=>"email ou mot de passe incorrecte"]);

        }
    }

    public function logoutAction(){
        session_destroy();
        $this->logger->info('Logout',["email"=>$_SESSION["user"]->getEmail()]);
        header("Location:/DarmoticsApp/public/Home");
    }
}
