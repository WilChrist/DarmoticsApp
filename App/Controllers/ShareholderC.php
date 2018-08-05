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
        if(!isset($_SESSION['user'])){
            header("Location:/DarmoticsApp/public/");
        }
        elseif ($this->getpost("last_name")==null || $this->getpost("email")==null || $this->getpost("sharespercentage")==null ){
            header("Location:/DarmoticsApp/public/Shareholder");
        }
        else{
            $newShareholder = new Shareholder();
            $newShareholder->setLastName($this->getpost("last_name"));
                $newShareholder->setFirstName($this->getpost("fisrt_name"));
                $newShareholder->setEmail($this->getpost("email"));
                $newShareholder->setPhone($this->getpost("phone"));
                $newShareholder->setAddress($this->getpost("address"));
                $newShareholder->setSharesPercentage($this->getpost("sharespercentage"));
                $newShareholder->setPassword(md5("default"));

                try{
                    $this->db->persist($newShareholder);
                    $this->db->flush();
                    View::renderTemplate('Shareholder/index.html',['user'=>$_SESSION["user"],'success'=>"Ajout correctement éffectuer"]);
                }
                catch (\Exception $e){
                    //var_dump($e->getMessage());
                    View::renderTemplate('Shareholder/index.html',['user'=>$_SESSION["user"],'error'=>"erreur lors de l'ajout veuillez réessayer"]);
                }
        }
    }

    public function editAction(){

    }

    public function deleteAction(){

    }
}