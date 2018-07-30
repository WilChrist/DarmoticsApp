<?php
/**
 * Created by PhpStorm.
 * User: francis
 * Date: 27/07/18
 * Time: 01:59
 */

namespace App\Models;

/**
 * @Entity
 * @Table(name="financialEntry")
 */
class FinancialEntry extends FinancialMovement
{
    /** @Column(length=255) */
    protected $type;

    /** @Column(type="integer") */
    protected $contributorID;

    /** @Column(type="integer") */
    protected $documentID;

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getContributorID()
    {
        return $this->contributorID;
    }

    /**
     * @param mixed $contributorID
     */
    public function setContributorID($contributorID)
    {
        $this->contributorID = $contributorID;
    }

    /**
     * @return mixed
     */
    public function getDocumentID()
    {
        return $this->documentID;
    }

    /**
     * @param mixed $documentID
     */
    public function setDocumentID($documentID)
    {
        $this->documentID = $documentID;
    }


}