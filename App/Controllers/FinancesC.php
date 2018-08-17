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
use App\Models\CapitalUpdate;
use App\Models\DonationUpdate;
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
            header("Location:".Config::RACINE."/");
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
            header("Location:".Config::RACINE."/");
        }
        else {

            try {
                //creation of the new entry
                $newentry = new FinancialEntry();
                $giver = null;
                $newupdate = null;

                if ($this->getpost("type") == "shareholder") {
                    $newentry->setType("apport actionnaire");
                    $newentry->setAmount($this->getpost("amount"));
                    $newentry->setContributorID($this->getpost("shareholder"));
                    $giver = $this->getSingleShareholder($this->getpost("shareholder"));

                    //updating account state for capital
                    $newupdate = new CapitalUpdate();
                    $this->setAccountUpdate($newupdate,'\CapitalUpdate');

                    //adding date
                    $newupdate->setDate();
                    $newentry->setMovementDate();

                    //save modification
                    $this->db->persist($newentry);
                    $this->db->persist($newupdate);
                    $this->db->flush();

                    //update shareholder
                    $this->updateSharesPercentage();

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

                    //updating account state for donation
                    $newupdate = new DonationUpdate();
                    $this->setAccountUpdate($newupdate,'\DonationUpdate');

                    //adding date
                    $newupdate->setDate();
                    $newentry->setMovementDate();

                    //save modification
                    $this->db->persist($newentry);
                    $this->db->persist($newupdate);
                    $this->db->flush();
                }



                //generate pdf
                $pdf = new EntryBill(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                $pdf->initialise();
                $pdf->setEntry($newentry);
                $pdf->setGiver($giver);
                $pdf->setUser($_SESSION['user']);
                $pdf->setCreationDate(new \DateTime("now"));
                $this->db->persist($pdf);
                $this->db->flush();


                $pdf->writeData();
                $this->logger->info('Adding of new Entry '.$newentry->getType().' '.$newentry->getAmount(),["email"=>$_SESSION["user"]->getEmail()]);
                $pdf->printBill();

                View::renderTemplate('Finances/entry.html', ['user' => $_SESSION["user"],
                    'shareholders' => $this->db->getRepository('App\Models\Shareholder')->findAll()]);
            }
            catch (\Exception $e){
                View::renderTemplate('404.html',['message'=>$e->getMessage()]);
            }
        }

    }

    public function budgetingAction(){
        if (!isset($_SESSION["user"])) {
            header("Location:".Config::RACINE."/");
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
            header("Location:".Config::RACINE."/");
        } else {
            //test if budget is not supérior to capital
            $availablecapital = $this->getAvailableTreasury();
            if( $availablecapital < $this->getpost("amount")){
                View::renderTemplate('Finances/budgeting.html', ['user' => $_SESSION["user"],'projects'=>$this->projectsWithoutBudget(),
                    'error'=>'budget supérieur à la trésorerie non alouée qui est de '.$availablecapital]);
            }
            else{
                $budget = new Budgeting();
                $budget->setAmount($this->getpost("amount"));
                $budget->setProject($this->db->getRepository('App\Models\Project')->findOneBy(array('id'=>$this->getpost("project"))));
                $budget->setMovementDate();

                $this->db->persist($budget);
                $this->db->flush();
                $this->logger->info('Creation of a new Budget for project '.$budget->getProject()->getName(),["email"=>$_SESSION["user"]->getEmail()]);
                View::renderTemplate('Finances/budgeting.html', ['user' => $_SESSION["user"],'projects'=>$this->projectsWithoutBudget(),
                    'success'=>'le budget a été alouer']);
            }
        }
    }

    public function exitAction(){
        if (!isset($_SESSION["user"])) {
            header("Location:".Config::RACINE."/");
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
            header("Location:".Config::RACINE."/");
        } elseif($this->getpost("budget")<=0 || $this->getpost("amount")==null || $this->getpost("reason")==null ){
            View::renderTemplate('Finances/exit.html', ['user' => $_SESSION["user"],'budgets'=>$this->getBudgeting(),
                'error'=>'veuillez remplir tous les champs']);
        }
        else {
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
                $pdf->setExit($newexit);
                $pdf->setUser($_SESSION['user']);
                $pdf->setCreationDate(new \DateTime("now"));
                $this->db->persist($pdf);
                $this->db->flush();
                
                $pdf->writeData();
                $this->logger->info('Registration of new Exit for '.$newexit->getReason().' Amount'.$newexit->getAmount(),["email"=>$_SESSION["user"]->getEmail()]);
                $pdf->printBill();

                View::renderTemplate('Finances/exit.html', ['user' => $_SESSION["user"],'budgets'=>$this->getBudgeting(),
                    'success'=>'la sortie a correctement été enregistrée']);
            }

        }
    }

    public function listAction(){
        if (!isset($_SESSION["user"])) {
            header("Location:".Config::RACINE."/");
        } else {
            try {
                View::renderTemplate('Finances/list.html', ['user' => $_SESSION["user"],'budgets'=>$this->getBudgeting()]);
            }
            catch (\Exception $e){
                View::renderTemplate('500.html');
            }
        }
    }
    /* ================================================================================================================*/
    public function editBudgetAction($other,$id){
        if (!isset($_SESSION['user'])) {
            header("Location:".Config::RACINE."/");
        } elseif ($this->getpost("amount") == null || $this->getpost("reason") == null) {
            $currentbudget=null;
            $currentbudget = $this->db->getRepository('App\Models\Budgeting')->find($id);
            View::renderTemplate('Finances/editbudget.html',["budget" => $currentbudget ]);
        } else {
            $newbudget = $this->db->getRepository('App\Models\Budgeting')->find($this->getpost("id"));
            $currentbudget = unserialize(serialize($newbudget));

            /*check amount constraints of budget*/
            $query= $this->db->createQuery("SELECT SUM(e.amount) FROM App\Models\FinancialExit e WHERE e.budgeting = ?1");
            $query->setParameter(1, $this->getpost("id"));
            $usedpart = $query->getSingleScalarResult();
            $availabletreasury = $this->getAvailableTreasury();

            if($availabletreasury<$this->getpost("amount") || $this->getpost("amount")<$usedpart  ){
                View::renderTemplate('Finances/list.html', ['user' => $_SESSION["user"],'budgets'=>$this->getBudgeting(),
                    'error' => "le montant ne peut supérieur à la trésorerie disponible qui est de ".$availabletreasury." 
                    et ne peut être inférieur aux dépenses déjà rélisés qui sont de ".$usedpart]);
            }
            else{

                $newbudget->setAmount($this->getpost("amount"));

                try {
                    $this->db->persist($newbudget);
                    $this->db->flush();
                    $this->logger->info('Modification of a Budget', [
                        "authorEmail" => $_SESSION["user"]->getEmail(),
                        "oldData" => [
                            "Amount"=>$currentbudget->getAmount(),
                        ],
                        "newData" => [
                            "Amount"=>$newbudget->getAmount(),
                            "Modification reason"=>$this->getpost("reason")
                        ]]);
                    View::renderTemplate('Finances/list.html', ['user' => $_SESSION["user"],'budgets'=>$this->getBudgeting(), 'success' => "Modification correctement éffectuée"]);
                } catch (\Exception $e) {
                    //var_dump($e->getMessage());
                    View::renderTemplate('Finances/list.html', ['user' => $_SESSION["user"],'budgets'=>$this->getBudgeting(), 'error' => "erreur lors de la modification veuillez réessayer"]);
                }
            }



        }
    }









    /* ================================================================================================================*/

    /*this function return all project that doesn't have a budget assigned*/
    private function projectsWithoutBudget(){
        $projects = $this->db->getRepository('App\Models\Project')->findAll();
        return array_filter($projects,function ($v){return $v->getBudget()==null;});
    }

    /*return all the capital that can't be use for a new budget*/
    private function getAvailableTreasury(){

        $query= $this->db->createQuery("SELECT SUM(b.amount) FROM App\Models\Budgeting b");
        $capital= $this->db->getRepository('App\Models\CapitalUpdate')->findOneBy(array(), array('id' => 'desc'))->getAmountafter();
        $donation= $this->db->getRepository('App\Models\DonationUpdate')->findOneBy(array(), array('id' => 'desc'))->getAmountafter();
        return $capital+$donation - $query->getSingleScalarResult();
    }

    /*get the remaining amount of a budget */
    private function getAvailableBudget($id){
        $query= $this->db->createQuery("SELECT SUM(e.amount) FROM App\Models\FinancialExit e WHERE e.budgeting = ?1");
        $query->setParameter(1, $id);
        return $this->getSingleBudget($id)->getAmount() - $query->getSingleScalarResult();
    }

    /*return all budgets*/
    private function getBudgeting(){
        return $this->db->getRepository('App\Models\Budgeting')->findAll();
    }

    /*get a single budget by id*/
    private function getSingleBudget($id){
        return $this->db->getRepository('App\Models\Budgeting')->findOneBy(array('id'=>$id));
    }

    /*get a single shareholder by id*/
    private function getSingleShareholder($id){
        return $this->db->getRepository('App\Models\Shareholder')->findOneBy(array('id'=>$id));
    }

    /*set the new account update by the type of account*/
    private function setAccountUpdate(&$newupdate,$type){
        $previousupdate = $this->db->getRepository('App\Models'.$type)->findOneBy(array(), array('id' => 'desc'));
        $newupdate->setAmountbefore($previousupdate->getAmountafter());
        $newupdate->setAmount($this->getpost("amount"));
        $newupdate->setAmountafter($this->getpost("amount") + $previousupdate->getAmountafter());
    }

    /*this function update shares percentage of a shareholder*/
    private function updateSharesPercentage(){

        $shareholders = $this->db->getRepository('App\Models\Shareholder')->findAll();
        $capital= $this->db->getRepository('App\Models\CapitalUpdate')->findOneBy(array(), array('id' => 'desc'))->getAmountafter();

        foreach ($shareholders as $shareholder){
            $query= $this->db->createQuery("SELECT SUM(f.amount) FROM App\Models\FinancialEntry f WHERE f.type=?1 AND f.contributorID=?2");
            $query->setParameter(1, "apport actionnaire");
            $query->setParameter(2, $shareholder->getId());
            $shareholderpart = $query->getSingleScalarResult();
            $shareholder->setSharesPercentage(($shareholderpart/$capital)*100);
            $this->db->persist($shareholder);
        }

        $this->db->flush();
    }

}