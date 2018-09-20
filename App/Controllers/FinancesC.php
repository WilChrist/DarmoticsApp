<?php
/**
 * Created by PhpStorm.
 * User: francis
 * Date: 10/08/18
 * Time: 11:02
 */

namespace App\Controllers;

use App\Models\BudgetingUpdate;
use App\Models\File;
use Core\Controller;
use Core\View;
use App\Models\Budgeting;
use App\Models\FinancialEntry;
use App\Models\FinancialExit;
use App\Models\Contributor;
use App\Models\CapitalUpdate;
use App\Models\DonationUpdate;
use App\Models\EntryBill;
use App\Models\ExitBill;
use App\Models\Shareholder;
use App\Config;
class FinancesC extends Controller
{
    public function indexAction()
    {
        $this->entryAction();
    }

    public function entryAction()
    {
        if (!isset($_SESSION["user"])) {
            header("Location:" . Config::RACINE . "/");
        } else {
            try {
                $error = $this->getMessage('error'); $this->setMessage('error','');
                $success = $this->getMessage('success'); $this->setMessage('success','');
                $shareholders = $this->db->getRepository('App\Models\Shareholder')->findAll();

                View::renderTemplate('Finances/entry.html', ['user' => $_SESSION["user"],
                    'shareholders' => $shareholders,
                    'error'=>$error,
                    'success'=>$success]);

            } catch (\Exception $e) {
                echo $e->getMessage();
                //View::renderTemplate('500.html');
            }

        }
    }

