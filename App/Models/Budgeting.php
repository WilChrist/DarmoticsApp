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

    /**
     * One Budgeting is for One Project.
     * @OneToOne(targetEntity="Project", mappedBy="budget")
     */
    protected $project;

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



}