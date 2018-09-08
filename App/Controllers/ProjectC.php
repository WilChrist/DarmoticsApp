<?php
/**
 * Created by PhpStorm.
 * User: francis
 * Date: 06/08/18
 * Time: 22:05
 */

namespace App\Controllers;
use App\Config;
use App\Models\Employee_Project;
use App\Models\Project;
use Core\Controller;
use Core\Model;
use Core\View;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;


class ProjectC extends Controller
{
    public function indexAction()
    {
        if (!isset($_SESSION["user"])) {
            header("Location:".Config::RACINE."/");
        } else {
            $error = $this->getMessage('error'); $this->setMessage('error','');
            $success = $this->getMessage('success'); $this->setMessage('success','');
            View::renderTemplate('Project/index.html', ['user' => $_SESSION["user"],
                'employees'=>$this->employeesNotWorkingOnThisProject(0),
                'error'=>$error,
                'success'=>$success]);
        }
    }

    public function addAction(){
        if (!isset($_SESSION['user'])) {
            header("Location:".Config::RACINE."/");
        } elseif ($this->getpost("name") == null || $this->getpost("description") == null || $this->getpost("startDate") == null || !is_numeric($this->getpost('chief'))) {
            $this->setMessage('error','veuillez remplir tous les champs (avec de bonnes valeurs)');
            header("Location:".Config::RACINE."/Project");
        } else {
            $newProject = new Project();
            $newProject->setName($this->getpost("name"));
            $newProject->setDescription($this->getpost("description"));
            $newProject->setStartDate(new \DateTime($this->getpost("startDate")));
            $newProject->setEndDate(new \DateTime($this->getpost("endDate")));
            $newProject->setChief($this->findById(Model::models('emp'),$this->getpost('chief')));
            $emp_ids=$this->getPostMultiple('employees');
            if($emp_ids!=null){
                $projects=new ArrayCollection();
                foreach ($emp_ids as $empId){
                    if(is_numeric($empId)){
                        $employee_project=new Employee_Project();
                        $employee_project->setEmployee($this->findById(Model::models('emp'),$empId));
                        $employee_project->setProject($newProject);
                        $employee_project->setAffectionDate(new \DateTime('now'));
                        $this->db->persist($employee_project);
                        $projects->add($employee_project);
                    }
                }
                $newProject->setEmployeeProject($projects);
            }
            //var_dump($newProject->getEmployeeProject());
            //print("<pre>".print_r($newProject,true)."</pre>");
            try {
                $this->db->persist($newProject);
                $this->db->flush();
                $this->logger->info('Creation of a new project '.$newProject->getName(), [
                        "authorEmail"=>$_SESSION["user"]->getEmail(),
                        "projectName"=>$newProject->getName(),
                        ]);
                $this->setMessage('success','le projet a été ajouté');
                header("Location:".Config::RACINE."/Project");
            } catch (\Exception $e) {
                if($e instanceof UniqueConstraintViolationException){
                    $this->setMessage('error','le nom de project "'.$newProject->getName().'"existe déjà dans la base de données, veuillez en trouver un autre');
                    header("Location:".Config::RACINE."/Project");
                }
                print("<pre>".var_dump($e,true)."</pre>");
                //View::renderTemplate('404.html');
            }
        }
    }

