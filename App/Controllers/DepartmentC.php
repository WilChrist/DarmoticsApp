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
use Core\Model;
use Core\View;


class DepartmentC extends Controller
{

    public function indexAction()
    {

        if (!isset($_SESSION["user"])) {
            header("Location:".Config::RACINE."/");
        } else {
            $error = $this->getMessage('error'); $this->setMessage('error','');
            $success = $this->getMessage('success'); $this->setMessage('success','');

            View::renderTemplate('Department/index.html',
                [
                    'employees'=>$this->notChiefEmployees(),
                    'user' => $_SESSION["user"],
                    'error'=>$error,
                    'success'=>$success
                ]);
        }

    }

    public function addAction(){
        if (!isset($_SESSION['user'])) {
            header("Location:".Config::RACINE."/");
        } elseif ($this->getpost("name") == null || $this->getpost("creationDate") == null) {
            $this->setMessage('error','veuillez remplir tous les champs');
            header("Location:".Config::RACINE."/Department");
        } else {
            $newDepartment = new Department();
            $newDepartment->setName($this->getpost("name"));
            $newDepartment->setDescription($this->getpost("description"));
            $newDepartment->setChief($this->findById(Model::models('emp'),$this->getpost("chief")));
            $newDepartment->setCreationDate(new \DateTime($this->getpost("creationDate")));
            $newDepartment->setLastUpdateDate(new \DateTime("now"));

            try {
                $this->db->persist($newDepartment);
                $this->db->flush();
                $this->setMessage('success','le département a été ajouté');
                $this->logger->info("création d'un Département", [
                    'authorEmail' => $_SESSION['user']->getEmail(),
                    'departmentName' => $newDepartment->getName(),
                ]);
                header("Location:".Config::RACINE."/Department");
            } catch (\Exception $e) {
                View::renderTemplate('404.html');
            }
        }
    }

    public function editAction($other, $id)
    {
        if (!isset($_SESSION['user'])) {
            header("Location:".Config::RACINE."/");
        } else{

            $did = isset($id)?$id:$this->getpost("id");
            if ($this->getpost("name") == null || $this->getpost("creationDate") == null) {

                if($this->getpost("name") != null || $this->getpost("creationDate") != null){
                    $this->setMessage('error','veuillez remplir tous les champs');
                }
                $employees=[];
                $currentDepartment=null;
                $error = $this->getMessage('error'); $this->setMessage('error','');
                $success = $this->getMessage('success'); $this->setMessage('success','');

                $currentDepartment = $this->findById(Model::models('dep'),$did);

                if($currentDepartment==null){
                    $this->setMessage('error','ce Département n\'existe pas');
                    header("Location:".Config::RACINE."/Department");
                }
                if($currentDepartment->getChiefId()!=null)
                    $employees=$this->thisChiefAndNotChiefEmployees($currentDepartment->getChiefId());

                View::renderTemplate('Department/edit.html',["department" => $currentDepartment,
                    'employees'=>$employees,
                    'error'=>$error,
                    'success'=>$success]);
            } else {

                $newDepartment = $this->findById(Model::models('dep'),$did);
                $chemail=null;
                if($newDepartment->getChief()!=null)
                    $chemail=$newDepartment->getChief()->getEmail();
                $currentDepartment = unserialize(serialize($newDepartment));

                $newDepartment->setName($this->getpost("name"));
                $newDepartment->setDescription($this->getpost("description"));
                $newDepartment->setChief($this->findById(Model::models('emp'),$this->getpost("chief")));
                $newDepartment->setCreationDate(new \DateTime($this->getpost("creationDate")));
                $newDepartment->setLastUpdateDate(new \DateTime("now"));

                try {
                    $this->db->persist($newDepartment);
                    $this->db->flush();
                    $nchemail=null;
                    if($newDepartment->getChief()!=null)
                        $nchemail=$newDepartment->getChief()->getEmail();
                    $this->logger->info('Modification of an Department', [
                        "authorEmail" => $_SESSION["user"]->getEmail(),
                        "oldData" => [
                            "name"=>$currentDepartment->getName(),
                            "description"=>$currentDepartment->getDescription(),
                            "chief"=>$chemail,
                            "creationDate"=>$currentDepartment->getCreationDate(),
                            "lastUpdateDate"=>$currentDepartment->getLastUpdateDate()
                        ],
                        "newData" => [
                            "name"=>$newDepartment->getName(),
                            "description"=>$newDepartment->getDescription(),
                            "chief"=>$nchemail,
                            "creationDate"=>$newDepartment->getCreationDate(),
                            "lastUpdateDate"=>$newDepartment->getLastUpdateDate()
                        ]]);

                    $this->setMessage('success','le département a été Modifié');
                    header("Location:".Config::RACINE."/Department/list");
                } catch (\Exception $e) {
                    View::renderTemplate('404.html');
                }
            }
        }


    }

    public function deleteAction()
    {
        if (!isset($_SESSION['user'])) {
            header("Location:".Config::RACINE."/");
        } else{
            if ($this->getpost("id") == null || $this->getpost("reason") == null) {

                echo json_encode(array('message' => 'veuillez entrez une raison', 'great' => "0"));
            } else {
                // echo $this->getpost("reason");
                try {
                    $currentDepartment = null;
                    $currentDepartment = $this->findById(Model::models('dep'),$this->getpost('id'));
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


    }

    public function listAction(){
        if (!isset($_SESSION["user"])) {
            header("Location:".Config::RACINE."/");
        } else {
            $departments = null;
            try{
                $error = $this->getMessage('error'); $this->setMessage('error','');
                $success = $this->getMessage('success'); $this->setMessage('success','');
                $departments = $this->findAll(Model::models('dep'));
                View::renderTemplate('Department/list.html', ['user' => $_SESSION["user"],
                    "departments"=>$departments,
                    'error'=>$error,
                    'success'=>$success]);
            }
            catch (\Exception $e){
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