<?php
/**
 * Created by PhpStorm.
 * User: francis
 * Date: 27/07/18
 * Time: 02:06
 */

namespace App\Models;

/**
 * @Entity
 * @Table(name="financialExit")
 */
class FinancialExit extends FinancialMovement
{
    /** @Column(length=255) */
    protected $reason;

    /**
     * @return mixed
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * @param mixed $reason
     */
    public function setReason($reason)
    {
        $this->reason = $reason;
    }



}