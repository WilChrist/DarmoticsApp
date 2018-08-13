<?php
/**
 * Created by PhpStorm.
 * User: francis
 * Date: 27/07/18
 * Time: 01:53
 */

namespace App\Models;

/**
 * @Table(name="financialMovement")
 */
class FinancialMovement
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /** @Column(type="float",options={"default"=0}) */
    protected $amount;

    /** @Column(type="datetime",options={"default"="CURRENT_TIMESTAMP"}) */
    protected $movementDate;

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
    public function getMovementDate()
    {
        return $this->movementDate;
    }

    /**
     * @param mixed $movementDate
     */
    public function setMovementDate()
    {
        $this->movementDate = new \DateTime("now");
    }


}