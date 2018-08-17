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
     * Many FinancialExit are related to One Budgeting.
     * @ManyToOne(targetEntity="Budgeting", inversedBy="financialExit")
     * @JoinColumn(name="budgeting_id", referencedColumnName="id")
     */
    protected $budgeting;


    /**
     * One entryBill is for One entry.
     * @OneToOne(targetEntity="ExitBill", mappedBy="exit")
     */
    protected $exitbill;


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

    /**
     * @return mixed
     */
    public function getBudgeting()
    {
        return $this->budgeting;
    }

    /**
     * @param mixed $budgeting
     */
    public function setBudgeting($budgeting)
    {
        $this->budgeting = $budgeting;
    }

    /**
     * @return mixed
     */
    public function getExitbill()
    {
        return $this->exitbill;
    }

    /**
     * @param mixed $exitbill
     */
    public function setExitbill($exitbill)
    {
        $this->exitbill = $exitbill;
    }



}