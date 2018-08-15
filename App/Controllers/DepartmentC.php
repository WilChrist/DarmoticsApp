<?php
/**
 * Created by PhpStorm.
 * User: francis
 * Date: 06/08/18
 * Time: 22:05
 */

namespace App\Controllers;
use App\Config;
use App\Models\Department;
use Core\Controller;
use \Core\View;


class DepartmentC extends Controller
{

    public function indexAction()
    {

        if (!isset($_SESSION["user"])) {
            header("Location:".Config::RACINE."/");
        } else {

            View::renderTemplate('Department/index.html', ['user' => $_SESSION["user"]]);
        }

    }

    public function addAction(){
        if (!isset($_SESSION['user'])) {
            header("Location:".Config::RACINE."/");
        } elseif ($this->getpost("name") == null || $this->getpost("creationDate") == null) {
            header("Location:".Config::RACINE."/Department");
        } else {
            $newDepartment = new Department();
            $newDepartment->setName($this->getpost("name"));
            $newDepartment->setDescription($this->getpost("description"));
            $newDepartment->setChief($this->getpost("chief"));
            $newDepartment->setCreationDate(new \DateTime($this->getpost("creationDate")));
            $newDepartment->setLastUpdateDate(new \DateTime("now"));

            try {
                $this->db->persist($newDepartment);
                $this->db->flush();

                View::renderTemplate('Department/index.html', ['user' => $_SESSION["user"], 'success' => "le département a été ajouté"]);
            } catch (\Exception $e) {
                //var_dump($e->getMessage());
                View::renderTemplate('Department/index.html', ['user' => $_SESSION["user"], 'error' => "erreur lors de l'ajout veuillez réessayer"]);
            }
        }
    }

    public function editAction($other, $id)
    {
        if (!isset($_SESSION['user'])) {
            header("Location:".Config::RACINE."/");
        } elseif ($this->getpost("name") == null || $this->getpost("creationDate") == null) {
            $currentDepartment=null;
            $currentDepartment = $this->db->getRepository('App\Models\Department')->find($id);
            View::renderTemplate('Department/edit.html',["department" => $currentDepartment]);
        } else {

            $newDepartment = $this->db->getRepository('App\Models\Department')->find($this->getpost("id"));

            $currentDepartment = unserialize(serialize($newDepartment));

            $newDepartment->setName($this->getpost("name"));
            $newDepartment->setDescription($this->getpost("description"));
            $newDepartment->setChief($this->getpost("chief"));
            $newDepartment->setCreationDate(new \DateTime($this->getpost("creationDate")));
            $newDepartment->setLastUpdateDate(new \DateTime("now"));

            try {
                $this->db->persist($newDepartment);
                $this->db->flush();

                $this->logger->info('Modification of an Department', [
                    "authorEmail" => $_SESSION["user"]->getEmail(),
                    "oldData" => [
                        "name"=>$currentDepartment->getName(),
                        "description"=>$currentDepartment->getDescription(),
                        "chief"=>$currentDepartment->getChief(),
                        "creationDate"=>$currentDepartment->getCreationDate(),
                        "lastUpdateDate"=>$currentDepartment->getLastUpdateDate()
                    ],
                    "oldData" => [
                        "name"=>$newDepartment->getName(),
                        "description"=>$newDepartment->getDescription(),
                        "chief"=>$newDepartment->getChief(),
                        "creationDate"=>$newDepartment->getCreationDate(),
                        "lastUpdateDate"=>$newDepartment->getLastUpdateDate()
                    ]]);
                View::renderTemplate('Department/index.html', ['user' => $_SESSION["user"], 'success' => "le département a été Modifié"]);
            } catch (\Exception $e) {
                //var_dump($e->getMessage());
                View::renderTemplate('Department/index.html', ['user' => $_SESSION["user"], 'error' => "erreur lors de la modification, veuillez réessayer"]);
            }
        }

    }

    public function deleteAction()
    {
        if (!isset($_SESSION['user'])) {
            header("Location:/");
        } elseif ($this->getpost("id") == null || $this->getpost("reason") == null) {
            //$this->logger->info($this->getpost("reason")." 1");
            header("Location:/Department");
        } else {
            // echo $this->getpost("reason");
            try {
                $currentDepartment = null;
                $currentDepartment = $this->db->getRepository('App\Models\Department')->find($this->getpost('id'));
                $this->db->remove($currentDepartment);
                $this->db->flush();

                $this->logger->warning("Suppression d'un Département", [
                    'authorEmail' => $_SESSION['user']->getEmail(),
                    'reason' => $this->getpost("reason"),
                    'deletedDepartmentId' => $currentDepartment->getId(),
                    'deletedDepartmentName' => $currentDepartment->getName(),
                ]);
                $arr = array('message' => 'Departement Supprimmé', 'great' => "1");

                echo json_encode($arr);

            } catch (\Exception $e) {
                $this->logger->info("erreur lors de la suppression" . $e->getMessage());
                $arr = array('message' => 'Erreur lors de la suppréssion du Departement, veuillez reéssayer', 'great' => "0");

                echo json_encode($arr);

            }
        }

    }

    public function listAction(){
        if (!isset($_SESSION["user"])) {
            header("Location:".Config::RACINE."/");
        } else {
            $departments = null;
            try{
                $departments = $this->db->getRepository('App\Models\Department')->findAll();
                View::renderTemplate('Department/list.html', ['user' => $_SESSION["user"],"departments"=>$departments]);
            }
            catch (\Exception $e){
                //var_dump($e->getMessage());
                print("<pre>" . print_r($e, true) . "</pre>");
            }
        }
    }
}