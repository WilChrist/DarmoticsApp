<?php
/**
 * Created by PhpStorm.
 * User: francis
 * Date: 10/08/18
 * Time: 11:02
 */

namespace App\Controllers;

use Core\Controller;
use \Core\View;
use App\Models\Budgeting;
use App\Models\FinancialEntry;
use App\Models\FinancialExit;
use App\Models\Contributor;
use App\Models\AccountUpdate;
class FinancesC extends \Core\Controller
{
    public function indexAction()
    {
        $this->entryAction();
    }

    public function entryAction(){
        if (!isset($_SESSION["user"])) {
            header("Location:/DarmoticsApp/public/");
        } else {
            try{
                $shareholders = $this->db->getRepository('App\Models\Shareholder')->findAll();
                View::renderTemplate('Finances/entry.html', ['user' => $_SESSION["user"],'shareholders'=>$shareholders]);
            }
            catch (\Exception $e){
                echo $e->getMessage();
                //View::renderTemplate('500.html');
            }

        }
    }

    public function addEntryAction(){

        if (!isset($_SESSION["user"])) {
            header("Location:/DarmoticsApp/public/");
        } else {

            try {
                //creation of the new entry
                $newentry = new FinancialEntry();
                if ($this->getpost("type") == "shareholder") {
                    $newentry->setType("shareholder");
                    $newentry->setAmount($this->getpost("amount"));
                    $newentry->setContributorID($this->getpost("shareholder"));

                } else {
                    //adding contributor in case of donatio type entry
                    $donor = new Contributor();
                    $donor->setName($this->getpost("contributorName"));
                    $donor->setPhone($this->getpost("contributorPhone"));

                    $this->db->persist($donor);
                    $this->db->flush();

                    $newentry->setType("donation");
                    $newentry->setAmount($this->getpost("amount"));
                    $newentry->setContributorID($donor->getId());
                }

                //updating account state
                $previousupdate = $this->db->getRepository('App\Models\AccountUpdate')->findOneBy(array(), array('id' => 'desc'));
                $newupdate = new AccountUpdate();
                $newupdate->setAmountbefore($previousupdate->getAmountafter());
                $newupdate->setAmount($this->getpost("amount"));
                $newupdate->setAmountafter($this->getpost("amount") + $previousupdate->getAmountafter());


                $newupdate->setDate();
                $newentry->setMovementDate();

                //save modification
                $this->db->persist($newentry);
                $this->db->persist($newupdate);
                $this->db->flush();

                View::renderTemplate('Finances/entry.html', ['user' => $_SESSION["user"], 'shareholders' => $this->db->getRepository('App\Models\Shareholder')->findAll()]);
            }
            catch (\Exception $e){
                View::renderTemplate('500.html');
            }
        }

    }

    public function budgetingAction(){
        if (!isset($_SESSION["user"])) {
            header("Location:/DarmoticsApp/public/");
        } else {
            try {
                View::renderTemplate('Finances/budgeting.html', ['user' => $_SESSION["user"],'projects'=>$this->projectsWithoutBudget()]);
            }
            catch (\Exception $e){
                View::renderTemplate('500.html');
            }
        }
    }

    public function addBudgetingAction(){
        if (!isset($_SESSION["user"])) {
            header("Location:/DarmoticsApp/public/");
        } else {
            $lastupdate = $this->db->getRepository('App\Models\AccountUpdate')->findOneBy(array(), array('id' => 'desc'));

            if($lastupdate->getAmountafter()<$this->getpost("amount")){
                View::renderTemplate('Finances/budgeting.html', ['user' => $_SESSION["user"],'projects'=>$this->projectsWithoutBudget()]);
            }
            else{
                $budget = new Budgeting();
                $budget->setAmount($this->getpost("amount"));
                $budget->setProject($this->db->getRepository('App\Models\Project')->findOneBy(array('id'=>$this->getpost("project"))));
                $budget->setMovementDate();

                $this->db->persist($budget);
                $this->db->flush();
                View::renderTemplate('Finances/budgeting.html', ['user' => $_SESSION["user"],'projects'=>$this->projectsWithoutBudget()]);
            }

        }
    }

    public function exitAction(){
        if (!isset($_SESSION["user"])) {
            header("Location:/DarmoticsApp/public/");
        } else {
            try {
                $budgets = $this->db->getRepository('App\Models\Budgeting')->findAll();
                View::renderTemplate('Finances/exit.html', ['user' => $_SESSION["user"],'budgets'=>$budgets]);
            }
            catch (\Exception $e){
                View::renderTemplate('500.html');
            }
        }
    }

    public function addExitAction(){

    }

    public function projectsWithoutBudget(){
        $projects = $this->db->getRepository('App\Models\Project')->findAll();
        return array_filter($projects,function ($v){return $v->getBudget()==null;});
    }
}