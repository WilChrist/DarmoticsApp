<?php
/**
 * Created by PhpStorm.
 * User: francis
 * Date: 30/07/18
 * Time: 14:36
 */

namespace App\Controllers;

use App\Config;
use Core\Controller;
use \Core\View;
use \App\Models\Shareholder;

class ShareholderC extends \Core\Controller
{

    public function indexAction()
    {
        if (!isset($_SESSION["user"])) {
            header("Location:".Config::RACINE."/");
        } else {

            View::renderTemplate('Shareholder/index.html', ['user' => $_SESSION["user"]]);
        }

    }

    public function addAction()
    {
        if (!isset($_SESSION['user'])) {
            header("Location:".Config::RACINE."/");
        } elseif ($this->getpost("last_name") == null || $this->getpost("email") == null || $this->getpost("sharespercentage") == null) {
            header("Location:".Config::RACINE."/Shareholder");
        } else {
            $newShareholder = new Shareholder();
            $newShareholder->setLastName($this->getpost("last_name"));
            $newShareholder->setFirstName($this->getpost("first_name"));
            $newShareholder->setEmail($this->getpost("email"));
            $newShareholder->setPhone($this->getpost("phone"));
            $newShareholder->setAddress($this->getpost("address"));
            $newShareholder->setSharesPercentage($this->getpost("sharespercentage"));
            $newShareholder->setPassword(md5("default"));

            try {
                $this->db->persist($newShareholder);
                $this->db->flush();
                $this->logger->info('Creation of a new Shareholer '.$newShareholder->getEmail(),["email"=>$_SESSION["user"]->getEmail()]);

                View::renderTemplate('Shareholder/index.html', ['user' => $_SESSION["user"], 'success' => "Ajout correctement éffectuer"]);
            } catch (\Exception $e) {
                //var_dump($e->getMessage());
                View::renderTemplate('Shareholder/index.html', ['user' => $_SESSION["user"], 'error' => "erreur lors de l'ajout veuillez réessayer"]);
            }
        }
    }


    public function listAction(){
        if (!isset($_SESSION["user"])) {
            header("Location:".Config::RACINE."/");
        } else {
            $shareholders = null;
            try{
                $shareholders = $this->db->getRepository('App\Models\Shareholder')->findAll();
                View::renderTemplate('Shareholder/list.html', ['user' => $_SESSION["user"],"shareholders"=>$shareholders]);
            }
            catch (\Exception $e){
                var_dump($e->getMessage());
            }
        }
    }
    public function editAction($other,$id)
    {
        if (!isset($_SESSION['user'])) {
            header("Location:".Config::RACINE."/");
        } elseif ($this->getpost("last_name") == null || $this->getpost("email") == null) {
            $currentShareholder=null;
            $currentShareholder = $this->db->getRepository('App\Models\Shareholder')->find($id);
            View::renderTemplate('Shareholder/edit.html',["shareholder" => $currentShareholder]);
        } else {
            $newShareholder = $this->db->getRepository('App\Models\Shareholder')->find($this->getpost("id"));
            $currentShareholder = unserialize(serialize($newShareholder));

            $newShareholder->setLastName($this->getpost("last_name"));
            $newShareholder->setFirstName($this->getpost("first_name"));
            $newShareholder->setEmail($this->getpost("email"));
            $newShareholder->setPhone($this->getpost("phone"));
            $newShareholder->setAddress($this->getpost("address"));
            //$newShareholder->setSharesPercentage($this->getpost("sharespercentage"));
            //$newShareholder->setPassword(sha1("default"));

            try {
                $this->db->persist($newShareholder);
                $this->db->flush();
                $this->logger->info('Modification of a Shareholder', [
                    "authorEmail" => $_SESSION["user"]->getEmail(),
                    "oldData" => [
                        "last_name"=>$currentShareholder->getLastName(),
                        "first_name"=>$currentShareholder->getFirstName(),
                        "email"=>$currentShareholder->getEmail(),
                        "phone"=>$currentShareholder->getPhone(),
                        "address"=>$currentShareholder->getAddress()
                    ],
                    "newData" => [
                        "last_name"=>$newShareholder->getLastName(),
                        "first_name"=>$newShareholder->getFirstName(),
                        "email"=>$newShareholder->getEmail(),
                        "phone"=>$newShareholder->getPhone(),
                        "address"=>$newShareholder->getAddress()
                    ]]);

                View::renderTemplate('Shareholder/index.html', ['user' => $_SESSION["user"], 'success' => "Modification correctement éffectuée"]);
            } catch (\Exception $e) {
                //var_dump($e->getMessage());
                View::renderTemplate('Shareholder/index.html', ['user' => $_SESSION["user"], 'error' => "erreur lors de la modification veuillez réessayer"]);
            }
        }

    }

    public function deleteAction()
    {

    }
}