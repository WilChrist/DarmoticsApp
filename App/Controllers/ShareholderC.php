<?php
/**
 * Created by PhpStorm.
 * User: francis
 * Date: 30/07/18
 * Time: 14:36
 */

namespace App\Controllers;

use Core\Controller;
use \Core\View;
use \App\Models\Shareholder;

class ShareholderC extends \Core\Controller
{

    public function indexAction(){
        if(!isset($_SESSION["user"])){
            header("Location:/DarmoticsApp/public/");
        }
        else{

            View::renderTemplate('Shareholder/index.html',['user'=>$_SESSION["user"]]);
        }

    }

    public function addAction(){

    }

    public function editAction(){

    }

    public function deleteAction(){

    }
}