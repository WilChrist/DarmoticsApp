<?php
/**
 * Created by PhpStorm.
 * User: francis
 * Date: 06/08/18
 * Time: 22:05
 */

namespace App\Controllers;
use App\Models\Department;
use Core\Controller;
use \Core\View;


$log=null;
class DepartmentC extends Controller
{

    public function indexAction()
    {

        if (!isset($_SESSION["user"])) {
            header("Location:/");
        } else {

            View::renderTemplate('Department/index.html', ['user' => $_SESSION["user"]]);
        }

    }

    public function addAction(){
        if (!isset($_SESSION['user'])) {
            header("Location:/");
        } elseif ($this->getpost("name") == null || $this->getpost("creationDate") == null) {
            header("Location:/Department");
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
                // add records to the log
                $this->log->info("My First Log");

                View::renderTemplate('Department/index.html', ['user' => $_SESSION["user"], 'success' => "le département a été ajouteé"]);
            } catch (\Exception $e) {
                //var_dump($e->getMessage());
                View::renderTemplate('Department/index.html', ['user' => $_SESSION["user"], 'error' => "erreur lors de l'ajout veuillez réessayer"]);
            }
        }
    }

    public function editAction()
    {

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
            header("Location:/");
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