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
use \App\Models\Project;

class DepartmentC extends Controller
{
    public function indexAction()
    {
        if (!isset($_SESSION["user"])) {
            header("Location:/DarmoticsApp/public/");
        } else {

            View::renderTemplate('Department/index.html', ['user' => $_SESSION["user"]]);
        }
    }

    public function addAction(){
        if (!isset($_SESSION['user'])) {
            header("Location:/DarmoticsApp/public/");
        } elseif ($this->getpost("name") == null || $this->getpost("creationDate") == null) {
            header("Location:/DarmoticsApp/public/Department");
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

    }

    public function listAction(){
        if (!isset($_SESSION["user"])) {
            header("Location:/DarmoticsApp/public/");
        } else {
            $departments = null;
            try{
                $departments = $this->db->getRepository('App\Models\Department')->findAll();
                View::renderTemplate('Department/list.html', ['user' => $_SESSION["user"],"departments"=>$departments]);
            }
            catch (\Exception $e){
                var_dump($e->getMessage());
            }
        }
    }
}