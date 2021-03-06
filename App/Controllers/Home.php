<?php

namespace App\Controllers;

use App\Config;
use Core\Controller;
use Core\View;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Home extends Controller
{

    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
        if (!isset($_SESSION['user'])) {
            View::renderTemplate('Home/index.html');
        }else{
            header("Location:".Config::RACINE."/Home/dashboard");
        }
    }

    /**
     * Show the login page
     *
     * @return void
     */
    public function loginAction()
    {
        $directorRepository = $this->db->getRepository('App\Models\Director');

        $user = $directorRepository->findOneBy(array("email"=>$this->getpost("email"),"password"=> sha1($this->getpost("password"))));

        if($user!==null){
            $_SESSION["user"]=$user;
            $this->logger->info( 'Login',["email"=>$user->getEmail()]);
            header("Location:".Config::RACINE."/Home/dashboard");
        }
        else{
            View::renderTemplate('Home/index.html',["error"=>"email ou mot de passe incorrecte"]);

        }
    }

    public function dashboardAction()
    {
        if (!isset($_SESSION['user'])) {
            header("Location:".Config::RACINE."/");

        }else{
            $numberOfShareholders=$this->getNumberOf('App\Models\Shareholder');
            $numberOfDepartments=$this->getNumberOf('App\Models\Department');
            $numberOfEmployees=$this->getNumberOf('App\Models\Employee');
            $numberOfProjects=$this->getNumberOf('App\Models\Project');

            View::renderTemplate('Home/dashboard.html',
                [
                    "numberOfShareholders"=>$numberOfShareholders,
                    "numberOfDepartments"=>$numberOfDepartments,
                    "numberOfEmployees"=>$numberOfEmployees,
                    "numberOfProjects"=>$numberOfProjects,
                    "budgets"=> $this->db->getRepository('App\Models\Budgeting')->findAll(),
                    "apportA"=> $this->db->getRepository('App\Models\CapitalUpdate')->findOneBy(array(), array('id' => 'desc'))->getAmountafter(),
                    "don"=>$this->db->getRepository('App\Models\DonationUpdate')->findOneBy(array(), array('id' => 'desc'))->getAmountafter()
                ]);
        }
    }

    public function chartDataAction(){
        if (!isset($_SESSION['user'])) {
            header("Location:".Config::RACINE."/");

        }else{

            $apportA = $this->db->getRepository('App\Models\CapitalUpdate')->findOneBy(array(), array('id' => 'desc'))->getAmountafter();
            $don = $this->db->getRepository('App\Models\DonationUpdate')->findOneBy(array(), array('id' => 'desc'))->getAmountafter();
            $total =$apportA+$don;

            
            $query = $this->db->createQuery("SELECT SUM(e.amount) FROM App\Models\Budgeting e ");
            $totalAllocation=$query->getSingleScalarResult();

            if($total!=null && $total!=0){

                $treasuryData = array(
                    'apports Actionnaires'=> ($apportA/$total)*100,
                    'dons'=> ($don/$total)*100
                );

                $allocationData = array(
                    'capital alloué'=>$totalAllocation,
                    'capital non alloué'=>$total-$totalAllocation
                );

            }else {
                $treasuryData = array(
                    'apports Actionnaires' => 0,
                    'dons' => 0
                );

                $allocationData = array(
                    'capital alloué'=>0,
                    'capital non alloué'=>0
                );
            }

            $capitalData=array();
            $shareholders = $this->db->getRepository('App\Models\Shareholder')->findAll();
            foreach ($shareholders as $shareholder){
                $capitalData[$shareholder->getLastName()]=$shareholder->getSharesPercentage();
            }



            echo json_encode(array('treasuryData'=>$treasuryData,'capitalData'=>$capitalData,'allocationData'=>$allocationData));
        }
    }


    public function logoutAction(){
        session_destroy();
        $this->logger->info('Logout',["email"=>$_SESSION["user"]->getEmail()]);
        header("Location:".Config::RACINE."/Home");
    }

    private function getNumberOf($objet){

        $query= $this->db->createQuery("SELECT COUNT(o.id) FROM ".$objet." o");
        //print("<pre>" . print_r($query, true) . "</pre>");
        return $query->getSingleScalarResult();
    }
}
