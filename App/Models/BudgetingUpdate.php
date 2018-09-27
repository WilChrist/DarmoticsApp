<?php
/**
 * Created by PhpStorm.
 * User: francis
 * Date: 18/09/18
 * Time: 13:20
 */

namespace App\Models;

/**
 * @Entity
 * @Table(name="budgetingUpdate")
 */
class BudgetingUpdate extends FinancialMovement
{
    /** @Column(type="float",options={"default"=0}) */
    protected $amount_before;

    /** @Column(type="float",options={"default"=0}) */
    protected $amount_after;

    /**
     * Many BudgetingUpdate are related to One Budgeting.
     * @ManyToOne(targetEntity="Budgeting", inversedBy="budgetingUpdate")
     * @JoinColumn(name="budgeting_id", referencedColumnName="id")
     */
    protected $budgeting;

    /**
     * @return mixed
     */
    public function getAmountBefore()
    {
        return $this->amount_before;
    }

    /**
     * @param mixed $amount_before
     */
    public function setAmountBefore($amount_before)
    {
        $this->amount_before = $amount_before;
    }

    /**
     * @return mixed
     */
    public function getAmountAfter()
    {
        return $this->amount_after;
    }

    /**
     * @param mixed $amount_after
     */
    public function setAmountAfter($amount_after)
    {
        $this->amount_after = $amount_after;
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


}