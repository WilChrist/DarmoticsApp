<?php
/**
 * Created by PhpStorm.
 * User: francis
 * Date: 20/09/18
 * Time: 19:05
 */

namespace App\Models;

/**
 * @Entity
 * @Table(name="file")
 */
class File
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /** @Column(length=255,nullable=true) */
    protected $origin_name;

    /** @Column(length=255,nullable=true) */
    protected $name;

    /** @Column(type="datetime",options={"default"="CURRENT_TIMESTAMP"}) */
    protected $uploadDate;

    /**
     * Many Files are related to One FinancialExit.
     * @ManyToOne(targetEntity="FinancialExit", inversedBy="files")
     * @JoinColumn(name="exit_id", referencedColumnName="id")
     */
    protected $exit;

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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getUploadDate()
    {
        return $this->uploadDate;
    }

    /**
     * @param mixed $uploadDate
     */
    public function setUploadDate()
    {
        $this->uploadDate = new \DateTime("now");
    }

    /**
     * @return mixed
     */
    public function getExit()
    {
        return $this->exit;
    }

    /**
     * @param mixed $exit
     */
    public function setExit($exit)
    {
        $this->exit = $exit;
    }

    /**
     * @return mixed
     */
    public function getOriginName()
    {
        return $this->origin_name;
    }

    /**
     * @param mixed $origin_name
     */
    public function setOriginName($origin_name)
    {
        $this->origin_name = $origin_name;
    }


}