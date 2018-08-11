<?php
/**
 * Created by PhpStorm.
 * User: francis
 * Date: 06/08/18
 * Time: 21:24
 */

namespace App\Models;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @Entity
 * @Table(name="project")
 */
class Project
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id ;

    /** @Column(length=255) */
    protected $name;

    /** @Column(type="text") */
    protected $description;

    /** @Column(type="datetime") */
    protected $startDate;

    /** @Column(type="datetime") */
    protected $endDate;


    /**
     * One Project has One Budget.
     * @OneToOne(targetEntity="Budgeting", inversedBy="project")
     * @JoinColumn(name="budget_id", referencedColumnName="id")
     */
    protected $budget;

    /**
     * One Project has Many Employee_Project.
     * @OneToMany(targetEntity="Employee_Project", mappedBy="project")
     */
    protected $employee_project;

    /**
     * @return mixed
     */
    public function getEmployeeProject()
    {
        return $this->employee_project;
    }

    /**
     * @param mixed $employee_project
     */
    public function setEmployeeProject($employee_project)
    {
        $this->employee_project = $employee_project;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param mixed $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return mixed
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param mixed $endDate
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }

    /**
     * @return mixed
     */
    public function getBudget()
    {
        return $this->budget;
    }

    /**
     * @param mixed $budget
     */
    public function setBudget($budget)
    {
        $this->budget = $budget;
    }

    public function __construct() {
        $this->employee_project = new ArrayCollection();
    }
}