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
     * One Budgeting is for One Project.
     * @OneToOne(targetEntity="Project", mappedBy="budget")
     */
    protected $project;


    /**
     * One Budgeting is related to many FinancialExit.
     * @OneToMany(targetEntity="FinancialExit", mappedBy="budgeting")
     */
    private $financialExit;

    /**
     * @return mixed
     */
    public function getProjectId()
    {
        return $this->projectId;
    }

    /**
     * @param mixed $projectId
     */
    public function setProjectId($projectId)
    {
        $this->projectId = $projectId;
    }

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