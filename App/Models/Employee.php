<?php
/**
 * Created by PhpStorm.
 * User: alexi
 * Date: 07/08/2018
 * Time: 13:46
 */

namespace App\Models;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Tests\Models\Reflection\ParentClass;

/**
 * @Entity
 * @Table(name="employee",uniqueConstraints={@UniqueConstraint(name="email_unique",columns={"email"})})
 */
class Employee extends User
{
    /** @Column(length=255,nullable=true) */
    protected $education;

    /** @Column(type="datetime",options={"default":"CURRENT_TIMESTAMP"}) */
    protected $dateOfEntry;

    /** @Column(length=255,nullable=false) */
    protected $office;

    /** @Column(length=255,nullable=true) */
    protected $skills;


    /**
     * One Employee has One Department.
     * @ManyToOne(targetEntity="Department", inversedBy="employees")
     * @JoinColumn(name="department_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $department;

    /**
     * One Employee has Many Employee_Project.
     * @OneToMany(targetEntity="Employee_Project", mappedBy="employee")
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
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * @param mixed $department
     */
    public function setDepartment($department)
    {
        $this->department = $department;
    }




    /**
     * @return mixed
     */
    public function getOffice()
    {
        return $this->office;
    }

    /**
     * @param mixed $office
     */
    public function setOffice($office)
    {
        $this->office = $office;
    }

    /**
     * @return mixed
     */
    public function getSkills()
    {
        return $this->skills;
    }

    /**
     * @param mixed $skills
     */
    public function setSkills($skills)
    {
        $this->skills = $skills;
    }

    /**
     * @return mixed
     */
    public function getEducation()
    {
        return $this->education;
    }

    /**
     * @param mixed $education
     */
    public function setEducation($education)
    {
        $this->education = $education;
    }

    /**
     * @return mixed
     */
    public function getDateOfEntry()
    {
        return $this->dateOfEntry;
    }

    /**
     * @param mixed $dateOfEntry
     */
    public function setDateOfEntry($dateOfEntry)
    {
        $this->dateOfEntry = $dateOfEntry;
    }


    /**
     * Employee constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->employee_project = new ArrayCollection();
    }
}