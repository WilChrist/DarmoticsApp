<?php
/**
 * Created by PhpStorm.
 * User: francis
 * Date: 27/07/18
 * Time: 02:21
 */

namespace App\Models;


/**
 * @Entity
 * @Table(name="account")
 */
class Account
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id ;

    /** @Column(type="float")  */
    protected $amount;

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


}