    public function editAction($other, $id)
    {
        if (!isset($_SESSION['user'])) {
            header("Location:".Config::RACINE."/");
        } else
        {
            $pid = isset($id)?$id:$this->getpost("id");
            if ($this->getpost("name") == null || $this->getpost("description") == null || $this->getpost("startDate") == null || $this->getpost("endDate")==null || $this->getpost("state")==null || !array_key_exists($this->getpost('state'),Project::states())) {

                if ($this->getpost("name") != null || $this->getpost("description") != null || $this->getpost("startDate") != null || $this->getpost("endDate")!=null){
                    $m='';
                    if(!array_key_exists($this->getpost('state'),Project::states()))
                        $m=' et selectionnez un état valide';
                    $this->setMessage('error','veuillez remplir tous les champs'.$m);
                }
                $currentProject=null;
                $error = $this->getMessage('error'); $this->setMessage('error','');
                $success = $this->getMessage('success'); $this->setMessage('success','');
                $currentProject = $this->findById(Model::models('pro'),$pid);
                //var_dump($this->idSOfEmployeeWorkingOnThisProject($pid));
                View::renderTemplate('Project/edit.html',[
                    "project" => $currentProject,
                    "employees"=>$this->findAll(Model::models('emp')),
                    "states" => Project::states(),
                    'employeesOnThisProject'=>$this->idSOfEmployeeWorkingOnThisProject($pid),
                    'error'=>$error,
                    'success'=>$success]);
            } else {
                $newProject = $this->findById(Model::models('pro'),$pid);
                $chemail=null;
                if($newProject->getChief()!=null)
                    $chemail=$newProject->getChief()->getEmail();
                $empWOTP=$this->idSOfEmployeeWorkingOnThisProject($pid);
                $currentProject = unserialize(serialize($newProject));
                //$newProject->removeEmployeeProjects($this->findEmployeeProjectByProjectId($pid));
                $this->deleteEmployeeProjectByProjectId($pid);
                $newProject = $this->findById(Model::models('pro'),$pid);


                $newProject->setName($this->getpost("name"));
                $newProject->setDescription($this->getpost("description"));
                $newProject->setStartDate(new \DateTime($this->getpost("startDate")));
                $newProject->setEndDate(new \DateTime($this->getpost("endDate")));
                $newProject->setState($this->getpost("state"));
                $newProject->setChief($this->findById(Model::models('emp'),$this->getpost("chief")));
                $emp_ids=$this->getPostMultiple('employees');
                if($emp_ids!=null){
                    $projects=$newProject->getEmployeeProject();
                    foreach ($emp_ids as $empId){
                        if(is_numeric($empId)){
                            $employee_project=new Employee_Project();
                            $employee_project->setEmployee($this->findById(Model::models('emp'),$empId));
                            $employee_project->setProject($newProject);
                            $employee_project->setAffectionDate(new \DateTime('now'));
                            $this->db->persist($employee_project);
                            $projects->add($employee_project);
                        }
                    }
                    $newProject->setEmployeeProject($projects);
                }
                try {
                    $this->db->persist($newProject);
                    $this->db->flush();

                    $nchemail=null;
                    if($newProject->getChief()!=null)
                        $nchemail=$newProject->getChief()->getEmail();
                    $this->logger->info('Modification of a Project', [
                        "authorEmail" => $_SESSION["user"]->getEmail(),
                        "oldData" => [
                            "name"=>$newProject->getName(),
                            "description"=>$newProject->getDescription(),
                            "startDate"=>$newProject->getStartDate(),
                            "endDate"=>$newProject->getEndDate(),
                            "chief"=>$chemail,
                            "IdsOfEmployeeWorkingOnThisProject"=>$empWOTP,
                        ],
                        "newData" => [
                            "name"=>$currentProject->getName(),
                            "description"=>$currentProject->getDescription(),
                            "startDate"=>$currentProject->getStartDate(),
                            "endDate"=>$currentProject->getEndDate(),
                            "chief"=>$nchemail,
                            "IdsOfEmployeeWorkingOnThisProject"=>$emp_ids,
                        ]]);
                    $this->setMessage('success','le projet a été Modifié');
                    header("Location:".Config::RACINE."/Project/list");
                } catch (\Exception $e) {
                    var_dump($e);
                    //View::renderTemplate('404.html');
                }
            }
        }


    }

    public function deleteAction()
    {

    }

    public function listAction(){
        if (!isset($_SESSION["user"])) {
            header("Location:".Config::RACINE."/");
        } else {
            $projects = null;
            try{
                $error = $this->getMessage('error'); $this->setMessage('error','');
                $success = $this->getMessage('success'); $this->setMessage('success','');
                $projects = $this->db->getRepository('App\Models\Project')->findAll();

                View::renderTemplate('Project/list.html', ['user' => $_SESSION["user"],
                    "projects"=>$projects,
                    "states" => Project::states(),
                    'error'=>$error,
                    'success'=>$success]);
            }
            catch (\Exception $e){
                View::renderTemplate('404.html');
            }
        }
    }

    public function employeesNotWorkingOnThisProject($idp){
        $query = $this->db->createQuery('select e from App\Models\Employee e where e.id NOT in (select distinct ep.employee_id from App\Models\Employee_Project ep where ep.employee_id is not null and ep.project_id='.$idp.')');
        $employees = $query->getResult();
        return $employees;
    }
    public function idSOfEmployeeWorkingOnThisProject($idp){
        $query = $this->db->createQuery('select distinct ep.employee_id from App\Models\Employee_Project ep where ep.project_id is not null and ep.project_id='.$idp.'');
        $idSs = $query->getResult();
        $idS=[];
        foreach ($idSs as $i){
            array_push($idS,$i['employee_id']);
        }
        return $idS;
    }
    public function findEmployeeProjectByEmployeeIdAndProjectId($emp_id,$proj_id){
        $query = $this->db->createQuery('select distinct ep from App\Models\Employee_Project ep where ep.employee_id='.$emp_id.' and ep.project_id='.$proj_id.'');
        $employeeProject = $query->getResult();
        return $employeeProject;
    }
    public function findEmployeeProjectByProjectId($proj_id){
        $query = $this->db->createQuery('select ep from App\Models\Employee_Project ep where ep.project_id='.$proj_id.'');
        $employeeProjects = $query->getResult();
        return $employeeProjects;
    }
    public function deleteEmployeeProjectByProjectId($proj_id){
        $query = $this->db->createQuery('delete from App\Models\Employee_Project ep where ep.project_id='.$proj_id.'');
        return $query->getResult();
    }

}