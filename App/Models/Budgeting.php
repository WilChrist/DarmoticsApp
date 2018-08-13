<?php
/**
 * Created by PhpStorm.
 * User: francis
 * Date: 27/07/18
 * Time: 02:10
 */

namespace App\Models;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @Table(name="budgeting")
 */
class Budgeting extends FinancialMovement
{

    public function __construct() {
        $this->financialexit = new ArrayCollection();
    }

    /**
     * One Project has One Budget.
     * @OneToOne(targetEntity="Project", inversedBy="budget")
     * @JoinColumn(name="project_id", referencedColumnName="id")
     */
    protected $project;


    /**
     * One Budgeting is related to many FinancialExit.
     * @OneToMany(targetEntity="FinancialExit", mappedBy="budgeting")
     */
    protected $financialExit;


    /**
     * @return mixed
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param mixed $project
     */
    public function setProject($project)
    {
        $this->project = $project;
    }

    /**
     * @return mixed
     */
    public function getFinancialExit()
    {
        return $this->financialexit;
    }

    /**
     * @param mixed $financialexit
     */
    public function setFinancialExit($financialExit)
    {
        $this->financialExit = $financialExit;
    }

}