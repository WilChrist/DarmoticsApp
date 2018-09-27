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
        $this->budgetingUpdate = new ArrayCollection();
    }

    /**
     * One Project has One Budget.
     * @OneToOne(targetEntity="Project", inversedBy="budget")
     * @JoinColumn(name="project_id", referencedColumnName="id")
     */
    protected $project;

    /** @Column(type="float",options={"default"=0}) */
    protected $origin_amount;

    /** @Column(type="float",options={"default"=0}) */
    protected $used_part;

    /** @Column(type="float",options={"default"=0}) */
    protected $rest;

    /**
     * One Budgeting is related to many FinancialExit.
     * @OneToMany(targetEntity="FinancialExit", mappedBy="budgeting")
     */
    protected $financialExit;

    /**
     * One Budgeting is related to many FinancialExit.
     * @OneToMany(targetEntity="BudgetingUpdate", mappedBy="budgeting")
     */
    protected $budgetingUpdate;

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

    /**
     * @return mixed
     */
    public function getOriginAmount()
    {
        return $this->origin_amount;
    }

    /**
     * @param mixed $origin_amount
     */
    public function setOriginAmount($origin_amount)
    {
        $this->origin_amount = $origin_amount;
    }

    /**
     * @return mixed
     */
    public function getBudgetingUpdate()
    {
        return $this->budgetingUpdate;
    }

    /**
     * @param mixed $budgetingUpdate
     */
    public function setBudgetingUpdate($budgetingUpdate)
    {
        $this->budgetingUpdate = $budgetingUpdate;
    }

    /**
     * @return mixed
     */
    public function getUsedPart()
    {
        return $this->used_part;
    }

    /**
     * @param mixed $used_part
     */
    public function setUsedPart($used_part)
    {
        $this->used_part = $used_part;
    }

    /**
     * @return mixed
     */
    public function getRest()
    {
        return $this->rest;
    }

    /**
     * @param mixed $rest
     */
    public function setRest($rest)
    {
        $this->rest = $rest;
    }


}