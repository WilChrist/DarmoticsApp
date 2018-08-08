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
        //Le POST existe déjà donc on peut créer un USer directement
        //$user =new User();
        //$user->setEmail($this->post["email"]);
        //$user->setPassword($this->post["password"]);
        $directorRepository = $this->db->getRepository('App\Models\Director');

        $user = $directorRepository->findOneBy(array("email"=>$this->getpost("email"),"password"=> sha1($this->getpost("password"))));

        if($user!==null){
            $_SESSION["user"]=$user;
            header("Location:/DarmoticsApp/public/Shareholder");
        }
        else{
            View::renderTemplate('Home/index.html',["error"=>"email ou mot de passe incorrecte"]);

        }
    }

    public function logoutAction(){
        session_destroy();
        header("Location:/DarmoticsApp/public/Home");
    }
}
