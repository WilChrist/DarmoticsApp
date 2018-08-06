<?php
/**
 * Created by PhpStorm.
 * User: francis
 * Date: 06/08/18
 * Time: 21:24
 */

namespace App\Models;

/**
 * @Entity
 * @Table(name="project")
 */
class Project
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id ;


}