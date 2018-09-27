<?php
/**
 * Created by PhpStorm.
 * User: francis
 * Date: 06/08/18
 * Time: 22:05
 */

namespace App\Controllers;
use App\Config;
use App\Models\Employee;
use App\Models\Employee_Project;
use Core\Controller;
use Core\Model;
use Core\View;
use App\Models\Project;

class EmployeeC extends Controller
{
    public function indexAction()
    {
        if (!isset($_SESSION["user"])) {
            header("Location:".Config::RACINE."/");
        } else {
            try {
                $departments = null;
                $error = $this->getMessage('error'); $this->setMessage('error','');
                $success = $this->getMessage('success'); $this->setMessage('success','');
                $departments = $this->findAll(Model::models('dep'));
                $projects = null;
                View::renderTemplate('Employee/index.html', ['user' => $_SESSION["user"],
                    'departments' => $departments,
                    'error'=>$error,
                    'success'=>$success]);

            } catch (\Exception $e) {
                View::renderTemplate('404.html');
            }
        }
    }

    public function addAction()
    {
        if (!isset($_SESSION['user'])) {
            header("Location:".Config::RACINE."/");
        } elseif ($this->getpost("first_name") == null || $this->getpost("email") == null) {
            $this->setMessage('error','veuillez remplir tous les champs');
            header("Location:".Config::RACINE."/Employee");
        } else {

            $newEmployee = new Employee();
            $newEmployee->setFirstName($this->getpost("first_name"));
            $newEmployee->setLastName($this->getpost("last_name"));
            $newEmployee->setEmail($this->getpost("email"));
            $newEmployee->setPhone($this->getpost("phone"));
            $newEmployee->setAddress($this->getpost("address"));
            $newEmployee->setDateOfEntry(new \DateTime($this->getpost("dateOfEntry")));
            $newEmployee->setEducation($this->getpost("education"));
            $newEmployee->setDepartment($this->findById(Model::models('dep'),$this->getpost("department")));
            $newEmployee->setOffice($this->getpost("office"));
            $newEmployee->setSkills($this->getpost("skills"));
            $newEmployee->setPassword(sha1("password"));
            $newEmployee->setLastUpdateDate();
            $newEmployee->setSignUpDate();

            try {
                $this->db->persist($newEmployee);
                $this->db->flush();
                /*if ($this->getpost("project") != null && $this->getpost("project") != '') {
                    $employee_project = new Employee_Project();
                    $employee_project->setAffectionDate(new \DateTime("now"));
                    $employee_project->setProject($this->db->getRepository('App\Models\Project')->find($this->getpost("project")));
                    $employee_project->setEmployee($this->db->getRepository('App\Models\Employee')->findOneBy(array("email" => $this->getpost("email"))));
                    $this->db->persist($employee_project);
                    $this->db->flush();
                }*/

                $this->logger->info('Creation of a new Employee ' . $newEmployee->getEmail(), ["authorEmail" => $_SESSION["user"]->getEmail()]);
                $this->setMessage('success','l\'employé a été ajouté');
                header("Location:".Config::RACINE."/Employee");
            } catch (\Exception $e) {
                $this->setMessage('error','Erreur lors de l\'ajout veuillez vérifier vos informations (champs bien remplis, email unique) et réessayer');
                header("Location:".Config::RACINE."/Employee");
            }
        }
    }

