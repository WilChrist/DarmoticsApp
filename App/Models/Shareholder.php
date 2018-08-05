<?php
/**
 * Created by PhpStorm.
 * User: francis
 * Date: 27/07/18
 * Time: 00:43
 */

namespace App\Models;

/**
 * @Entity
 * @Table(name="shareholder",uniqueConstraints={@UniqueConstraint(name="email_unique",columns={"email"})})
 */
class Shareholder extends User
{
    /** @Column(type="datetime",options={"default" : "CURRENT_TIMESTAMP"}) */
    protected $enterToCapitalDate;

    /** @Column(type="float",options={"default"=0}) */
    protected $sharesPercentage;

    /**
     * @return mixed
     */
    public function getEnterToCapitalDate()
    {
        return $this->enterToCapitalDate;
    }

    /**
     * @param mixed $enterToCapitalDate
     */
    public function setEnterToCapitalDate()
    {
        $this->enterToCapitalDate = new \DateTime("now");
    }

    /**
     * @return mixed
     */
    public function getSharesPercentage()
    {
        return $this->sharesPercentage;
    }

    /**
     * @param mixed $sharesPercentage
     */
    public function setSharesPercentage($sharesPercentage)
    {
        $this->sharesPercentage = $sharesPercentage;
    }

    public function __construct()
    {
        parent::__construct();
        $this->setEnterToCapitalDate();
    }
}