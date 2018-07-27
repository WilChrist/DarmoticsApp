<?php
/**
 * Created by PhpStorm.
 * User: francis
 * Date: 27/07/18
 * Time: 00:41
 */

namespace App\Models;

/**
 * @Entity
 * @Table(name="administrativeChief",uniqueConstraints={@UniqueConstraint(name="email_unique",columns={"email"})})
 */
class AdministrativeChief extends User
{
    /** @Column(length=255,nullable=true) */
    protected $unit;

    /**
     * @return mixed
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @param mixed $unit
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;
    }
}