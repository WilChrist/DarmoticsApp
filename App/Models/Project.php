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

    /** @Column(length=255,unique=true) */
    protected $name;

    /** @Column(type="text") */
    protected $description;

    /** @Column(type="datetime") */
    protected $startDate;

    /** @Column(type="datetime") */
    protected $endDate;


    /**
     * One Budgeting is for One Project.
     * @OneToOne(targetEntity="Budgeting", mappedBy="project")
     */
    protected $budget;

    /** @Column(length=255, options={"default":"created"}) */
    protected $state;

    /**
     * One Project has Many Employee_Project.
     * @OneToMany(targetEntity="Employee_Project", mappedBy="project", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $employee_project;

    /** @Column(type="integer",nullable=true) */
    protected $chief_id;

    /**
     * One Project has One Chief(Employee).
     * @ManyToOne(targetEntity="Employee")
     * @JoinColumn(name="chief_id", referencedColumnName="id")
     */
    protected $chief;

    /**
     * @return mixed
     */
    public function getChief()
    {
        return $this->chief;
    }

    /**
     * @param mixed $chief
     */
    public function setChief($chief)
    {
        $this->chief = $chief;
    }

    /**
     * @return mixed
     */
    public function getChiefId()
    {
        return $this->chief_id;
    }

    /**
     * @param mixed $chief_id
     */
    public function setChiefId($chief_id)
    {
        $this->chief_id = $chief_id;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param mixed $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }



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
    public static function states(){
        return [
            "created" => ["Créé","light"],
            "notstarted" => ["Non Démarré","dark"],
            "started" => ["Démarré","primary"],
            "running" => ["En Cours","warning"],
            "stoped" => ["Stoppé","danger"],
            "finished" => ["Terminé","success"],
            "canceled" => ["Annulé","info"],
        ];
    }

    /**
     * @param Employee_Project $employeeProject
     */
    public function removeEmployeeProject(Employee_Project $employeeProject)
    {
        if (!$this->employee_project->contains($employeeProject)) {
            echo'notgood';
            return false;
        }
        $this->employee_project->remove($employeeProject);
        return true;
    }
    /**
     * @param Employee_Project[] $employeeProjects
     */
    public function removeEmployeeProjects($employeeProjects)
    {
        try{
            foreach ($employeeProjects as $ep){
                $this->removeEmployeeProject($ep);
            }
        }catch (\Exception $exe){
            var_dump($exe);
        }
        return true;
    }
}