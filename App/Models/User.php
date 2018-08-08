<?php

namespace App\Models;


/**
 * @Table(name="user",uniqueConstraints={@UniqueConstraint(name="email_unique",columns={"email"})})
 */
class User
{

    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /** @Column(length=255,nullable=true) */
    protected $first_name;

    /** @Column(length=255) */
    protected $last_name;

    /** @Column(length=255) */
    protected $email;

    /** @Column(length=255)*/
    protected $password;

    /** @Column(length=255,nullable=true) */
    protected $phone;

    /** @Column(type="datetime",options={"default" : "CURRENT_TIMESTAMP"}) */
    protected $signUpDate;

    /** @Column(type="datetime",options={"default":"CURRENT_TIMESTAMP"}) */
    protected $lastUpdateDate;

    /** @Column(length=255,nullable=true) */
    protected $address;


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
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * @param mixed $first_name
     */
    public function setFirstName($first_name)
    {
        $this->first_name = $first_name;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * @param mixed $last_name
     */
    public function setLastName($last_name)
    {
        $this->last_name = $last_name;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return mixed
     */
    public function getSignUpDate()
    {
        return $this->signUpDate;
    }

    /**
     * @param mixed $signUpDate
     */
    public function setSignUpDate()
    {
        $this->signUpDate = new \DateTime("now");
    }

    /**
     * @return mixed
     */
    public function getLastUpdateDate()
    {
        return $this->lastUpdateDate;
    }

    /**
     * @param mixed $lastUpdateDate
     */
    public function setLastUpdateDate()
    {
        $this->lastUpdateDate = new \DateTime("now");
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    public function __construct()
    {
        $this->setLastUpdateDate();
        $this->setSignUpDate();
    }
}
