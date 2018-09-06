<?php
/**
 * Created by PhpStorm.
 * User: alexi
 * Date: 07/08/2018
 * Time: 14:03
 */

namespace App\Models;

/**
 * @Entity
 * @Table(name="department")
 */
class Department
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /** @Column(length=255) */
    protected $name;

    /** @Column(length=255,nullable=true) */
    protected $description;

    /** @Column(type="datetime",options={"default":"CURRENT_TIMESTAMP"}) */
    protected $creationDate;

    /** @Column(type="datetime",options={"default":"CURRENT_TIMESTAMP"}) */
    protected $lastUpdateDate;

    /** @Column(type="integer",nullable=true) */
    protected $chief_id;

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
    public function getEmployees()
    {
        return $this->employees;
    }

    /**
     * @param mixed $employees
     */
    public function setEmployees($employees)
    {
        $this->employees = $employees;
    }

    /**
     * One Department has One Chief(Employee).
     * @OneToOne(targetEntity="Employee")
     * @JoinColumn(name="chief_id", referencedColumnName="id")
     */
    protected $chief;

    /**
     * One Department has Many Employee.
     * @OneToMany(targetEntity="Employee", mappedBy="department")
     * @JoinColumn(name="employee_id", referencedColumnName="id")
     */
    protected $employees;

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
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * @param mixed $creationDate
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;
    }

    /**
     * @return mixed
     */
    public function getLastUpdateDate()
    {
        return $this->lastUpdateDate;
    }

    /**
     * @param mixed $lastUpdateDate
     */
    public function setLastUpdateDate($lastUpdateDate)
    {
        $this->lastUpdateDate = $lastUpdateDate;
    }

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


    /*INSERT INTO `director` (`id`, `department`, `first_name`, `last_name`, `email`, `password`, `phone`, `signUpDate`, `lastUpdateDate`, `address`) VALUES (NULL, NULL, NULL, 'admin', 'admin@darmotics.ma', '2dbc2fd2358e1ea1b7a6bc08ea647b9a337ac92d', NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, NULL);*/

}