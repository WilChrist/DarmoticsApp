<?php
/**
 * Created by PhpStorm.
 * User: francis
 * Date: 06/08/18
 * Time: 22:05
 */

namespace App\Controllers;
use App\Models\Employee;
use Core\Controller;
use \Core\View;
use \App\Models\Project;

class EmployeeC extends Controller
{
    public function indexAction()
    {
        if (!isset($_SESSION["user"])) {
            header("Location:/DarmoticsApp/public/");
        } else {
            $departments = null;
            $departments = $this->db->getRepository('App\Models\Department')->findAll();
            View::renderTemplate('Employee/index.html', ['user' => $_SESSION["user"],"departments"=>$departments]);
        }
    }

    public function addAction(){
        if (!isset($_SESSION['user'])) {
            header("Location:/DarmoticsApp/public/");
        } elseif ($this->getpost("first_name") == null || $this->getpost("email") == null || $this->getpost("department") == null) {
            header("Location:/DarmoticsApp/public/Employee");
        } else {
            $newEmployee = new Employee();
            $newEmployee->setFirstName($this->getpost("first_name"));
            $newEmployee->setLastName($this->getpost("last_name"));
            $newEmployee->setEmail($this->getpost("email"));
            $newEmployee->setPhone($this->getpost("phone"));
            $newEmployee->setAddress($this->getpost("address"));
            $newEmployee->setDateOfEntry(new \DateTime($this->getpost("dateOfEntry")));
            $newEmployee->setEducation($this->getpost("education"));
            $newEmployee->setDepartment($this->db->getRepository('App\Models\Department')->find($this->getpost("department")));
            $newEmployee->setOffice($this->getpost("office"));
            $newEmployee->setSkills($this->getpost("skills"));
            $newEmployee->setPassword(sha1("password"));
            $newEmployee->setLastUpdateDate(new \DateTime("now"));
            //print_r("<pre>".$newEmployee."</pre>");
            //print("<pre>".print_r($newEmployee,true)."</pre>");
            //var_dump($newEmployee);
            try {
                $this->db->persist($newEmployee);
                $this->db->flush();
                View::renderTemplate('Employee/index.html', ['user' => $_SESSION["user"], 'success' => "l'employé a été ajouté"]);
            } catch (\Exception $e) {
                //var_dump($e->getMessage());
                View::renderTemplate('Employee/index.html', ['user' => $_SESSION["user"], 'error' => "Erreur lors de l'ajout veuillez vérifier vos informations (champs bien remplis, email unique) et réessayer"]);
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
            $employees = null;
            try{
                $employees = $this->db->getRepository('App\Models\Employee')->findAll();
                View::renderTemplate('Employee/list.html', ['user' => $_SESSION["user"],"employees"=>$employees]);
            }
            catch (\Exception $e){
                var_dump($e->getMessage());
            }
        }
    }
}