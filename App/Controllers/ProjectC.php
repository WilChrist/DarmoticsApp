<?php
/**
 * Created by PhpStorm.
 * User: francis
 * Date: 06/08/18
 * Time: 22:05
 */

namespace App\Controllers;
use App\Config;
use App\Models\Project;
use Core\Controller;
use Core\View;


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
                'error'=>$error,
                'success'=>$success]);
        }
    }

    public function addAction(){
        if (!isset($_SESSION['user'])) {
            header("Location:".Config::RACINE."/");
        } elseif ($this->getpost("name") == null || $this->getpost("description") == null || $this->getpost("startDate") == null) {
            $this->setMessage('error','veuillez remplir tous les champs');
            header("Location:".Config::RACINE."/Project");
        } else {
            $newProject = new Project();
            $newProject->setName($this->getpost("name"));
            $newProject->setDescription($this->getpost("description"));
            $newProject->setStartDate(new \DateTime($this->getpost("startDate")));
            $newProject->setEndDate(new \DateTime($this->getpost("endDate")));

            try {
                $this->db->persist($newProject);
                $this->db->flush();
                $this->logger->info('Creation of a new project '.$newProject->getName(),["email"=>$_SESSION["user"]->getEmail()]);
                $this->setMessage('success','le projet a été ajouté');
                header("Location:".Config::RACINE."/Project");
            } catch (\Exception $e) {
                View::renderTemplate('404.html');
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
            if ($this->getpost("name") == null || $this->getpost("description") == null || $this->getpost("startDate") == null || $this->getpost("endDate")==null) {

                if ($this->getpost("name") != null || $this->getpost("description") != null || $this->getpost("startDate") != null || $this->getpost("endDate")!=null){
                    $this->setMessage('error','veuillez remplir tous les champs');
                }
                $currentProject=null;
                $error = $this->getMessage('error'); $this->setMessage('error','');
                $success = $this->getMessage('success'); $this->setMessage('success','');
                $currentProject = $this->db->getRepository('App\Models\Project')->find($pid);

                View::renderTemplate('Project/edit.html',["project" => $currentProject,
                    'error'=>$error,
                    'success'=>$success]);
            } else {
                $newProject = $this->db->getRepository('App\Models\Project')->find($pid);
                $currentProject = unserialize(serialize($newProject));

                $newProject->setName($this->getpost("name"));
                $newProject->setDescription($this->getpost("description"));
                $newProject->setStartDate(new \DateTime($this->getpost("startDate")));
                $newProject->setEndDate(new \DateTime($this->getpost("endDate")));

                try {
                    $this->db->persist($newProject);
                    $this->db->flush();

                    $this->logger->info('Modification of a Project', [
                        "authorEmail" => $_SESSION["user"]->getEmail(),
                        "oldData" => [
                            "name"=>$newProject->getName(),
                            "description"=>$newProject->getDescription(),
                            "startDate"=>$newProject->getStartDate(),
                            "endDate"=>$newProject->getEndDate()
                        ],
                        "newData" => [
                            "name"=>$currentProject->getName(),
                            "description"=>$currentProject->getDescription(),
                            "startDate"=>$currentProject->getStartDate(),
                            "endDate"=>$currentProject->getEndDate()
                        ]]);
                    $this->setMessage('success','le projet a été Modifié');
                    header("Location:".Config::RACINE."/Project/list");
                } catch (\Exception $e) {
                    View::renderTemplate('404.html');
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
                    'error'=>$error,
                    'success'=>$success]);
            }
            catch (\Exception $e){
                View::renderTemplate('404.html');
            }
        }
    }
}