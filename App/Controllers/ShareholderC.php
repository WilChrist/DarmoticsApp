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
use Core\View;
use App\Models\Shareholder;

class ShareholderC extends Controller
{

    public function indexAction()
    {
        if (!isset($_SESSION["user"])) {
            header("Location:".Config::RACINE."/");
        } else {
            $error = $this->getMessage('error'); $this->setMessage('error','');
            $success = $this->getMessage('success'); $this->setMessage('success','');
            View::renderTemplate('Shareholder/index.html', ['user' => $_SESSION["user"],
                'error'=>$error,
                'success'=>$success]);
        }

    }

    public function addAction()
    {
        if (!isset($_SESSION['user'])) {
            header("Location:".Config::RACINE."/");
        } elseif ($this->getpost("last_name") == null || $this->getpost("email") == null || $this->getpost("phone") == null) {
            $this->setMessage('error','veuillez remplir tous les champs');
            header("Location:".Config::RACINE."/Shareholder");
        } else {
            $newShareholder = new Shareholder();
            $newShareholder->setLastName($this->getpost("last_name"));
            $newShareholder->setFirstName($this->getpost("first_name"));
            $newShareholder->setEmail($this->getpost("email"));
            $newShareholder->setPhone($this->getpost("phone"));
            $newShareholder->setAddress($this->getpost("address"));
            $newShareholder->setSharesPercentage(0);
            $newShareholder->setPassword(md5("default"));

            try {
                $this->db->persist($newShareholder);
                $this->db->flush();
                $this->logger->info('Creation of a new Shareholer '.$newShareholder->getEmail(),["email"=>$_SESSION["user"]->getEmail()]);

                $this->setMessage('success','Ajout correctement éffectué');
                header("Location:".Config::RACINE."/Shareholder");
            } catch (\Exception $e) {
                View::renderTemplate('404.html');
            }
        }
    }


    public function listAction(){
        if (!isset($_SESSION["user"])) {
            header("Location:".Config::RACINE."/");
        } else {
            $shareholders = null;
            try{
                $error = $this->getMessage('error'); $this->setMessage('error','');
                $success = $this->getMessage('success'); $this->setMessage('success','');
                $shareholders = $this->db->getRepository('App\Models\Shareholder')->findAll();

                View::renderTemplate('Shareholder/list.html', ['user' => $_SESSION["user"],
                    'shareholders'=>$shareholders,
                    'error'=>$error,
                    'success'=>$success]);
            }
            catch (\Exception $e){
                View::renderTemplate('404.html');
            }
        }
    }

    public function editAction($other,$id)
    {
        if (!isset($_SESSION['user'])) {
            header("Location:".Config::RACINE."/");
        } else{
            $shid = isset($id)?$id:$this->getpost("id");
            if ($this->getpost("last_name") == null || $this->getpost("email") == null) {

                $currentShareholder=null;
                if($this->getpost("last_name") != null  || $this->getpost("email") != null){
                    $this->setMessage('error', 'veuillez remplir tous les champs');
                }

                $error = $this->getMessage('error'); $this->setMessage('error', '');
                $success = $this->getMessage('success'); $this->setMessage('success', '');

                $currentShareholder = $this->db->getRepository('App\Models\Shareholder')->find($shid);

                View::renderTemplate('Shareholder/edit.html',["shareholder" => $currentShareholder,
                    'error' => $error,
                    'success' => $success]);
            } else {
                $newShareholder = $this->db->getRepository('App\Models\Shareholder')->find($shid);
                $currentShareholder = unserialize(serialize($newShareholder));

                $newShareholder->setLastName($this->getpost("last_name"));
                $newShareholder->setFirstName($this->getpost("first_name"));
                $newShareholder->setEmail($this->getpost("email"));
                $newShareholder->setPhone($this->getpost("phone"));
                $newShareholder->setAddress($this->getpost("address"));
                $newShareholder->setLastUpdateDate(new \DateTime("now"));
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
                    $this->setMessage('success', 'Modification correctement éffectuée');
                    header("Location:".Config::RACINE."/Shareholder/list");
                } catch (\Exception $e) {
                    View::renderTemplate('404.html');
                }
        }

        }

    }

    public function deleteAction()
    {

    }
}