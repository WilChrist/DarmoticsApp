<?php
/**
 * Created by PhpStorm.
 * User: francis
 * Date: 06/08/18
 * Time: 22:05
 */

namespace App\Controllers;
use App\Models\Employee;
use App\Models\Employee_Project;
use Core\Controller;
use \Core\View;
use \App\Models\Project;

class EmployeeC extends Controller
{
    public function indexAction()
    {
        if (!isset($_SESSION["user"])) {
            header("Location:/");
        } else {
            $departments = null;
            $departments = $this->db->getRepository('App\Models\Department')->findAll();
            $projects = null;
            $projects = $this->db->getRepository('App\Models\Project')->findAll();
            View::renderTemplate('Employee/index.html', ['user' => $_SESSION["user"],"departments"=>$departments,'projects'=>$projects]);
        }
    }

    public function addAction(){
        if (!isset($_SESSION['user'])) {
            header("Location:/");
        } elseif ($this->getpost("first_name") == null || $this->getpost("email") == null || $this->getpost("department") == null) {
            header("Location:/Employee");
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
            $newEmployee->setSignUpDate();

            //print_r("<pre>".$newEmployee."</pre>");
            //print("<pre>".print_r($newEmployee,true)."</pre>");
            //var_dump($newEmployee);
            try {
                $this->db->persist($newEmployee);
                $this->db->flush();
                if($this->getpost("project")!=null && $this->getpost("project")!='') {
                    $employee_project = new Employee_Project();
                    $employee_project->setAffectionDate(new \DateTime("now"));
                    $employee_project->setProject($this->db->getRepository('App\Models\Project')->find($this->getpost("project")));
                    $employee_project->setEmployee($this->db->getRepository('App\Models\Employee')->findOneBy(array("email" => $this->getpost("email"))));
                    $this->db->persist($employee_project);
                    $this->db->flush();
                }

                $this->logger->info('Creation of a new Employee '.$newEmployee->getEmail(),["email"=>$_SESSION["user"]->getEmail()]);
                View::renderTemplate('Employee/index.html', ['user' => $_SESSION["user"], 'success' => "l'employé a été ajouté"]);
            } catch (\Exception $e) {
                //var_dump($e->getMessage());
                View::renderTemplate('Employee/index.html', ['user' => $_SESSION["user"], 'error' => "Erreur lors de l'ajout veuillez vérifier vos informations (champs bien remplis, email unique) et réessayer"]);
            }
        }
    }

