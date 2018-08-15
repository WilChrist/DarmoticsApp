<?php
/**
 * Created by PhpStorm.
 * User: francis
 * Date: 06/08/18
 * Time: 22:05
 */

namespace App\Controllers;
use App\Config;
use Core\Controller;
use \Core\View;
use \App\Models\Project;

class ProjectC extends Controller
{
    public function indexAction()
    {
        if (!isset($_SESSION["user"])) {
            header("Location:".Config::RACINE."/");
        } else {

            View::renderTemplate('Project/index.html', ['user' => $_SESSION["user"]]);
        }
    }

    public function addAction(){
        if (!isset($_SESSION['user'])) {
            header("Location:".Config::RACINE."/");
        } elseif ($this->getpost("name") == null || $this->getpost("description") == null || $this->getpost("startDate") == null) {
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
                View::renderTemplate('Project/index.html', ['user' => $_SESSION["user"], 'success' => "le projet a été ajouter"]);
            } catch (\Exception $e) {
                //var_dump($e->getMessage());
                View::renderTemplate('Project/index.html', ['user' => $_SESSION["user"], 'error' => "erreur lors de l'ajout veuillez réessayer"]);
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
            header("Location:".Config::RACINE."/");
        } else {
            $projects = null;
            try{
                $projects = $this->db->getRepository('App\Models\Project')->findAll();
                View::renderTemplate('Project/list.html', ['user' => $_SESSION["user"],"projects"=>$projects]);
            }
            catch (\Exception $e){
                var_dump($e->getMessage());
            }
        }
    }
}