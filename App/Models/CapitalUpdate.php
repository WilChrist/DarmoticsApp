<?php
/**
 * Created by PhpStorm.
 * User: francis
 * Date: 10/08/18
 * Time: 22:22
 */

namespace App\Models;

/**
 * @Entity
 * @Table(name="capitalupdate")
 */
class CapitalUpdate extends Account
{
    public function __construct()
    {
        parent::__construct();
    }
}