<?php
/**
 * Created by PhpStorm.
 * User: francis
 * Date: 27/07/18
 * Time: 02:10
 */

namespace App\Models;


/**
 * @Entity
 * @Table(name="budgeting")
 */
class Budgeting extends FinancialMovement
{

    /** @Column(type="integer") */
    protected $projectId;

    /**
     * @return mixed
     */
    public function getProjectId()
    {
        return $this->projectId;
    }

    /**
     * @param mixed $projectId
     */
    public function setProjectId($projectId)
    {
        $this->projectId = $projectId;
    }



}