    public function addEntryAction()
    {

        if (!isset($_SESSION["user"])) {
            header("Location:" . Config::RACINE . "/");
        } else {

            try {
                //creation of the new entry
                $newentry = new FinancialEntry();
                $giver = null;
                $newupdate = null;

                if ($this->getpost("type") == "shareholder" && ($this->getpost("amount") == null || $this->getpost("amount")==0 || $this->getpost("shareholder") < 0)) {

                    $this->setMessage('error','veuillez remplir tous les champs');
                    header("Location:" . Config::RACINE . "/Finances/entry");

                } elseif ($this->getpost("type") != "shareholder" && ($this->getpost("amount") == null || $this->getpost("amount")==0 || $this->getpost("contributorName") == null)) {

                    $this->setMessage('error','veuillez remplir tous les champs');
                    header("Location:" . Config::RACINE . "/Finances/entry");

                } else {
                    if ($this->getpost("type") == "shareholder") {
                        $newentry->setType("apport actionnaire");
                        $newentry->setAmount($this->getpost("amount"));
                        $newentry->setContributorID($this->getpost("shareholder"));
                        $giver = $this->getSingleShareholder($this->getpost("shareholder"));

                        //updating account state for capital
                        $newupdate = new CapitalUpdate();
                        $this->setAccountUpdate($newupdate, '\CapitalUpdate');

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
                        $donor->setEmail($this->getpost("contributorEmail"));

                        $this->db->persist($donor);
                        $this->db->flush();

                        $newentry->setType("don");
                        $newentry->setAmount($this->getpost("amount"));
                        $newentry->setContributorID($donor->getId());
                        $giver = $donor;

                        //updating account state for donation
                        $newupdate = new DonationUpdate();
                        $this->setAccountUpdate($newupdate, '\DonationUpdate');

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


                    //$pdf->writeData();
                    $this->logger->info('Adding of new Entry ' . $newentry->getType() . ' ' . $newentry->getAmount(), ["email" => $_SESSION["user"]->getEmail()]);
                    //$pdf->printBill();
                    $message = 'l\'apport a correctement été enregistrée cliquer sur ce <a href = "'.Config::RACINE .'/FinancesC/'.$pdf->getId().'/entryprint" target="_blank">lien</a> pour télécharger la facture';
                    $this->setMessage('success',$message);
                    header("Location:" . Config::RACINE . "/Finances/entry");


                }
            }
            catch (\Exception $e) {
                View::renderTemplate('404.html');
            }
        }

    }

    public function budgetingAction()
    {
        if (!isset($_SESSION["user"])) {
            header("Location:" . Config::RACINE . "/");
        } else {
            try {
                $error = $this->getMessage('error'); $this->setMessage('error','');
                $success = $this->getMessage('success'); $this->setMessage('success','');

                View::renderTemplate('Finances/budgeting.html', ['user' => $_SESSION["user"],
                    'projects' => $this->projectsWithoutBudget(),
                    'error'=>$error,
                    'success'=>$success]);

            } catch (\Exception $e) {
                View::renderTemplate('404.html');
            }
        }
    }

    public function addBudgetingAction()
    {
        if (!isset($_SESSION["user"])) {
            header("Location:" . Config::RACINE . "/");
        } else {
            try {
                $availablecapital = $this->getAvailableTreasury();
                if($this->getpost("amount")==null || $this->getpost("project")<0){
                    $this->setMessage('error','veuillez remplir tous les champs');
                    header("Location:" . Config::RACINE . "/Finances/budgeting");
                }
                elseif ($availablecapital< $this->getpost("amount")){
                    $this->setMessage('error','budget supérieur à la trésorerie non alouée qui est de ' . $availablecapital);
                    header("Location:" . Config::RACINE . "/Finances/budgeting");
                }

                else {
                    $budget = new Budgeting();
                    $budget->setOriginAmount($this->getpost("amount")); $budget->setAmount($this->getpost("amount"));
                    $budget->setUsedPart(0); $budget->setRest($this->getpost("amount"));
                    $budget->setProject($this->db->getRepository('App\Models\Project')->findOneBy(array('id' => $this->getpost("project"))));
                    $budget->setMovementDate();

                    $this->db->persist($budget);
                    $this->db->flush();
                    $this->logger->info('Creation of a new Budget for project ' . $budget->getProject()->getName(), ["email" => $_SESSION["user"]->getEmail()]);

                    $this->setMessage('success','le budget a été alouer');
                    header("Location:" . Config::RACINE . "/Finances/budgeting");

                }
            }
            catch (\Exception $e){
                var_dump($e->getMessage());
                //View::renderTemplate('404.html');
            }
        }
    }

    public function exitAction()
    {
        if (!isset($_SESSION["user"])) {
            header("Location:" . Config::RACINE . "/");
        } else {
            try {
                $error = $this->getMessage('error'); $this->setMessage('error','');
                $success = $this->getMessage('success'); $this->setMessage('success','');

                View::renderTemplate('Finances/exit.html', ['user' => $_SESSION["user"],
                    'budgets' => $this->getBudgeting(),
                    'error'=>$error,
                    'success'=>$success]);
            } catch (\Exception $e) {
                View::renderTemplate('404.html');
            }
        }
    }

    public function addExitAction()
    {
        if (!isset($_SESSION["user"])) {
            header("Location:" . Config::RACINE . "/");
        } elseif ($this->getpost("budget") <= 0 || $this->getpost("amount") == null || $this->getpost("reason") == null) {
            $this->setMessage('error','veuillez remplir tous les champs');
            header("Location:" . Config::RACINE . "/Finances/exit");
        } else {
            $budgetId = $this->getpost("budget");
            $budget = $this->getSingleBudget($budgetId);
            if ($budget->getRest() < $this->getpost("amount")) {
                $this->setMessage('error','le budget n\'est pas/plus suffisant ou épuisé');
                header("Location:" . Config::RACINE . "/Finances/exit");
            } elseif($this->testFile()){
                $this->setMessage('error','format ou poids de fichier incorrect');
                header("Location:" . Config::RACINE . "/Finances/exit");
            }
            else{
                $newexit = new FinancialExit();
                $newexit->setAmount($this->getpost("amount"));
                $newexit->setReason($this->getpost("reason"));
                $newexit->setProject($budget->getProject()->getName());
                $newexit->setBudgeting($budget);

                /*important*/
                $budget->setUsedPart($budget->getUsedPart() + $this->getpost("amount"));
                $budget->setRest($budget->getAmount() - $budget->getUsedPart());

                $newexit->setMovementDate();
                $this->db->persist($newexit);
                $this->db->persist($budget);
                $this->db->flush();

                $pdf = new ExitBill(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                $pdf->initialise();
                $pdf->setExit($newexit);
                $pdf->setUser($_SESSION['user']);
                $pdf->setCreationDate(new \DateTime("now"));
                $this->db->persist($pdf);
                $this->db->flush();

                /*file upload*/
                $this->savefile($newexit);

                //$pdf->writeData();
                $this->logger->info('Registration of new Exit for ' . $newexit->getReason() . ' Amount' . $newexit->getAmount(), ["email" => $_SESSION["user"]->getEmail()]);
                //$pdf->printBill();
                $message = 'la sortie a correctement été enregistrée cliquer sur ce <a href = "'.Config::RACINE .'/FinancesC/'.$pdf->getId().'/exitprint" target="_blank">lien</a> pour télécharger la facture';
                $this->setMessage('success',$message);
                header("Location:" . Config::RACINE . "/Finances/exit");
            }

        }
    }

    public function listAction()
    {
        if (!isset($_SESSION["user"])) {
            header("Location:" . Config::RACINE . "/");
        } else {
            try {
                $error = $this->getMessage('error');
                $this->setMessage('error', '');
                $success = $this->getMessage('success');
                $this->setMessage('success', '');
                View::renderTemplate('Finances/list.html', ['user' => $_SESSION["user"], 'budgets' => $this->getBudgeting(),
                    'error' => $error,
                    'success' => $success]);
            } catch (\Exception $e) {
                var_dump($e->getMessage());
                //View::renderTemplate('404.html');
            }
        }
    }

    /* ================================================================================================================*/
    public function editBudgetAction($other,$id,$type)
    {
        if (!isset($_SESSION['user'])) {
            header("Location:" . Config::RACINE . "/");

        } else {
            try {
                $budgetid = isset($id)?$id:$this->getpost("id");
                $actiontype = isset($type)?$type:$this->getpost("type");
                $currentbudget = null;
                if ($this->getpost("amount") == null || $this->getpost("reason") == null || $this->getpost("type")==null) {

                    if($this->getpost("amount") != null || $this->getpost("reason") != null || $this->getpost("type")!=null){
                        $this->setMessage('error', 'veuillez remplir tous les champs');
                    }
                    $error = $this->getMessage('error');
                    $this->setMessage('error', '');
                    $success = $this->getMessage('success');
                    $this->setMessage('success', '');
                    $currentbudget = $this->db->getRepository('App\Models\Budgeting')->find($budgetid);

                    View::renderTemplate('Finances/editbudget.html', ["budget" => $currentbudget,
                        'error' => $error,
                        'success' => $success,
                        'type'=>$actiontype]);

                } else {

                    $newbudget = $this->db->getRepository('App\Models\Budgeting')->find($budgetid);
                    $currentbudget = unserialize(serialize($newbudget));
                    $newamount = null;

                    if($actiontype=="1"){$newamount = $newbudget->getAmount() + $this->getpost("amount");}
                    elseif ($actiontype=="0"){$newamount = $newbudget->getAmount()- $this->getpost("amount");}

                    /*check amount constraints of budget
                    $query = $this->db->createQuery("SELECT SUM(e.amount) FROM App\Models\FinancialExit e WHERE e.budgeting = ?1");
                    $query->setParameter(1, $this->getpost("id"));
                    $usedpart = $query->getSingleScalarResult();*/
                    $availabletreasury = $this->getAvailableTreasury();

                    if ($availabletreasury < $newamount || $newamount < $newbudget->getUsedPart() || $this->getpost("amount")==0) {
                        $this->setMessage('error','le nouveau budget ne peut être supérieur à la trésorerie disponible qui est de' . $availabletreasury .', et ne peut être inférieur aux dépenses déjà rélisés qui sont de ' . $newbudget->getUsedPart().' et le montant à ajouter/diminuer ne peut être nul');
                        header("Location:" . Config::RACINE . "/FinancesC/".$budgetid."/".$actiontype."/editbudget");
                    } else {

                        $newbudget->setAmount($newamount);
                        $newbudget->setRest($newbudget->getAmount() - $newbudget->getUsedPart());
                        $budgetingupdate = new BudgetingUpdate();
                        $budgetingupdate->setAmountBefore($currentbudget->getAmount());
                        $budgetingupdate->setAmountAfter($newamount);
                        $budgetingupdate->setAmount($this->getpost("amount"));
                        $budgetingupdate->setBudgeting($newbudget);
                        $budgetingupdate->setMovementDate();


                        $this->db->persist($newbudget);
                        $this->db->persist($budgetingupdate);
                        $this->db->flush();
                        $this->logger->info('Modification of a Budget', [
                            "authorEmail" => $_SESSION["user"]->getEmail(),
                            "oldData" => [
                                "Amount" => $currentbudget->getAmount(),
                            ],
                            "newData" => [
                                "Amount" => $newbudget->getAmount(),
                                "Modification reason" => $this->getpost("reason")
                            ]]);
                        $this->setMessage('success', 'Modification correctement éffectuée');
                        header("Location:" . Config::RACINE . "/Finances/list");
                    }
                }
            } catch (\Exception $e) {
                var_dump($e->getMessage());
                //View::renderTemplate('404.html');
            }

        }
    }


    public function listEntryBillAction()
    {
        if (!isset($_SESSION["user"])) {
            header("Location:" . Config::RACINE . "/");
        } else {
            try {
                View::renderTemplate('Finances/listEntryBill.html', ['user' => $_SESSION["user"], 'entrybills' => $this->getbillforview('\EntryBill')]);
            } catch (\Exception $e) {
                View::renderTemplate('404.html', ['message' => $e->getMessage()]);
            }
        }
    }

    public function listExitBillAction()
    {
        if (!isset($_SESSION["user"])) {
            header("Location:" . Config::RACINE . "/");
        } else {
            try {
                View::renderTemplate('Finances/listExitBill.html', ['user' => $_SESSION["user"], 'exitbills' => $this->getbillforview('\ExitBill')]);
            } catch (\Exception $e) {
                View::renderTemplate('404.html', ['message' => $e->getMessage()]);
            }
        }
    }

    public function entryprintAction($other, $id){
        if (!isset($_SESSION['user'])) {
            header("Location:" . Config::RACINE . "/");
        } elseif ($id == null) {
            View::renderTemplate('Finances/listEntryBill.html', ['user' => $_SESSION["user"], 'entrybills' => $this->getbillforview('\EntryBill'),'error'=>'erreur veuillez réessayer']);
        } else {
            try {
            $entrybill = $this->db->getRepository('App\Models\EntryBill')->find($id);
            $pdf = new EntryBill(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            $user = $this->db->getRepository('App\Models\Director')->find($entrybill->getUserId());
            if ($entrybill->getEntry()->getType()=="apport actionnaire"){
                $pdf->setGiver($this->db->getRepository('App\Models\Shareholder')->find($entrybill->getEntry()->getContributorId()));
            }
            else{
                $pdf->setGiver($this->db->getRepository('App\Models\Contributor')->find($entrybill->getEntry()->getContributorId()));
            }
                $pdf->setEntry($entrybill->getEntry());
                $pdf->setId($entrybill->getId());
                $pdf->setUser($user);
                $pdf->setCreationDate($entrybill->getCreationDate());
                $pdf->init();
                $pdf->writeData();
                $pdf->printBill();

            }
            catch (\Exception $e){
                View::renderTemplate('Finances/listEntryBill.html', ['user' => $_SESSION["user"], 'entrybills' => $this->getbillforview('\EntryBill'),'error'=>'erreur veuillez réessayer']);
            }
        }
    }

    public function exitprintAction($other, $id){
        if (!isset($_SESSION['user'])) {
            header("Location:" . Config::RACINE . "/");
        } elseif ($id == null) {
            View::renderTemplate('Finances/listExitBill.html', ['user' => $_SESSION["user"], 'exitbills' => $this->getbillforview('\ExitBill'),'error'=>'erreur veuillez réessayer']);
        } else {
            try {
                $exitbill = $this->db->getRepository('App\Models\ExitBill')->find($id);
                $pdf = new ExitBill(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

                $user = $this->db->getRepository('App\Models\Director')->find($exitbill->getUserId());
                $files = $this->db->getRepository('App\Models\File')->findBy(array('exit'=>$exitbill->getExit()));
                $links="";
                $server=$_SERVER['SERVER_NAME'];
                if (count($files)>0){
                    for($i=0,$m=count($files);$i<$m;$i++){
                        $links.='<a href="http://'.$this->getbase().Config::FileRacine.$files[$i]->getName().'" download="'.$files[$i]->getOriginName().'" target="_blank">'.$files[$i]->getOriginName().'</a> ';
                    }
                }
                else{
                    $links="aucune";
                }
                $pdf->setExit($exitbill->getExit());
                $pdf->setId($exitbill->getId());
                $pdf->setUser($user);
                $pdf->setLinks($links);
                $pdf->setCreationDate($exitbill->getCreationDate());
                $pdf->init();
                $pdf->writeData();
                $pdf->printBill();

            }
            catch (\Exception $e){
                View::renderTemplate('Finances/listExitBill.html', ['user' => $_SESSION["user"], 'exitbills' => $this->getbillforview('\ExitBill'),'error'=>'erreur veuillez réessayer']);
            }
        }
    }



    public function historyAction($other,$id){
        if (!isset($_SESSION['user'])) {
            header("Location:" . Config::RACINE . "/");
        } elseif ($id == null) {
            $this->setMessage('error', 'une erreur s\'est produite');
            header("Location:" . Config::RACINE . "/Finances/list");
        } else {
            try {
                $list = $this->db->getRepository('App\Models\BudgetingUpdate')->findBy(array('budgeting' => $id),array('movementDate'=>'DESC'));
                View::renderTemplate('Finances/history.html', ['user' => $_SESSION["user"], 'budgetupdates' => $list]);
            }
            catch (\Exception $e){
                var_dump($e->getMessage());
            }
        }
    }

    public function summaryAction(){
        if (!isset($_SESSION['user'])) {
            header("Location:" . Config::RACINE . "/");
        } else {
            try {
                /*code here*/
            }
            catch (\Exception $e){
                var_dump($e->getMessage());
            }
        }
    }

    public function filelistAction(){
        if (!isset($_SESSION['user'])) {
            header("Location:" . Config::RACINE . "/");
        } else {
            try {
                /*code here*/
            }
            catch (\Exception $e){
                var_dump($e->getMessage());
            }
        }
    }
    /* ================================================================================================================*/


    /*this function return bill information for the view*/
    private function getbillforview($type){
        $vrac = $this->db->getRepository('App\Models'.$type)->findBy(array(),array('creation_date'=>'DESC'));

        if($type=='\ExitBill') {
            return array_map(function ($v) {
                return array('id' => $v->getId(),
                 'creationDate' => $v->getCreationDate()->format('Y-m-d H:i:s'),
                 'project'=>$v->getExit()->getProject());
            }, $vrac);
        }
        else{
            return array_map(function ($v) {
                return array(
                    'id' => $v->getId(),
                    'creationDate' => $v->getCreationDate()->format('Y-m-d H:i:s'),
                    'contributorID'=> $v->getEntry()->getContributorID());
            }, $vrac);
        }
    }

    /*this function return all project that doesn't have a budget assigned*/
    private function projectsWithoutBudget()
    {
        $projects = $this->db->getRepository('App\Models\Project')->findAll();
        return array_filter($projects, function ($v) {
            return $v->getBudget() == null;
        });
    }

    /*return all the capital that can't be use for a new budget*/
    private function getAvailableTreasury()
    {

        $query = $this->db->createQuery("SELECT SUM(b.amount) FROM App\Models\Budgeting b");
        $capital = $this->db->getRepository('App\Models\CapitalUpdate')->findOneBy(array(), array('id' => 'desc'))->getAmountafter();
        $donation = $this->db->getRepository('App\Models\DonationUpdate')->findOneBy(array(), array('id' => 'desc'))->getAmountafter();
        return $capital + $donation - $query->getSingleScalarResult();
    }

    /*get the remaining amount of a budget */
    private function getAvailableBudget($id)
    {
        $query = $this->db->createQuery("SELECT SUM(e.amount) FROM App\Models\FinancialExit e WHERE e.budgeting = ?1");
        $query->setParameter(1, $id);
        return $this->getSingleBudget($id)->getAmount() - $query->getSingleScalarResult();
    }

    /*return all budgets*/
    private function getBudgeting()
    {
        return $this->db->getRepository('App\Models\Budgeting')->findAll();
    }

    /*get a single budget by id*/
    private function getSingleBudget($id)
    {
        return $this->db->getRepository('App\Models\Budgeting')->findOneBy(array('id' => $id));
    }

    /*get a single shareholder by id*/
    private function getSingleShareholder($id)
    {
        return $this->db->getRepository('App\Models\Shareholder')->findOneBy(array('id' => $id));
    }

    /*set the new account update by the type of account*/
    private function setAccountUpdate(&$newupdate, $type)
    {
        $previousupdate = $this->db->getRepository('App\Models' . $type)->findOneBy(array(), array('id' => 'desc'));
        $newupdate->setAmountbefore($previousupdate->getAmountafter());
        $newupdate->setAmount($this->getpost("amount"));
        $newupdate->setAmountafter($this->getpost("amount") + $previousupdate->getAmountafter());
    }

    /*this function update shares percentage of a shareholder*/
    private function updateSharesPercentage()
    {

        $shareholders = $this->db->getRepository('App\Models\Shareholder')->findAll();
        $capital = $this->db->getRepository('App\Models\CapitalUpdate')->findOneBy(array(), array('id' => 'desc'))->getAmountafter();

        foreach ($shareholders as $shareholder) {
            $query = $this->db->createQuery("SELECT SUM(f.amount) FROM App\Models\FinancialEntry f WHERE f.type=?1 AND f.contributorID=?2");
            $query->setParameter(1, "apport actionnaire");
            $query->setParameter(2, $shareholder->getId());
            $shareholderpart = $query->getSingleScalarResult();
            $shareholder->setSharesPercentage(($shareholderpart / $capital) * 100);
            $this->db->persist($shareholder);
        }

        $this->db->flush();
    }

    private function randomkey(){
        return sha1(microtime(TRUE)*1000000);
    }

    private function savefile($exit){
        $total = count($_FILES['file']['name']);
        for( $i=0 ; $i < $total ; $i++ ) {
            $fichier=$this->randomkey().strrchr($_FILES['file']['name'][$i], '.');

            $files[$i] = new File();
            $files[$i]->setOriginName($_FILES['file']['name'][$i]);
            $files[$i]->setName($fichier);
            $files[$i]->setExit($exit);
            $files[$i]->setUploadDate();

            move_uploaded_file($_FILES['file']['tmp_name'][$i],'../files/' . $fichier);

            $this->db->persist( $files[$i]);
        }
        $this->db->flush();
    }

    private function testFile(){
        $total = count($_FILES['file']['name']);
        $error = false;

        if($_FILES['file']['name'][0]!=""){
        $extensions = array('.png','.gif','.jpg','.jpeg','.pdf','.docx','.txt','.doc','.ppt','.odt');
            for( $i=0 ; $i < $total ; $i++ ) {
                $extension =strrchr($_FILES['file']['name'][$i], '.');
                $taille = filesize($_FILES['file']['tmp_name'][$i]);

                if(!in_array($extension,$extensions) || $taille>5000000){
                    $error=true; break;
                }
            }
        }
        return $error;
    }

    public function testAction(){
        echo $this->getbase();
    }

    private function getbase(){
        return $_SERVER['SERVER_ADDR'];
    }
}