    public function editAction($other,$id)
    {
        if (!isset($_SESSION['user'])) {
            header("Location:/");
        } elseif ($this->getpost("first_name") == null || $this->getpost("email") == null || $this->getpost("department") == null) {
            //var_dump($this->getpost("first_name"));//header("Location:/Employee");
            $currentEmployee=null;
            $currentEmployee = $this->db->getRepository('App\Models\Employee')->find($id);
            $departments = null;
            $departments = $this->db->getRepository('App\Models\Department')->findAll();
            $projects = null;
            $projects = $this->db->getRepository('App\Models\Project')->findAll();
            //$this->logger->info( 'Edit',["employee"=>$currentEmployee->getEmail()]);
            View::renderTemplate('Employee/edit.html', ['user' => $_SESSION["user"],'employee'=>$currentEmployee,"departments"=>$departments,'projects'=>$projects]);
            //var_dump($currentEmployee);
        } else {

            //$currentEmployee=null;
            //$currentEmployee = $this->db->getRepository('App\Models\Employee')->find($this->getpost("id"));
            //var_dump($this->getpost("first_name"));
            $newEmployee = $this->db->getRepository('App\Models\Employee')->find($this->getpost("id"));

            $currentEmployee= unserialize(serialize($newEmployee));
            $projectid=null;

            //var_dump($projectid);
            $departmentName=null;
            if($newEmployee->getDepartment()!=null)
                $departmentName=$newEmployee->getDepartment()->getName();

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
            $newEmployee->setSignUpDate();


            //print_r("<pre>".$newEmployee."</pre>");
            //print("<pre>".print_r($newEmployee,true)."</pre>");
            //var_dump($newEmployee);
            try {
                if(isset($newEmployee->getEmployeeProject()[0])){
                    $projectid=$newEmployee->getEmployeeProject()[0]->getProject()->getId();
                    $newEmployee->getEmployeeProject()[0]->setAffectionDate(new \DateTime("now"));
                    $newEmployee->getEmployeeProject()[0]->setProject($this->db->getRepository('App\Models\Project')->find($this->getpost("project")));
                    $newEmployee->getEmployeeProject()[0]->setEmployee($this->db->getRepository('App\Models\Employee')->findOneBy(array("email"=>$this->getpost("email"))));
                }else{
                    if($this->getpost("project")!=null && $this->getpost("project")!='') {
                        $employee_project = new Employee_Project();
                        $employee_project->setAffectionDate(new \DateTime("now"));
                        $employee_project->setProject($this->db->getRepository('App\Models\Project')->find($this->getpost("project")));
                        $employee_project->setEmployee($this->db->getRepository('App\Models\Employee')->findOneBy(array("email" => $this->getpost("email"))));
                        $this->db->persist($employee_project);
                        $this->db->flush();
                    }
                }

                $this->db->persist($newEmployee);
                $this->db->flush();

                //var_dump($this->getpost("first_name"));
                $this->logger->info('Modification of an Employee',[
                    "authorEmail"=>$_SESSION["user"]->getEmail(),
                    "oldData"=>[
                        "firstName"=>$currentEmployee->getFirstName(),
                        "lastName"=>$currentEmployee->getLastName(),
                        "email"=>$currentEmployee->getEmail(),
                        "phone"=>$currentEmployee->getPhone(),
                        "address"=>$currentEmployee->getAddress(),
                        "dateOfEntry"=>$currentEmployee->getDateOfEntry(),
                        "education"=>$currentEmployee->getEducation(),
                        "departmentName"=>$departmentName,
                        "office"=>$currentEmployee->getOffice(),
                        "skills"=>$currentEmployee->getSkills(),
                        "lastUpdateDate"=>$currentEmployee->getLastUpdateDate(),
                        "signUpDate"=>$currentEmployee->getSignUpDate(),
                        "projectId"=>$projectid,

                    ],
                    "newData"=>[
                        "firstName"=>$newEmployee->getFirstName(),
                        "lastName"=>$newEmployee->getLastName(),
                        "email"=>$newEmployee->getEmail(),
                        "phone"=>$newEmployee->getPhone(),
                        "address"=>$newEmployee->getAddress(),
                        "dateOfEntry"=>$newEmployee->getDateOfEntry(),
                        "education"=>$newEmployee->getEducation(),
                        "departmentName"=>$newEmployee->getDepartment()->getName(),
                        "office"=>$newEmployee->getOffice(),
                        "skills"=>$newEmployee->getSkills(),
                        "lastUpdateDate"=>$newEmployee->getLastUpdateDate(),
                        "signUpDate"=>$newEmployee->getSignUpDate(),
                        "projectId"=>$this->getpost("project")
                ]
                ]);unset($projectid);unset($departmentName);
                View::renderTemplate('Employee/index.html', ['user' => $_SESSION["user"], 'success' => "l'employé a été Modifié"]);
            } catch (\Exception $e) {
                //var_dump($e->getMessage());
                View::renderTemplate('Employee/index.html', ['user' => $_SESSION["user"], 'error' => "Erreur lors de l'ajout veuillez vérifier vos informations (champs bien remplis, email unique) et réessayer"]);
            }
        }

    }

    public function deleteAction()
    {
        if (!isset($_SESSION['user'])) {
            header("Location:/");
        } elseif ($this->getpost("id") == null || $this->getpost("reason") == null) {
            //$this->logger->info($this->getpost("reason")." 1");
            header("Location:/Employee");
        } else {
           // echo $this->getpost("reason");
            try {
                $currentEmployee=null;
                $currentEmployee = $this->db->getRepository('App\Models\Employee')->find($this->getpost('id'));
                $this->db->remove($currentEmployee);
                $this->db->flush();

                $this->logger->warning("Suppression d'un employé",[
                    'authorEmail'=>$_SESSION['user']->getEmail(),
                    'reason'=>$this->getpost("reason"),
                    'deletedEmployeeEmail'=>$currentEmployee->getEmail(),
                ]);
                $arr = array('message' => 'Employé Supprimmé', 'great'=>"1");

                echo json_encode($arr);

                //$this->logger->warning('Creation of a new Employee '.$newEmployee->getEmail(),["email"=>$_SESSION["user"]->getEmail()]);
                //View::renderTemplate('Employee/list.html', ['user' => $_SESSION["user"], 'success' => "l'employé a été supprimmé"]);
                //header("Location:/Employee/list");
            } catch (\Exception $e) {
                $arr = array('message' => 'Erreur lors de la suppréssion de l\'employé, veuillez reéssayer', 'great'=>"0");

                echo json_encode($arr);
                //var_dump($e->getMessage());
                //View::renderTemplate('Employee/index.html', ['user' => $_SESSION["user"], 'error' => "Erreur lors de la suppréssion veuillez vérifier vos informations et réessayer"]);
            }
        }

    }

    public function listAction(){
        if (!isset($_SESSION["user"])) {
            header("Location:/");
        } else {
            $employees = null;
            try{
                $employees = $this->db->getRepository('App\Models\Employee')->findAll();
                //print ($employees[3]->getEmployeeProject()[0]->getProject()->getName());
                ////
                View::renderTemplate('Employee/list.html', ['user' => $_SESSION["user"],"employees"=>$employees]);
            }
            catch (\Exception $e){
                //var_dump($e->getMessage());
                print("<pre>" . print_r($e, true) . "</pre>");
            }
        }
    }
}