<?php
/**
 * Created by PhpStorm.
 * User: francis
 * Date: 27/07/18
 * Time: 00:38
 */

namespace App\Models;

/**
 * @Entity
 * @Table(name="director",uniqueConstraints={@UniqueConstraint(name="email_unique",columns={"email"})})
 */
class Director extends  User
{
    /** @Column(length=255,nullable=true) */
    protected $department;

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
}