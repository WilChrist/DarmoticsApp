<?php
/**
 * Created by PhpStorm.
 * User: alexi
 * Date: 10/08/2018
 * Time: 13:32
 */

namespace App\Models;

/** @Entity
 * @Table(name="employee_project",uniqueConstraints={@UniqueConstraint(name="employee_project_unique",columns={"employee_id", "project_id"})})
 */
class Employee_Project
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

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
    public function getAffectionDate()
    {
        return $this->affectionDate;
    }

    /**
     * @param mixed $affectionDate
     */
    public function setAffectionDate($affectionDate)
    {
        $this->affectionDate = $affectionDate;
    }

    /**
     * @return mixed
     */
    public function getEmployee()
    {
        return $this->employee;
    }

    /**
     * @param mixed $employee
     */
    public function setEmployee($employee)
    {
        $this->employee = $employee;
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

    /** @Column(type="datetime",options={"default" : "CURRENT_TIMESTAMP"}) */
    protected $affectionDate;

    /**
     * @ManyToOne(targetEntity="Employee",inversedBy="employee_project")
     * @JoinColumn(name="employee_id", referencedColumnName="id",onDelete="CASCADE")
     */
    protected $employee;

    /**
     * @ManyToOne(targetEntity="Project",inversedBy="employee_project")
     * @JoinColumn(name="project_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $project;

}