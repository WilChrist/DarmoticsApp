<?php
/**
 * Created by PhpStorm.
 * User: francis
 * Date: 10/08/18
 * Time: 22:22
 */

namespace App\Models;

/**
 * @Entity
 * @Table(name="accountupdate")
 */
class AccountUpdate
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /** @Column(type="float")  */
    protected $amountbefore;

    /** @Column(type="float")  */
    protected $amount;

    /** @Column(type="float")  */
    protected $amountafter;

    /** @Column(type="datetime",options={"default":"CURRENT_TIMESTAMP"}) */
    protected $date;

    /**
     * @return mixed
     */
    public function getAmountbefore()
    {
        return $this->amountbefore;
    }

    /**
     * @param mixed $amountbefore
     */
    public function setAmountbefore($amountbefore)
    {
        $this->amountbefore = $amountbefore;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param mixed $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return mixed
     */
    public function getAmountafter()
    {
        return $this->amountafter;
    }

    /**
     * @param mixed $amountafter
     */
    public function setAmountafter($amountafter)
    {
        $this->amountafter = $amountafter;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate()
    {
        $this->date = new \DateTime("now");
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



    public function __construct()
    {
        $this->setDate();
    }
}