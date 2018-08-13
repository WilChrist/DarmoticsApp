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
use App\Models\EntryBill;
use App\Models\ExitBill;
use App\Models\Shareholder;

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
                $giver = null;
                if ($this->getpost("type") == "shareholder") {
                    $newentry->setType("apport actionnaire");
                    $newentry->setAmount($this->getpost("amount"));
                    $newentry->setContributorID($this->getpost("shareholder"));
                    $giver = $this->getSingleShareholder($this->getpost("shareholder"));
                } else {
                    //adding contributor in case of donatio type entry
                    $donor = new Contributor();
                    $donor->setLastName($this->getpost("contributorName"));
                    $donor->setPhone($this->getpost("contributorPhone"));

                    $this->db->persist($donor);
                    $this->db->flush();

                    $newentry->setType("don");
                    $newentry->setAmount($this->getpost("amount"));
                    $newentry->setContributorID($donor->getId());
                    $giver = $donor;
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

                //generate pdf
                $pdf = new EntryBill(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                $pdf->initialise();
                $pdf->writeData($newentry,$giver,$_SESSION['user']);
                $pdf->printBill();

                View::renderTemplate('Finances/entry.html', ['user' => $_SESSION["user"],
                    'shareholders' => $this->db->getRepository('App\Models\Shareholder')->findAll()]);
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
            //test if budget is not supérior to capital
            $availablecapital = $this->getAvailableCapital();
            if( $availablecapital < $this->getpost("amount")){
                View::renderTemplate('Finances/budgeting.html', ['user' => $_SESSION["user"],'projects'=>$this->projectsWithoutBudget(),
                    'error'=>'budget supérieur au capital non alouer qui est de '.$availablecapital]);
            }
            else{
                $budget = new Budgeting();
                $budget->setAmount($this->getpost("amount"));
                $budget->setProject($this->db->getRepository('App\Models\Project')->findOneBy(array('id'=>$this->getpost("project"))));
                $budget->setMovementDate();

                $this->db->persist($budget);
                $this->db->flush();
                View::renderTemplate('Finances/budgeting.html', ['user' => $_SESSION["user"],'projects'=>$this->projectsWithoutBudget(),
                    'success'=>'le budget a été alouer']);
            }
        }
    }

    public function exitAction(){
        if (!isset($_SESSION["user"])) {
            header("Location:/DarmoticsApp/public/");
        } else {
            try {
                View::renderTemplate('Finances/exit.html', ['user' => $_SESSION["user"],'budgets'=>$this->getBudgeting()]);
            }
            catch (\Exception $e){
                View::renderTemplate('500.html');
            }
        }
    }

    public function addExitAction(){
        if (!isset($_SESSION["user"])) {
            header("Location:/DarmoticsApp/public/");
        } else {
            $budget = $this->getpost("budget");
            if($this->getAvailableBudget($budget) < $this->getpost("amount")){
                View::renderTemplate('Finances/exit.html', ['user' => $_SESSION["user"],'budgets'=>$this->getBudgeting(),
                    'error'=>'le budget n\'est pas/plus suffisant ou épuisé']);
            }
            else{
                $newexit = new FinancialExit();
                $newexit->setAmount($this->getpost("amount"));
                $newexit->setReason($this->getpost("reason"));
                $newexit->setBudgeting($this->getSingleBudget($budget));
                $newexit->setMovementDate();

                $this->db->persist($newexit);
                $this->db->flush();

                $pdf = new ExitBill(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                $pdf->initialise();
                $pdf->writeData($newexit,$_SESSION['user']);
                $pdf->printBill();

                View::renderTemplate('Finances/exit.html', ['user' => $_SESSION["user"],'budgets'=>$this->getBudgeting(),
                    'success'=>'la sortie a correctement été enregistrée']);
            }

        }
    }


    private function projectsWithoutBudget(){
        $projects = $this->db->getRepository('App\Models\Project')->findAll();
        return array_filter($projects,function ($v){return $v->getBudget()==null;});
    }

    private function getAvailableCapital(){

        $query= $this->db->createQuery("SELECT SUM(b.amount) FROM App\Models\Budgeting b");
        $capital= $this->db->getRepository('App\Models\AccountUpdate')->findOneBy(array(), array('id' => 'desc'))->getAmountafter();
        return $capital - $query->getSingleScalarResult();
    }

    private function getAvailableBudget($id){
        $query= $this->db->createQuery("SELECT SUM(e.amount) FROM App\Models\FinancialExit e WHERE e.budgeting = ?1");
        $query->setParameter(1, $id);
        return $this->getSingleBudget($id)->getAmount() - $query->getSingleScalarResult();
    }

    private function getBudgeting(){
        return $this->db->getRepository('App\Models\Budgeting')->findAll();
    }

    private function getSingleBudget($id){
        return $this->db->getRepository('App\Models\Budgeting')->findOneBy(array('id'=>$id));
    }

    private function getSingleShareholder($id){
        return $this->db->getRepository('App\Models\Shareholder')->findOneBy(array('id'=>$id));
    }
}