    public function editAction($other, $id)
    {
        $eid = isset($id)?$id:$this->getpost("id");

        if (!isset($_SESSION['user'])) {
            header("Location:".Config::RACINE."/");
        } elseif ($this->getpost("first_name") == null || $this->getpost("email") == null || $this->getpost("department") == null) {

            if($this->getpost("first_name") != null || $this->getpost("email") != null || $this->getpost("department") != null){
                $this->setMessage('error','veuillez remplir tous les champs');
            }

            $error = $this->getMessage('error'); $this->setMessage('error','');
            $success = $this->getMessage('success'); $this->setMessage('success','');

            $currentEmployee=null;
            $currentEmployee = $this->findById(Model::models('emp'),$eid);
            $departments = null;
            $departments = $this->findAll(Model::models('dep'));

            View::renderTemplate('Employee/edit.html', ['user' => $_SESSION["user"],
                'employee' => $currentEmployee,
                'departments' => $departments,
                'error'=>$error,
                'success'=>$success]);

        } else {

            $newEmployee = $this->findById(Model::models('emp'),$eid);

            $currentEmployee = unserialize(serialize($newEmployee));

            $departmentName = null;
            if ($newEmployee->getDepartment() != null)
                $departmentName = $newEmployee->getDepartment()->getName();

            $newEmployee->setFirstName($this->getpost("first_name"));
            $newEmployee->setLastName($this->getpost("last_name"));
            $newEmployee->setEmail($this->getpost("email"));
            $newEmployee->setPhone($this->getpost("phone"));
            $newEmployee->setAddress($this->getpost("address"));
            $newEmployee->setDateOfEntry(new \DateTime($this->getpost("dateOfEntry")));
            $newEmployee->setEducation($this->getpost("education"));
            $newEmployee->setDepartment($this->findById(Model::models('dep'),$this->getpost("department")));
            $newEmployee->setOffice($this->getpost("office"));
            $newEmployee->setSkills($this->getpost("skills"));
            $newEmployee->setPassword(sha1("password"));
            $newEmployee->setLastUpdateDate();
            $newEmployee->setSignUpDate();

            $departName="";
            if ($newEmployee->getDepartment() != null)
                $departName = $newEmployee->getDepartment()->getName();

            try {

                $this->db->persist($newEmployee);
                $this->db->flush();

                //var_dump($this->getpost("first_name"));
                $this->logger->info('Modification of an Employee', [
                    "authorEmail" => $_SESSION["user"]->getEmail(),
                    "oldData" => [
                        "firstName" => $currentEmployee->getFirstName(),
                        "lastName" => $currentEmployee->getLastName(),
                        "email" => $currentEmployee->getEmail(),
                        "phone" => $currentEmployee->getPhone(),
                        "address" => $currentEmployee->getAddress(),
                        "dateOfEntry" => $currentEmployee->getDateOfEntry(),
                        "education" => $currentEmployee->getEducation(),
                        "departmentName" => $departmentName,
                        "office" => $currentEmployee->getOffice(),
                        "skills" => $currentEmployee->getSkills(),
                        "lastUpdateDate" => $currentEmployee->getLastUpdateDate(),
                        "signUpDate" => $currentEmployee->getSignUpDate(),

                    ],
                    "newData" => [
                        "firstName" => $newEmployee->getFirstName(),
                        "lastName" => $newEmployee->getLastName(),
                        "email" => $newEmployee->getEmail(),
                        "phone" => $newEmployee->getPhone(),
                        "address" => $newEmployee->getAddress(),
                        "dateOfEntry" => $newEmployee->getDateOfEntry(),
                        "education" => $newEmployee->getEducation(),
                        "departmentName" => $departName,
                        "office" => $newEmployee->getOffice(),
                        "skills" => $newEmployee->getSkills(),
                        "lastUpdateDate" => $newEmployee->getLastUpdateDate(),
                        "signUpDate" => $newEmployee->getSignUpDate(),
                    ]
                ]);
                unset($departmentName);

                $this->setMessage('success','l\'employé a été Modifié');
                header("Location:".Config::RACINE."/Employee/list");
            } catch (\Exception $e) {
                $this->setMessage('error','Erreur lors de l\'ajout veuillez vérifier vos informations (champs bien remplis, email unique) et réessayer');
                header("Location:".Config::RACINE."/Employee/list");
            }
        }

    }

    public function deleteAction()
    {
        if (!isset($_SESSION['user'])) {
            header("Location:".Config::RACINE."/");
        } elseif ($this->getpost("id") == null || $this->getpost("reason") == null) {

            echo json_encode(array('message' => 'veuillez saisir une raison', 'great' => "0"));
        } else {
            // echo $this->getpost("reason");
            try {
                $currentEmployee = null;
                $currentEmployee = $this->findById(Model::models('emp'),$this->getpost('id'));
                $this->db->remove($currentEmployee);
                $this->db->flush();

                $this->logger->warning("Suppression d'un employé", [
                    'authorEmail' => $_SESSION['user']->getEmail(),
                    'reason' => $this->getpost("reason"),
                    'deletedEmployeeEmail' => $currentEmployee->getEmail(),
                ]);
                $arr = array('message' => 'Employé Supprimmé', 'great' => "1");

                echo json_encode($arr);

            } catch (\Exception $e) {
                //$this->logger->warning($e->getMessage());
                $arr = array('message' => 'Erreur lors de la suppréssion de l\'employé, veuillez reéssayer', 'great' => "0");

                echo json_encode($arr);
                //var_dump($e->getMessage());
                //View::renderTemplate('Employee/index.html', ['user' => $_SESSION["user"], 'error' => "Erreur lors de la suppréssion veuillez vérifier vos informations et réessayer"]);
            }
        }

    }

    public function listAction()
    {
        if (!isset($_SESSION["user"])) {
            header("Location:".Config::RACINE."/");
        } else {
            $employees = null;
            try {
                $error = $this->getMessage('error'); $this->setMessage('error','');
                $success = $this->getMessage('success'); $this->setMessage('success','');
                $employees = $this->findAll(Model::models('emp'));

                View::renderTemplate('Employee/list.html', ['user' => $_SESSION["user"],
                    'employees' => $employees,
                    'error'=>$error,
                    'success'=>$success]);
            } catch (\Exception $e) {
                View::renderTemplate('404.html');
            }
        }
    }

    /*
     * get employees whom are not chiefs*/
    public function notChiefEmployees(){
        $query = $this->db->createQuery('select e from App\Models\Employee e where e.id NOT in (select f.chief_id from App\Models\Department f where f.chief_id is not null )');
        $employees = $query->getResult();
        return $employees;
    }
    /*
     * get employees whom are not chiefs and chief of the currentDepartment*/
    public function thisChiefAndNotChiefEmployees($id){
        $employees=$this->notChiefEmployees();
        array_push($employees,$this->findById(Model::models('emp'),$id));
        return $employees;
    }
}