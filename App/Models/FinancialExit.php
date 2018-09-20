<?php
/**
 * Created by PhpStorm.
 * User: francis
 * Date: 27/07/18
 * Time: 02:06
 */

namespace App\Models;

use Doctrine\Common\Collections\ArrayCollection;


/**
 * @Entity
 * @Table(name="financialExit")
 */
class FinancialExit extends FinancialMovement
{

    public function __construct() {
        $this->files= new ArrayCollection();
    }

    /** @Column(length=255) */
    protected $reason;

    /**
     * Many FinancialExit are related to One Budgeting.
     * @ManyToOne(targetEntity="Budgeting", inversedBy="financialExit")
     * @JoinColumn(name="budgeting_id", referencedColumnName="id")
     */
    protected $budgeting;

    /** @Column(length=255,nullable=true) */
    protected $project;

    /**
     * One exitBill is for One exit.
     * @OneToOne(targetEntity="ExitBill", mappedBy="exit")
     */
    protected $exitbill;

    /**
     * One FinancialExit is related to many File.
     * @OneToMany(targetEntity="File", mappedBy="exit")
     */
    protected $files;

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

    /**
     * @return mixed
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param mixed $project
     */
    public function setProject($project)
    {
        $this->project = $project;
    }

    /**
     * @return mixed
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @param mixed $files
     */
    public function setFiles($files)
    {
        $this->files = $files;
    }



}